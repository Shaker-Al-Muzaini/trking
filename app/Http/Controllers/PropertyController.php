<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class PropertyController extends Controller
{

    public function index()
    {
        $properties = Property::latest()->get();

        return inertia('Property', [
            'properties' => $properties
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'des' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->input('title'),
            'price' => $request->input('price'),
            'des' => $request->input('des'),
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/properties');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['image'] = '/uploads/properties/' . $filename;
        }

        Property::create($data);

        return redirect()->back();
    }

    public function update(Request $request, Property $property)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'des' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->input('title'),
            'price' => $request->input('price'),
            'des' => $request->input('des'),
        ];

        if ($request->hasFile('image')) {
            // حذف الصورة السابقة من السيرفر لتوفير المساحة
            if ($property->image && file_exists(public_path($property->image))) {
                @unlink(public_path($property->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/properties');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['image'] = '/uploads/properties/' . $filename;
        }

        $property->update($data);

        return redirect()->back();
    }


}
