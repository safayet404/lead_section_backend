<?php

namespace App\Http\Controllers;

use App\Models\AssignApplicationOfficer;
use App\Models\AssignComplianceOfficer;
use Exception;
use Illuminate\Http\Request;

class AssignCoController extends Controller
{
      public function AssignCo(Request $request)
    {
        try {
            $validated = $request->validate([
                'application_id' => 'exists:applications,id',
                'user_id' => 'exists:users,id',
            ]);

            AssignComplianceOfficer::create([
                'application_id' => $validated['application_id'],
                'user_id' => $validated['user_id']
            ]);

            return response()->json(['status' => 'success', 'message' => 'Application Officer Assign Successfull']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }


    public function AssignList(){
        return AssignComplianceOfficer::all();
    }

  
}
