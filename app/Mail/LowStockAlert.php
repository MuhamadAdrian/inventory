<?php

namespace App\Mail;

use App\Models\ProductBusinessLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProductBusinessLocation $pbl,
        public int $finalStock
    ) {}

    public function envelope(): Envelope
    {
        $product = $this->pbl->product->name;
        $location = $this->pbl->businessLocation->name;

        return new Envelope(
            subject: "Low Stock Alert: {$product} at {$location}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low_stock_alert',
            with: [
                'product' => $this->pbl->product,
                'location' => $this->pbl->businessLocation,
                'stock' => $this->finalStock,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}