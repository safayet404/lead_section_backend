<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function AssignPreview(Request $request)
    {
        $validated = $request->validate([
            'lead_type' => 'required|exists:lead_types,id',
            'lead_country' => 'nullable|exists:countries,id',
            'lead_branch' => 'required|exists:branches,id',
            'event_id' => 'nullable|exists:events,id',
            'assign_branch' => 'required|exists:branches,id',
        ]);

        $q = Lead::query()->whereNull('assigned_user')->where('lead_type',$validated['lead_type'])
        ->where('lead_branch',$validated['lead_branch']);

        if(!empty($validated['event_id'])){
            $q->where('event_id',$validated['event_id']);
        }
  if(!empty($validated['lead_country'])){
            $q->where('lead_country',$validated['lead_country']);
        }

        $totalAvailabe =(clone $q)->count();

        $users = User::query()->join('branches','users.branch_id' ,'=','branches.id')
        ->where('users.branch_id',$validated['assign_branch'])->select('users.id','users.name','users.email','users.branch_id','branches.name as branch_name')->get();

        $userIds = $users->pluck('id');
        $totals = Lead::select('assigned_user',DB::raw('COUNT(*) as total'))
        ->whereIn('assigned_user',$userIds)
        ->groupBy('assigned_user')
        ->pluck('total','assigned_user');

        $byCountry = Lead::select(
        'assigned_user',
        'lead_country',
        DB::raw('COUNT(*) as total'),
        'countries.name as country_name'
    )
    ->join('countries', 'leads.lead_country', '=', 'countries.id')
    ->whereIn('assigned_user', $userIds)
    ->groupBy('assigned_user','lead_country','countries.name')
    ->get()
    ->groupBy('assigned_user');


        $payload = $users->map(function ($u) use ($totals,$byCountry){
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'branch_id' => $u->branch_id,
                'branch_name' => $u->branch_name, // ✅ Added
                'current_total' => (int) ($totals[$u->id] ?? 0),
                'by_country' => ($byCountry[$u->id] ?? collect())->map(function ($row){
                    return [
                        'lead_country' => (int) $row->lead_country,
                       'country_name' => $row->country_name, // works now
                        'total' => (int) $row->total,
                    ];
                })->values()                
            ];

        });


        return response()->json([
            'status' => 'success',
            'total_leads_available' => $totalAvailabe,
            'total_leads_assigned' => 0,
            'remaining_leads' => $totalAvailabe,
            'users' => $users,
            'groupUser' => $payload
        ]);
    }
}
