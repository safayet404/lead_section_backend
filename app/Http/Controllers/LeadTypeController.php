<?php

namespace App\Http\Controllers;

use App\Models\LeadType;
use Exception;
use Illuminate\Http\Request;

class LeadTypeController extends Controller
{
    public function CreateLeadType(Request $request)
    {
        try {
            $validated = $request->validate(['name' => 'required|string|max:50']);

            LeadType::create([
                'name' => $validated['name'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Lead Type Created Successfull']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function LeadTypeList(Request $request)
    {
        $list = LeadType::all();

        return response()->json(['status' => 'success', 'list' => $list]);
    }

    public function SingleLeadType(Request $request)
    {
        try {
            $id = $request->id;
            $type = LeadType::find($id);

            return response()->json(['status' => 'success', 'type' => $type]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function LeadTypeUpdate(Request $request)
    {
        try {
            $id = $request->id;

            $type = LeadType::find($id);
            $data = $request->only(['name']);
            if ($type) {
                $type->fill($data)->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Lead Type Updated']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function LeadTypeDelete(Request $request)
    {
        try {
            $id = $request->id;
            $type = LeadType::find($id);

            if ($type) {
                $type->delete();
            } else {
                return response()->json(['status' => 'failed', 'message' => 'No lead type found']);
            }

            return response()->json(['status' => 'success', 'message' => 'Lead type Delete Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
