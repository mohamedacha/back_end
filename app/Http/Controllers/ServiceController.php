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
        foreach($services as $service){
            $service->img = asset('storage/'.$service->img);
        }
        return response()->json(['data' => $services]);
    }

    public function show(Service $service)
    {
        
        $default = asset('storage/services_imgs/default.jpeg');
        $service->img = $service->img ? asset('storage/' . $service->img) : asset('storage/default.jpeg'); // Ensure default.jpeg is accessible
        return response()->json(["data" => $service , "default_img" => $default]);
    }

    public function store(Request $request)
    {
        $service_validation = $request->validate([
            'type' => 'required|string|max:255',
            'available' => 'required|boolean',
            'description' => 'required|string',
            'img' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $name_img = 'services_imgs/default.jpeg';
        if ($request->hasFile('img')) {
            $name_img = $request->file('img')->store('services_imgs', 'public');
        }

        $service = Service::create([
            'type' => $service_validation['type'],
            'available' => $service_validation['available'],
            'description' => $service_validation['description'],
            'img' => $name_img,
        ]);

        return response()->json(['message' => 'Service created successfully!'], 201);
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
            if ($service->img && Storage::disk('public')->exists($service->img) && $service->img !== 'services_imgs/default.jpeg') {
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
