<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Student;
use App\Models\StudentApplicationFile;
use App\Models\User;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function CreateApplication(Request $request)
    {
        try {
             $id = $request->header('id');

          if (!$id) {
            return response()->json([
                'status' => 'failed', 
                'message' => 'User ID not provided in header'
            ], 400);
        }
        
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'failed', 
                'message' => 'User not found'
            ], 404);
        }
            $validated = $request->validate([
              
                'country_id' => 'exists:countries,id',
                'intake_id' => 'exists:intakes,id',
                'course_type_id' => 'exists:course_types,id',
                'university_id' => 'exists:universities,id',
                'course_id' => 'exists:courses,id',
    'channel_partner_id' => 'nullable|exists:channel_partners,id', // ✅ make nullable
                'application_status_id' => 'exists:application_statuses,id', 
                'branch_id' => 'exists:branches,id',
                'created_by' => 'exists:users,id',
                'passport_country' => 'string',
            ]);

           $application =  Application::create([
                 'student_id' => $validated['student_id'],
                'country_id' =>  $validated['country_id'],
                'intake_id' => $validated['intake_id'] ,
                'course_type_id' => $validated['course_type_id'],
                'university_id' => $validated['university_id'],
                'course_id' => $validated['course_id'],
    'channel_partner_id' => $validated['channel_partner_id'] ?? null, // ✅ safe fallback
                'application_status_id' => $validated['application_status_id'], 
                'branch_id' => $user->branch_id,
                'created_by' => $id,
                'passport_country' => $validated['passport_country'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Application created', 'id' => $application->id]);

            
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function CreateStudentWithApplication(Request $request)
    {
        DB::beginTransaction();
        try {
             $id = $request->header('id');

          if (!$id) {
            return response()->json([
                'status' => 'failed', 
                'message' => 'User ID not provided in header'
            ], 400);
        }
        
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'failed', 
                'message' => 'User not found'
            ], 404);
        }

        $validated = $request->validate([
            'first_name'       => 'required|string',
            'last_name'        => 'required|string',
            'email'            => 'required|email|unique:students,email',
            'phone'            => 'required|string',
            'date_of_birth'    => 'required|date',
            'passport_number'  => 'required|string',
            'passport_country' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'gender' => 'nullable',
            'visa_refusal' => 'nullable',

           
            'country_id'           => 'required|exists:countries,id',
            'intake_id'            => 'required|exists:intakes,id',
            'course_type_id'       => 'required|exists:course_types,id',
            'university_id'        => 'required|exists:universities,id',
            'course_id'            => 'required|exists:courses,id',
            'channel_partner_id'   => 'nullable|exists:channel_partners,id',
            'application_status_id'=> 'nullable|exists:application_statuses,id',
            'counsellor_phone' => 'nullable|string',
            'counsellor_email' => 'nullable|string',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $student = Student::create([
          'first_name'       => $validated['first_name'],
            'last_name'        => $validated['last_name'],
            'email'            => $validated['email'],
            'phone'            => $validated['phone'],
            'date_of_birth'    => $validated['date_of_birth'],
            'passport_number'  => $validated['passport_number'],
            'passport_country' => $validated['passport_country'],
            'branch_id'        => $user->branch_id,
            'address' => $validated['address'],
            'city' => $validated['city'],
            'gender' => $validated['gender'],
            'visa_refusal' => $validated['visa_refusal'],
         
          
        ]);

   
         $application = Application::create([
             'student_id'           => $student->id, // Use the created student's ID
            'country_id'           => $validated['country_id'],
            'intake_id'            => $validated['intake_id'],
            'course_type_id'       => $validated['course_type_id'],
            'university_id'        => $validated['university_id'],
            'course_id'            => $validated['course_id'],
            'channel_partner_id'   => $validated['channel_partner_id'] ?? null,
            'application_status_id'=> $validated['application_status_id'] ?? 1, // 
            'branch_id'            => $user->branch_id,
            'created_by'           => $user->id,
            'passport_country'     => $validated['passport_country'],
              'counsellor_phone' => $validated['counsellor_phone'],
            'counsellor_email' => $validated['counsellor_email']
        ]);

         if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // Store file on public disk
                    $path = $file->store('applications/' . $application->id, 'public');

                    StudentApplicationFile::create([
                        'application_id' => $application->id,
                        'file_path' => $path,
                        'file_type' => null,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            // Reload application with files & add full URL for each
            $application->load('files');
            $application->files->transform(function ($file) {
                $file->file_url = Storage::url($file->file_path);
                return $file;
            });

        DB::commit();

            return response()->json([
            'status' => 'success',
            'message' => 'Student and Application created successfully',
            'data' => [
                'student_id' => $student->id,
                'application_id' => $application->id,
                'student' => $student,
                'application' => $application
            ]
        ], 201);


        } catch (Exception $e) {
              DB::rollBack();

        return response()->json([
            'status' => 'failed',
            
            'message' => $e->getMessage()
        ], 500);
        }
    }

    public function ApplicationList(Request $request)
    {
        return Application::with('student','country','files','user', 'intake','courseType','university','course','branch','applicationStatus','channelPartner')->latest()->get();

    }

    public function SingleApplication($id)
    {
        return Application::with('student','country','files','user', 'intake','courseType','university','course','branch','applicationStatus','channelPartner')->find($id);
    }
}
