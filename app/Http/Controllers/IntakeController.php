<?php

namespace App\Http\Controllers;

use App\Models\Intake;
use Exception;
use Illuminate\Http\Request;

class IntakeController extends Controller
{
     public function CreateIntake(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'string'
            ]);

            Intake::create([
                'name' => $validated['name']
            ]);
            return response()->json(['status' => 'success', 'message' => 'Intake Created Succesfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

     public function AllIntake(Request $request)
    {
        $list = Intake::all();
                    return response()->json(['status' => 'success', 'list' => $list]);

    }
}
