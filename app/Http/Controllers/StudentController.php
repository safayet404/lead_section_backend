<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Exception;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function CreateStudent(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string', 
                'last_name' => 'required|string', 
                'email' => 'required|email', 
                'phone' => 'required|string', 
                'date_of_birth' => 'required|string', 
                'passport_number' => 'required|string', 
                'passport_country' => 'required|string', 
                'branch_id' => 'nullable|string|exist:branches,id', 
            ]);

            Student::create([
                'first_name' => $validated['first_name'], 
                'last_name' => $validated['last_name'], 
                'email' => $validated['email'], 
                'phone' => $validated['phone'], 
                'date_of_birth' =>$validated['date_of_birth'], 
                'passport_number' => $validated['passport_number'], 
                'passport_country' => $validated['passport_country'], 
                'branch_id' => 'nullable|string|exist:branches,id', 
            ]);

            return response()->json([
                'status' => "success",
                'message' => "Student Created Successfully"
            ]);
        } catch (Exception $e) {
              return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function AllStudents(Request $request)
    {
        return Student::all();
    }

}
