<?php

namespace App\Http\Controllers;

use App\Models\HomeOwner;
use App\Models\Officer;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    public function index()
    {
        $officers = Officer::with('homeowner')->get();
        $homeowners = HomeOwner::all();
        return view('admin.officer', compact('officers', 'homeowners'));
    }

    // Store a new officer
    public function store(Request $request)
    {
        $request->validate([
            'homeowner_id' => 'required|exists:home_owners,id',
            'position' => 'nullable|string|max:255',
        ]);

        // Define the positions that should only have one officer
        $uniquePositions = [
            'President',
            'Vice President',
            'Secretary',
            'Treasurer',
            'Asst. Secretary',
            'Asst. Treasurer'
        ];

        // Check if the position is one of the unique positions and if it already exists
        if (in_array($request->position, $uniquePositions)) {
            $existingOfficer = Officer::where('position', $request->position)->first();

            if ($existingOfficer) {
                return redirect()->route('officers.index')->with('error', ucfirst($request->position) . ' already has an officer assigned!');
            }
        }

        // Create the new officer
        Officer::create($request->all());

        // Redirect back with a success message
        return redirect()->route('officers.index')->with('success', 'Officer added successfully!');
    }

    // Update an officer
    public function update(Request $request, $id)
    {
        $officer = Officer::findOrFail($id);

        $request->validate([
            'homeowner_id' => 'required|exists:home_owners,id',
            'position' => 'nullable|string|max:255',
        ]);

        $officer->update($request->all());

        // Redirect back with a success message
        return redirect()->route('officers.index')->with('success', 'Officer updated successfully!');
    }

    // Delete an officer
    public function destroy($id)
    {
        $officer = Officer::findOrFail($id);
        $officer->delete();

        // Redirect back with a success message
        return redirect()->route('officers.index')->with('success', 'Officer deleted successfully!');
    }

}