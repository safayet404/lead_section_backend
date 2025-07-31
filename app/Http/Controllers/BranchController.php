<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    function CreateBranch(Request $request)
    {
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:25'
            ]);
    
            Branch::create([
                'name' => $validated['name']
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Branch Created successfully'
            ]);
            
        } catch (Exception $e) {
             return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
