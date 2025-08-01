<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function CreateBranch(Request $request)
    {

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:25',
            ]);

            Branch::create([
                'name' => $validated['name'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Branch Created successfully',
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function BranchList(Request $request)
    {
        $list = Branch::all();

        return response()->json([
            'status' => 'Request Success',
            'list' => $list,
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
                    'message' => 'Branch updated successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Branch not found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function BranchDelete(Request $request)
    {
        try {
            $id = $request->id;
            $branch = Branch::where('id',$id)->first();

            if($branch)
            {
                $branch->delete();
            }
            else
            {
                 return response()->json(['status' => 'failed', 'message'=> 'This branch is not']);
            }
            return response()->json(['status' => 'success', 'message'=> 'Branch deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function SingleBranch(Request $request)
    {
        try {
            $id = $request->id;

            $branch = Branch::where('id',$id)->first();
            return response()->json(['status' => 'success', 'list' => $branch]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed' , 'message' => $e->getMessage()]);
        }
    }
}
