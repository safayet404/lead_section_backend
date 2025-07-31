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

    function BranchList(Request $request)
    {
        $list = Branch::all();

        return response()->json([
            'status' => 'Request Success',
            'list' => $list
        ]);
    }

    public function BranchUpdate(Request $request)
{
    try {
        $id = $request->input('id');
        $name = $request->input('name');

        $branch = Branch::find($id);

        if ($branch) {
            $branch->update(['name' => $name]);

            return response()->json([
                'status' => 'success',
                'message' => 'Branch updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Branch not found'
            ]);
        }
    } catch (Exception $e) {
        return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
    }
}

}
