<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatus;
use Exception;
use Illuminate\Http\Request;

class ApplicationStatusController extends Controller
{
    public function CreateApplicationStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'color' => 'required|string',

            ]);

            ApplicationStatus::create([
                'name' => $validated['name'],
                'color' => $validated['color'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Application Status Created']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function AllStatus(Request $request)
    {
        return ApplicationStatus::all();
    }
}
