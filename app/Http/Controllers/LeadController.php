<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Exception;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function CreateLead(Request $request)
    {
        try {

            $id = $request->header('id');
            $validated = $request->validate([
                'lead_date' => 'required|date',
                'name' => 'required|string|max:55',
                'email' => 'required|email',
                'phone' => 'required|string|min:11',
                'interested_course' => 'required|string',
                'interested_country' => 'required|string|max:115',
                'current_qualification' => 'required|string|max:100',
                'ielts_or_english_test' => 'nullable|string|max:55',
                'soruce' => 'nullable|string',
                'status_id' => 'nullable|exists:lead_statues,id',
                'notes' => 'nullable|string',
                'assigned_branch' => 'nullable|exists:branches,id',
                'assigned_user' => 'nullable|exists:users,id',
                'created_by' => 'nullable|exists:users,id',
                'lead_type' => 'nullable|exists:lead_types,id',
                'event_id' => 'nullable|exists:events,id',

            ]);

            if (empty($validated['status_id'])) {
    $validated['status_id'] = 1;
}

            Lead::create([
                'lead_date' => $validated['lead_date'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'interested_course' => $validated['interested_course'],
                'interested_country' => $validated['interested_country'],
                'current_qualification' => $validated['current_qualification'],
                'ielts_or_english_test' => $validated['ielts_or_english_test'] ?? null,
                'soruce' => $validated['soruce'] ?? null,
                'status_id' => $validated['status_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'assigned_branch' => $validated['assigned_branch'] ?? null,
                'assigned_user' => $validated['assigned_user'] ?? null,
                'lead_type' => $validated['lead_type'] ?? null,
                'event_id' => $validated['event_id'] ?? null,
                'created_by' => $id,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Lead Created Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'success', 'message' => $e->getMessage()]);
        }
    }

    public function LeadList(Request $request)
    {
        $list = Lead::with('status','user.branch','type','event','assign_type','lead_country','lead_branch','note.user' )->get();

        return response()->json(['status' => 'success','list' => $list ]);
    }

    public function SingleLead(Request $request)
    {
        $id = $request->id;

        $lead = Lead::find($id);

        return response()->json(['status' => 'success','lead' => $lead]);
    }

    public function DeleteLead(Request $request)
    {
        try{
            $id = $request->id;

            $lead = Lead::find($id);

            if($lead)
            {
                $lead->delete();
            }else{
                return response()->json(['status' => 'success', 'message' => 'This lead is not found']);
            }
        }catch(Exception $e)
        {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function LeadUpdate(Request $request)
    {
        try {

            $id = $request->id;
            $lead = Lead::find($id);
            $data = $request->only([
                'lead_date',
                'name',
                'email',
                'phone',
                'interested_course',
                'interested_country',
                'current_qualification',
                'ielts_or_english_test',
                'soruce',
                'status_id',
                'notes',
                'assigned_branch',
                'event_id',
                'lead_type',
                'assigned_user',
                'assign_id',
                'lead_country',
                'lead_branch'
            ]);
              $lead->fill($data)->save();

              return response()->json(['status' => 'success', 'message' => 'Lead update successfull']);


        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
