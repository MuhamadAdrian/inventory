<?php

namespace App\Http\Controllers\Admin\BusinessLocation;

use App\Http\Controllers\Admin\AppController;
use App\Http\Requests\BusinessLocationRequest;
use App\Models\BusinessLocation;
use App\Services\BusinessLocation\BusinessLocationService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BusinessLocationController extends AppController
{
    protected BusinessLocationService $businessLocationService;

    public function __construct(Request $request, BusinessLocationService $businessLocationService)
    {
        parent::__construct($request);

        $this->middleware('can:view business location')->only(['index', 'show', 'data']);
        $this->middleware('can:delete business location')->only(['destroy']);
        $this->middleware('can:edit business location')->only(['edit', 'update']);

        $this->businessLocationService = $businessLocationService;

        config([
            'site.header' => 'Location Management',
            'site.breadcrumbs' => [
                ['name' => 'Locations', 'url' => route('business-locations.index')],
            ]
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.business-locations.index');
    }

    public function data(Request $request)
    {
        $businessLocations = $this->businessLocationService->businessLocationQuery();

        if ($request->filled('type_filter') && $request->input('type_filter') !== '') {
            $businessLocations->where('type', $request->input('type_filter'));
        }

        return DataTables::of($businessLocations)
            ->addColumn('action', function ($location) {
                return view('admin.business-locations.template.action', compact('location'));
            })
            ->editColumn('type', function ($location) {
                return ucfirst($location->type);
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.business-locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BusinessLocationRequest $request)
    {
        try{
            $this->businessLocationService->create($request);
            return redirect()->route('business-locations.index')->with('success', __('Lokasi berhasil dibuat.'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => __('Gagal membuat lokasi: ') . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $businessLocation = $this->businessLocationService->businessLocationQuery()->findOrFail($id);
        return view('admin.business-locations.edit', compact('businessLocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BusinessLocationRequest $request, int $id)
    {
        try {
            $this->businessLocationService->update($id, $request);
            return redirect()->route('business-locations.index')->with('success', __('Lokasi berhasil diperbarui.'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => __('Gagal memperbarui lokasi: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->businessLocationService->delete($id);
            return redirect()->route('business-locations.index')->with('success', __('Lokasi berhasil dihapus.'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => __('Gagal menghapus lokasi: ') . $e->getMessage()]);
        }
    }
}
