<?php

namespace App\Http\Controllers;

use App\Models\AssignType;
use Exception;
use Illuminate\Http\Request;

class AssignTypeController extends Controller
{
     public function CreateAssignType(Request $request)
    {

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:25',
            ]);

            AssignType::create([
                'name' => $validated['name'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Assign Type Created successfully',
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function AssignTypeList(Request $request)
    {
        $list = AssignType::orderBy('created_at','desc')->get();

        return response()->json([
            'status' => 'Request Success',
            'list' => $list,
        ]);
    }

    public function AssignTypeUpdate(Request $request)
    {
        try {
            $id = $request->input('id');
            $name = $request->input('name');

            $type = AssignType::find($id);

            if ($type) {
                $type->update(['name' => $name]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Assign Type updated successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Assign Type not found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function AssignTypeDelete(Request $request)
    {
        try {
            $id = $request->id;
            $type = AssignType::find($id);

            if($type) {
                $type->delete();
            } else {
                return response()->json(['status' => 'failed', 'message' => 'This assign type is not found']);
            }

            return response()->json(['status' => 'success', 'message' => 'Assign Type deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function SingleAssignType(Request $request)
    {
        try {
            $id = $request->id;

            $type = AssignType::find($id);

            return response()->json(['status' => 'success', 'list' => $type]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
