<?php

namespace App\Http\Controllers;

use App\Models\LeadStatus;
use Exception;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    public function CreateLeadStatus(Request $request)
    {
        try {
            $validated = $request->validate(['name' => 'required|string|max:55', 'health_type' => 'nullable|string', 'color_code' => 'nullable|string']);

            LeadStatus::create([
                'name' => $validated['name'],
                'health_type' => $validated['health_type'],
                'color_code' => $validated['color_code'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Lead Status Created']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function LeadStatusList(Request $request)
    {
        $list = LeadStatus::all();

        return response()->json(['status' => 'success', 'list' => $list]);
    }

    public function SingleLeadStatus(Request $request)
    {
        try {
            $id = $request->id;
            $status = LeadStatus::find($id);

            return response()->json(['status' => 'success', 'status' => $status]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function LeadStatusUpdate(Request $request)
    {
        try {
            $id = $request->id;

            $status = LeadStatus::find($id);
            $data = $request->only(['name', 'health_type', 'color_code']);
            if ($status) {
                $status->fill($data)->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Lead Status Updated']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function LeadStatusDelete(Request $request)
    {
        try {
            $id = $request->id;

            $status = LeadStatus::find($id);

            if ($status) {
                $status->delete();
            } else {
                return response()->json(['status' => 'failed', 'message' => 'No lead status found']);
            }

            return response()->json(['status' => 'success', 'message' => 'Lead Status Delete Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }

    }
}
