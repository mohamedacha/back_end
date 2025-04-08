<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Validation\ValidationException;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        foreach ($services as $service) {
            $service->img = asset('storage/' . $service->img);
        }
        return response()->json(['data' => $services]);
    }

    public function show(Service $service)
    {

        $default = asset('storage/services_imgs/default.jpeg');
        $service->img = $service->img ? asset('storage/' . $service->img) : asset('storage/default.jpeg'); // Ensure default.jpeg is accessible
        return response()->json(["data" => $service, "default_img" => $default]);
    }

    public function store(Request $request)
    {
        try {

            $service_validation = $request->validate([
                'service_name' => 'required|string|max:100|min:3|filled',
                'available' => 'required|boolean',
                'description' => 'required|string|max:1000|filled',
                'img' => 'nullable|mimes:png,jpg,jpeg|max:2048'
            ]);

            $name_img = 'services_imgs/default.jpeg';
            if ($request->hasFile('img')) {
                $name_img = $request->file('img')->store('services_imgs', 'public');
            }

            Service::create([
                'service_name' => $service_validation['service_name'],
                'available' => $service_validation['available'],
                'description' => $service_validation['description'],
                'img' => $name_img,
            ]);

            return response()->json(['message' => 'Service created successfully!'], 201);

        } catch (ValidationException $e) {
            return Response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id )
    {
        $service = Service::find($id);
        if(!$service){
            return response()->json(['message'=>'service not found']);
       }
       try{

           $service_validation = $request->validate([
               'service_name' => 'required|string|max:255',
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
        $service->service_name = $service_validation['service_name'] ?? $service->service_name ; 
        $service->available = $service_validation['available'] ?? $service->available ;
        $service->description = $service_validation['description'] ?? $service->description ;
        $service->save() ;


        return response()->json([
            'message' => 'The service '. $id.' was updated successfully',
            'data' => $service
        ]);
    }catch(ValidationException $e){
        return response()->json(["errors" => $e->errors()],422) ;
    }
    }

    public function destroy($id)
    {
        $service = Service::Find($id);
        if (!$service) {
            return response()->json(['message' => 'service not found'], 404);
        }

        $service->delete();
        return response()->json(['message' => 'service deleted succesfuly'], 200);
    }
}
