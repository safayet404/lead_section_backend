<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Exception;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function CreateApplication(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'exists:students,id',
                'country_id' => 'exists:countries,id',
                'intake_id' => 'exists:intakes,id',
                'course_type_id' => 'exists:course_types,id',
                'university_id' => 'exists:universities,id',
                'course_id' => 'exists:courses,id',
                'channel_partner_id' => 'exists:channel_partners,id',
                'application_status_id' => 'exists:application_statuses,id', 
                'branch_id' => 'exists:branches,id',
                'created_by' => 'exists:users,id',
                'passport_country' => 'string',
            ]);

            Application::create([
                 'student_id' => $validated['student_id'],
                'country_id' =>  $validated['country_id'],
                'intake_id' => $validated['intake_id'] ,
                'course_type_id' => $validated['course_type_id'],
                'university_id' => $validated['university_id'],
                'course_id' => $validated['course_id'],
                'channel_partner_id' => $validated['channel_partner_id'],
                'application_status_id' => $validated['application_status_id'], 
                'branch_id' =>$validated['branch_id'],
                'created_by' => $validated['created_by'],
                'passport_country' => $validated['passport_country'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Application created']);

            
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function ApplicationList(Request $request)
    {
        return Application::all();
        
    }
}
