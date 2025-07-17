<?php

namespace App\Services\Stock;

use App\Models\ProductStock;
use App\Models\ProductBusinessLocation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowStockAlert;

class StockStreamService
{
    public function handleNewMovement(ProductStock $stock): void
    {
        $pbl = ProductBusinessLocation::find($stock->product_business_location_id);

        if (!$pbl) return;

        $finalStock = $this->calculateFinalStock($pbl);

        if ($finalStock <= 10 && !$this->alreadyNotified($pbl)) {
            $this->createNotification($pbl, $finalStock);
            $this->blastEmail($pbl, $finalStock);
        }
    }

    protected function calculateFinalStock(ProductBusinessLocation $pbl): int
    {
        return $pbl->productStocks()->sum('quantity'); // since quantity is signed
    }

    protected function alreadyNotified(ProductBusinessLocation $pbl): bool
    {
        return Notification::where('notifiable_type', ProductBusinessLocation::class)
            ->where('notifiable_id', $pbl->id)
            ->where('type', 'low_stock')
            ->whereDate('created_at', now()->toDateString())
            ->exists();
    }

    protected function createNotification(ProductBusinessLocation $pbl, int $stock): void
    {
      $notification = Notification::create([
          'notifiable_type' => ProductBusinessLocation::class,
          'notifiable_id' => $pbl->id,
          'type' => 'low_stock',
          'message' => "Stock for {$pbl->product->name} at {$pbl->businessLocation->name} is low ({$stock} left).",
      ]);

      $users = User::all();
      $notification->readers()->attach($users->pluck('id'));
    }

    protected function blastEmail(ProductBusinessLocation $pbl, int $stock): void
    {
        $users = User::all(); // Filter by role or location if needed

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new LowStockAlert($pbl, $stock));
        }
    }
}