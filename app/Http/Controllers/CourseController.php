<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Exception;
use Illuminate\Http\Request;

class CourseController extends Controller
{
     public function CreateCourse(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'string',
                'university_id' => 'string|exists:universities,id',
                'country_id' => 'string|exists:countries,id',
                'intake_id' => 'string|exists:intakes,id',
                'course_type_id' => 'string|exists:course_types,id',
                'country_id' => 'string|exists:countries,id',
                'course_duration' => 'string',
                'tution_fee' => 'decimal',
                'academic_requirement' => 'text',
                'english_requirement' => 'text',
            ]);

            Course::create([
                'name' => $validated['name'],
                'university_id' => $validated['university_id'],
                'country_id' => $validated['country_id'],
                'intake_id' => $validated['intake_id'],
                'course_type_id' => $validated['course_type_id'],
                'country_id' => $validated['country_id'],
                'course_duration' => $validated['course_duration'],
                'tution_fee' => $validated['tution_fee'],
                'academic_requirement' => $validated['academic_requirement'],
                'english_requirement' => $validated['english_requirement'],
            ]);
            return response()->json(['status' => 'success', 'message' => 'Course Created Succesfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

     public function AllCourse(Request $request)
    {
        $list = Course::all();
                    return response()->json(['status' => 'success', 'list' => $list]);

    }
}
