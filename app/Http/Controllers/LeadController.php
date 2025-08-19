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

    public function AssignLeads(Request $request)
{
    $validated = $request->validate([
        'lead_type'     => 'required|exists:lead_types,id',
        'lead_country'  => 'nullable|exists:countries,id',
        'lead_branch'   => 'required|exists:branches,id',
        'event_id'      => 'nullable|exists:events,id',
        'assign_branch' => 'required|exists:branches,id',
        'assignments'   => 'required|array', // [{user_id: 5, leads: 3}, ...]
        'assignments.*.user_id' => 'required|exists:users,id',
        'assignments.*.leads'   => 'required|integer|min:0'
    ]);

    // Base query for unassigned leads
    $query = Lead::query()
        ->whereNull('assigned_user')
        ->where('lead_type', $validated['lead_type'])
        ->where('lead_branch', $validated['lead_branch']);

    if (!empty($validated['event_id'])) {
        $query->where('event_id', $validated['event_id']);
    }
    if (!empty($validated['lead_country'])) {
        $query->where('lead_country', $validated['lead_country']);
    }

    $availableLeads = $query->get();

    $assignedCount = 0;
    foreach ($validated['assignments'] as $assignment) {
        if ($assignment['leads'] <= 0) continue;

        $take = min($assignment['leads'], $availableLeads->count());
        $leadsForUser = $availableLeads->splice(0, $take);

        foreach ($leadsForUser as $lead) {
            $lead->update([
                'assigned_user'   => $assignment['user_id'],
                'assigned_branch' => $validated['assign_branch'],
                'assign_id'          => 2 
            ]);
        }

        $assignedCount += $take;
    }

    return response()->json([
        'status' => 'success',
        'assigned' => $assignedCount,
        'remaining' => $availableLeads->count()
    ]);
}


public function AssignSave(Request $request)
{
    $validated = $request->validate([
        'lead_type'     => 'required|exists:lead_types,id',
        'lead_country'  => 'nullable|exists:countries,id',
        'lead_branch'   => 'required|exists:branches,id',     // belongs-to branch
        'event_id'      => 'nullable|exists:events,id',
        'assign_branch' => 'required|exists:branches,id',      // branch whose users are being assigned to
        'assignments'   => 'required|array',
        'assignments.*.user_id' => 'required|exists:users,id',
        'assignments.*.leads'   => 'required|integer|min:0',
        'assigned_status_id'    => 'nullable|exists:lead_statues,id', // optional; default below
    ]);

    $assignedStatusId = $validated['assigned_status_id'] ?? 2; // <-- adjust to your "Assigned" status id

    return DB::transaction(function () use ($validated, $assignedStatusId) {
        // Build the pool of unassigned leads with same filters as preview
        $poolQ = Lead::query()
            ->whereNull('assigned_user')
            ->where('lead_type',  $validated['lead_type'])
            ->where('lead_branch',$validated['lead_branch']);

        if (!empty($validated['event_id'])) {
            $poolQ->where('event_id', $validated['event_id']);
        }
        if (!empty($validated['lead_country'])) {
            $poolQ->where('lead_country', $validated['lead_country']);
        }

        // Randomize & lock to avoid race conditions
        $poolIds = $poolQ->inRandomOrder()->lockForUpdate()->pluck('id')->toArray();
        $available = count($poolIds);

        $requested = array_sum(array_map(fn($a) => (int)$a['leads'], $validated['assignments']));
        $idx = 0; // pointer into pool

        foreach ($validated['assignments'] as $a) {
            $userId = (int) $a['user_id'];
            $count  = max(0, (int) $a['leads']);
            if ($count === 0 || $idx >= $available) continue;

            $slice = array_slice($poolIds, $idx, $count);
            $idx  += count($slice);

            if (!$slice) continue;

            Lead::whereIn('id', $slice)->update([
                'assigned_user'   => $userId,
                'assigned_branch' => $validated['assign_branch'], // or user's branch_id if you prefer
                'status_id'       => $assignedStatusId,
            ]);
        }

        $assigned = min($requested, $available);
        $remaining = max(0, $available - $assigned);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Leads assigned successfully.',
            'assigned'  => $assigned,
            'remaining' => $remaining,
        ]);
    });
}


}
