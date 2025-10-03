<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function __construct(private LocationService $locationService)
    {

    }

    public function index()
    {
        $locations = $this->locationService->listLocations();

        return view('admin.locations.index', [
            'locations' => $locations
        ]);
    }

    public function form()
    {
        $location = new Location();
        return view('admin.locations.form', [
            'location' => $location
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $location = new Location();
        $location->fill($request->all());
        $location->save();

        return redirect()->route('locations.edit', ['id' => $location->id])->with('success', __('modules.location_created'));
    }

    public function update(Request $request, $id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->fill($request->all());
            $location->save();

            return redirect()->route('locations.edit', ['id' => $location->id])
                ->with('success', __('modules.location_updated'));
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar a localização com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao atualizar a localização.'));
        }
    }

    public function edit($id)
    {
        $location = Location::find($id);

        return view('admin.locations.form', [
            'location' => $location
        ]);
    }


}
