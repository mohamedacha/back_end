<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return response()->json(['data' => $services]);
    }

    public function show(Service $service)
    {
        return response()->json(['data' => $service]);
    }

    public function store(Request $request)
    {
        $service_validation = $request->validate([
            'type' => 'required|string|max:255',
            'available' => 'required|boolean',
            'description' => 'required|string',
            'img' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $name_img = 'default.png';
        if ($request->hasFile('img')) {
            $name_img = $request->file('img')->store('services_imgs', 'public');
        }

        $service = Service::create([
            'type' => $service_validation['type'],
            'available' => $service_validation['available'],
            'description' => $service_validation['description'],
            'img' => $name_img,
        ]);

        return response()->json(['data' => $service], 201);
    }

    public function update(Request $request, Service $service)
    {
        $service_validation = $request->validate([
            'type' => 'required|string|max:255',
            'available' => 'required|boolean',
            'description' => 'required|string',
            'img' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        if ($request->hasFile('img')) {
            // Delete the old image if it's not the default
            if ($service->img !== 'default.png') {
                Storage::disk('public')->delete($service->img);
            }
            $service->img = $request->file('img')->store('services_imgs', 'public');
        }

        $service->update($service_validation);

        return response()->json([
            'message' => 'The service was updated successfully',
            'data' => $service
        ]);
    }

    public function destroy($id)
    {
        $service = Service::Find($id) ;
        if (!$service) {
            return response()->json(['message' => 'service not found'], 404);
        }

        $service->delete();
        return response()->json(['message' => 'service deleted succesfuly'], 200);
    }
}
