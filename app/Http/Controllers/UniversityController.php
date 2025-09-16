<?php

namespace App\Http\Controllers;

use App\Models\University;
use Exception;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function CreateUniversity(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'string',
                'country_id' => 'exists:countries,id',
            ]);

            University::create([
                'name' => $validated['name'],
                'country_id' => $validated['country_id'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'University Created Succesfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function AllUniversity(Request $request)
    {
        $list = University::all();

        return response()->json(['status' => 'success', 'list' => $list]);

    }
}
