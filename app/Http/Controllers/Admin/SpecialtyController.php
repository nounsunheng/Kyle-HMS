<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of specialties
     */
    public function index()
    {
        $specialties = Specialty::withCount('doctors')->orderBy('name')->paginate(20);
        return view('admin.specialties.index', compact('specialties'));
    }

    /**
     * Show the form for creating a new specialty
     */
    public function create()
    {
        return view('admin.specialties.create');
    }

    /**
     * Store a newly created specialty
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specialties',
            'description' => 'nullable|string',
        ]);

        Specialty::create($request->all());

        return redirect()->route('admin.specialties.index')
            ->with('success', 'Specialty created successfully!');
    }

    /**
     * Show the form for editing the specified specialty
     */
    public function edit(Specialty $specialty)
    {
        return view('admin.specialties.edit', compact('specialty'));
    }

    /**
     * Update the specified specialty
     */
    public function update(Request $request, Specialty $specialty)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name,' . $specialty->id,
            'description' => 'nullable|string',
        ]);

        $specialty->update($request->all());

        return redirect()->route('admin.specialties.index')
            ->with('success', 'Specialty updated successfully!');
    }

    /**
     * Remove the specified specialty
     */
    public function destroy(Specialty $specialty)
    {
        if ($specialty->doctors()->count() > 0) {
            return back()->with('error', 'Cannot delete specialty with assigned doctors.');
        }

        $specialty->delete();

        return redirect()->route('admin.specialties.index')
            ->with('success', 'Specialty deleted successfully!');
    }
}
