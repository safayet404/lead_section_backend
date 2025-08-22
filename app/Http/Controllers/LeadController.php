<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
                'assigned_at' => 'nullable'

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

    public function uploadLeads(Request $request)
{
   

    $validated =  $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        'lead_type' => 'required|exists:lead_types,id',
        'lead_branch' => 'required|exists:branches,id',
        'lead_country' => 'nullable|exists:countries,id',
        'event_id' => 'nullable|exists:events,id',

    ]);

    try {
        $file = $validated['file'];
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header = array_shift($rows);
        $leadsCreated = 0;

        foreach ($rows as $row) {
            $leadData = array_combine($header, $row);

            $data = [
                'lead_date' => $leadData['lead_date'],
                'name' => $leadData['name'],
                'email' => $leadData['email'],
                'phone' => $leadData['phone'],
                'interested_course' => $leadData['interested_course'],
                'interested_country' => $leadData['interested_country'],
                'current_qualification' => $leadData['current_qualification'],
                'ielts_or_english_test' => $leadData['ielts_or_english_test'] ?? null,
                'soruce' => $leadData['soruce'] ?? null,
              
                'lead_type' => $validated['lead_type'],
                'lead_country' => $validated['lead_country'] ?? null,
                'lead_branch' => $validated['lead_branch'] ,
                'event_id' => $validated['event_id'] ?? null,
                'created_by' => $request->header('id')
            ];
           
            Lead::create($data);
            $leadsCreated++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Successfully uploaded and created $leadsCreated leads."
        ]);

    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}



    public function LeadList(Request $request)
    {

        $userId = $request->header('id');

        $user = User::with('branch')->findOrFail($userId);

        $query = Lead::with('status', 'user.branch', 'type', 'event', 'assign_type', 'lead_country', 'lead_branch', 'note.user');

        if ($user->role_id == 1) {

        } elseif ($user->role_id == 3) {
            $query->where('assigned_branch', $user->branch_id);
        } else {
            $query->where('assigned_user', $user->id);
        }

        $query->orderByDesc('assigned_at');

        $list = $query->get()->map(function($lead) {
    $lead->is_new = false;

    if ($lead->assigned_at instanceof Carbon) {
        $lead->is_new = $lead->assigned_at->gt(now()->subHours(24));
    }

    return $lead;
});


        return response()->json(['status' => 'success', 'list' => $list]);
    }

    public function SingleLead(Request $request)
    {
        $id = $request->id;

        $lead = Lead::find($id);

        return response()->json(['status' => 'success', 'lead' => $lead]);
    }

    public function DeleteLead(Request $request)
    {
        try {
            $id = $request->id;

            $lead = Lead::find($id);

            if ($lead) {
                $lead->delete();
            } else {
                return response()->json(['status' => 'success', 'message' => 'This lead is not found']);
            }
        } catch (Exception $e) {
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
                'lead_branch',
                'assigned_at'
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

        $q = Lead::query()->whereNull('assigned_user')->where('lead_type', $validated['lead_type'])
            ->where('lead_branch', $validated['lead_branch']);

        if (! empty($validated['event_id'])) {
            $q->where('event_id', $validated['event_id']);
        }
        if (! empty($validated['lead_country'])) {
            $q->where('lead_country', $validated['lead_country']);
        }

        $totalAvailabe = (clone $q)->count();

        $users = User::query()->join('branches', 'users.branch_id', '=', 'branches.id')
            ->where('users.branch_id', $validated['assign_branch'])->select('users.id', 'users.name', 'users.email', 'users.branch_id', 'branches.name as branch_name')->get();

        $userIds = $users->pluck('id');
        $totals = Lead::select('assigned_user', DB::raw('COUNT(*) as total'))
            ->whereIn('assigned_user', $userIds)
            ->groupBy('assigned_user')
            ->pluck('total', 'assigned_user');

        $byCountry = Lead::select(
            'assigned_user',
            'lead_country',
            DB::raw('COUNT(*) as total'),
            'countries.name as country_name'
        )
            ->join('countries', 'leads.lead_country', '=', 'countries.id')
            ->whereIn('assigned_user', $userIds)
            ->groupBy('assigned_user', 'lead_country', 'countries.name')
            ->get()
            ->groupBy('assigned_user');

        $payload = $users->map(function ($u) use ($totals, $byCountry) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'branch_id' => $u->branch_id,
                'branch_name' => $u->branch_name,
                'current_total' => (int) ($totals[$u->id] ?? 0),
                'by_country' => ($byCountry[$u->id] ?? collect())->map(function ($row) {
                    return [
                        'lead_country' => (int) $row->lead_country,
                        'country_name' => $row->country_name,
                        'total' => (int) $row->total,
                    ];
                })->values(),
            ];

        });

        return response()->json([
            'status' => 'success',
            'total_leads_available' => $totalAvailabe,
            'total_leads_assigned' => 0,
            'remaining_leads' => $totalAvailabe,
            'users' => $users,
            'groupUser' => $payload,
        ]);
    }

    public function AssignLeads(Request $request)
    {
        $validated = $request->validate([
            'lead_type' => 'required|exists:lead_types,id',
            'lead_country' => 'nullable|exists:countries,id',
            'lead_branch' => 'required|exists:branches,id',
            'event_id' => 'nullable|exists:events,id',
            'assign_branch' => 'required|exists:branches,id',
            'assignments' => 'required|array',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.leads' => 'required|integer|min:0',
        ]);

        $query = Lead::query()
            ->whereNull('assigned_user')
            ->where('lead_type', $validated['lead_type'])
            ->where('lead_branch', $validated['lead_branch']);

        if (! empty($validated['event_id'])) {
            $query->where('event_id', $validated['event_id']);
        }
        if (! empty($validated['lead_country'])) {
            $query->where('lead_country', $validated['lead_country']);
        }

        $availableLeads = $query->get();

        $assignedCount = 0;
        foreach ($validated['assignments'] as $assignment) {
            if ($assignment['leads'] <= 0) {
                continue;
            }

            $take = min($assignment['leads'], $availableLeads->count());
            $leadsForUser = $availableLeads->splice(0, $take);

            foreach ($leadsForUser as $lead) {
                $lead->update([
                    'assigned_user' => $assignment['user_id'],
                    'assigned_branch' => $validated['assign_branch'],
                    'assign_id' => 2,
                    'assigned_at' => now()
                ]);
            }

            $assignedCount += $take;
        }

        return response()->json([
            'status' => 'success',
            'assigned' => $assignedCount,
            'remaining' => $availableLeads->count(),
        ]);
    }

    public function AssignSave(Request $request)
    {
        $validated = $request->validate([
            'lead_type' => 'required|exists:lead_types,id',
            'lead_country' => 'nullable|exists:countries,id',
            'lead_branch' => 'required|exists:branches,id',
            'event_id' => 'nullable|exists:events,id',
            'assign_branch' => 'required|exists:branches,id',
            'assignments' => 'required|array',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.leads' => 'required|integer|min:0',

        ]);

        return DB::transaction(function () use ($validated) {
            $poolQ = Lead::query()
                ->whereNull('assigned_user')
                ->where('lead_type', $validated['lead_type'])
                ->where('lead_branch', $validated['lead_branch']);

            if (! empty($validated['event_id'])) {
                $poolQ->where('event_id', $validated['event_id']);
            }
            if (! empty($validated['lead_country'])) {
                $poolQ->where('lead_country', $validated['lead_country']);
            }

            $poolIds = $poolQ->inRandomOrder()->lockForUpdate()->pluck('id')->toArray();
            $available = count($poolIds);

            $requested = array_sum(array_map(fn ($a) => (int) $a['leads'], $validated['assignments']));
            $idx = 0;

            foreach ($validated['assignments'] as $a) {
                $userId = (int) $a['user_id'];
                $count = max(0, (int) $a['leads']);
                if ($count === 0 || $idx >= $available) {
                    continue;
                }

                $slice = array_slice($poolIds, $idx, $count);
                $idx += count($slice);

                if (! $slice) {
                    continue;
                }

                Lead::whereIn('id', $slice)->update([
                    'assigned_user' => $userId,
                    'assigned_branch' => $validated['assign_branch'],
                    'assign_id' => 2,
                     'assigned_at' => now()
                ]);
            }

            $assigned = min($requested, $available);
            $remaining = max(0, $available - $assigned);

            return response()->json([
                'status' => 'success',
                'message' => 'Leads assigned successfully.',
                'assigned' => $assigned,
                'remaining' => $remaining,
            ]);
        });
    }

    public function branchManager(Request $request)
    {
        $userId = $request->header('id');
        $user = User::with('branch')->findOrFail($userId);

          if ($user->role_id != 3) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Unauthorized: Only branch managers can access this resource.'
        ], 403);
    }

        $branchId = $user->branch_id;

        // Status IDs
        $status_initial = 1;
        $status_converted = 11;
        $status_followup = 10;

        $range = $request->input('range', 'week');
        $leadType = $request->input('lead_type', 'all');
        $now = Carbon::now();
        switch ($range) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'all':
            default:
                $start = null;
                $end = null;
                break;
        }

        // Helper function to apply date filter
        $applyDateFilter = function ($query, $column = 'created_at') use ($start, $end) {
            if ($start && $end) {
                $query->whereBetween($column, [$start, $end]);
            }

            return $query;
        };

           $applyLeadTypeFilter = function($query) use($leadType)
        {
            if($leadType !== 'all')
            {
                $query->where('lead_type' ,$leadType);
            }

            return $query;
        };

        // Total leads assigned
      
        $leadsAssigned = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('assigned_branch', $branchId)
            )
        )->count();

        // Contacted leads (updated this range, excluding initial)
        $contacted = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('assigned_branch', $branchId)
                    ->where('status_id', '!=', $status_initial)
            ),
            'updated_at'
        )->count();
        $pending = $applyDateFilter(
            $applyLeadTypeFilter(
                Lead::where('assigned_branch', $branchId)
                    ->where('status_id', $status_initial)
                )
                
        )->count();

        // Converted leads
        $converted = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('assigned_branch', $branchId)
                    ->where('status_id', $status_converted)
            ),
            'updated_at'
        )->count();

        // Follow-ups
        $followUps = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('assigned_branch', $branchId)
                    ->where('status_id', $status_followup)
            )
                ,
            'updated_at'
        )->count();

        // Per counsellor summary
        $perCounsellorQuery = $applyLeadTypeFilter(Lead::where('assigned_branch', $branchId));
        if ($start && $end) {
            $perCounsellorQuery->whereBetween('created_at', [$start, $end]);
        }

        $perCounsellor = $perCounsellorQuery
            ->selectRaw('assigned_user, 
            COUNT(*) as total, 
            SUM(CASE WHEN status_id != ? THEN 1 ELSE 0 END) as contacted,
            SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as converted,
            SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as followup',
                [$status_initial, $status_initial, $status_converted, $status_followup]
            )
            ->groupBy('assigned_user')
            ->with('user:id,name')
            ->get();

        // Trend (daily for week/month, monthly for year)
        $trendQuery = $applyLeadTypeFilter(Lead::where('assigned_branch', $branchId));
        if ($start && $end) {
            $trendQuery->whereBetween('created_at', [$start, $end]);
        }

        if ($range === 'year') {
            $weeklyTrend = $trendQuery
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, COUNT(*) as count')
                ->groupBy('period')
                ->orderBy('period')
                ->get();
        } else {
            $weeklyTrend = $trendQuery
                ->selectRaw('DATE(created_at) as period, COUNT(*) as count')
                ->groupBy('period')
                ->orderBy('period')
                ->get();
        }

        // Response
        return response()->json([
            'summary' => [
            
                'assigned' => $leadsAssigned,
                'contacted' => $contacted,
                'converted' => $converted,
                'followUps' => $followUps,
                'pendingCall' => $pending,
                'conversionRate' => $leadsAssigned > 0 ? round(($converted / $leadsAssigned) * 100, 2) : 0,
            ],
            'perCounsellor' => $perCounsellor,
            'trend' => $weeklyTrend,
            'range' => $range,
        ]);
    }

    public function AdminReport(Request $request)
    {

            $userId = $request->header('id');
                $user = User::findOrFail($userId);

              if ($user->role_id != 1) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Unauthorized: Only branch managers can access this resource.'
        ], 403);
    }



        $status_initial = 1;
        $status_converted = 11;
        $status_followup = 10;

        $range = $request->input('range', 'week');
        $leadType = $request->input('lead_type', 'all');

        $now = Carbon::now();
        switch ($range) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'all':
            default:
                $start = null;
                $end = null;
                break;
        }

        $applyDateFilter = function ($query, $column = 'created_at') use ($start, $end) {
            if ($start && $end) {
                $query->whereBetween($column, [$start, $end]);
            }

            return $query;
        };

        $applyLeadTypeFilter = function($query) use($leadType)
        {
            if($leadType !== 'all')
            {
                $query->where('lead_type' ,$leadType);
            }

            return $query;
        };

        $leadsAssigned = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('assigned_user', '!=', null)
            )
        )->count();
        $totalLeads = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::query()
            )
        )->count();

        $leadsUnAssigned = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('assigned_user', null)
            )
        )->count();

        $contacted = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('status_id', '!=', $status_initial)
            ),
                'updated_at'
        )->count();

        $pending = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('status_id', $status_initial)
            )
        )->count();

        $converted = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('status_id', $status_converted)
            ),
            'updated_at'
        )->count();

        $followUps = $applyDateFilter(
            $applyLeadTypeFilter(

                Lead::where('status_id', $status_followup)
            ),
            'updated_at'
        )->count();

        $perCounsellorQuery = $applyLeadTypeFilter(
            Lead::where('assigned_user', '!=', null)
        );

        if ($start && $end) {
            $perCounsellorQuery->whereBetween('created_at', [$start, $end]);
        }

        $perCounsellor = $perCounsellorQuery
            ->selectRaw('assigned_user, 
            COUNT(*) as total, 
            SUM(CASE WHEN status_id != ? THEN 1 ELSE 0 END) as contacted,
            SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as converted,
            SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as followup',
                [$status_initial, $status_initial, $status_converted, $status_followup]
            )
            ->groupBy('assigned_user')
            ->with('user:id,name')
            ->get();

        if ($start && $end) {
            $trendQuery = $applyLeadTypeFilter(Lead::whereBetween('created_at', [$start, $end]));
        }

        if ($range === 'year') {
            $weeklyTrend = $trendQuery
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, COUNT(*) as count')
                ->groupBy('period')
                ->orderBy('period')
                ->get();
        } else {
            $weeklyTrend = $trendQuery
                ->selectRaw('DATE(created_at) as period, COUNT(*) as count')
                ->groupBy('period')
                ->orderBy('period')
                ->get();
        }

        return response()->json([
            'summary' => [
                'total' => $totalLeads,
                'assigned' => $leadsAssigned,
                'unassigned' => $leadsUnAssigned,
                'contacted' => $contacted,
                'converted' => $converted,
                'followUps' => $followUps,
                'pendingCall' => $pending,
                'conversionRate' => $leadsAssigned > 0 ? round(($converted / $leadsAssigned) * 100, 2) : 0,
            ],
            'perCounsellor' => $perCounsellor,
            'trend' => $weeklyTrend,
            'range' => $range,
        ]);

    }
}
