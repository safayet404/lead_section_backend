<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Course;
use App\Models\CourseType;
use App\Models\Intake;
use App\Models\University;
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
               'tution_fee' => 'nullable|numeric',

                  'academic_requirement' => 'nullable|string',
    'english_requirement' => 'nullable|string',
            ]);

            Course::create([
                'name' => $validated['name'],
                'university_id' => $validated['university_id'],
              
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

     public function Countries()
    {
        return Country::all();
    }

    public function IntakeByCountry($countryId){
        return Intake::whereHas('courses', fn($q) => $q->where('country_id',$countryId))->get();
    }

    public function Universities($countryId,$intakeId){
        return University::where('country_id',$countryId)->whereHas('courses', fn($q) => $q->where('intake_id',$intakeId))->get();
    }

    public function CourseTypes($countryId,$intakeId,$universityId){
        return CourseType::whereHas('courses', function ($q) use ($countryId,$intakeId,$universityId){
            $q->where('country_id',$countryId)->where('intake_id',$intakeId)->where('university_id',$universityId);
        } )->get();
    }

    public function Courses($countryId,$intakeId,$universityId,$courseTypeId)
    {
        return Course::with(['university','country','intake','courseType'])->where('country_id',$countryId)->where('intake_id',$intakeId)->where('university_id',$universityId)->where('course_type_id',$courseTypeId)->get();
    }
}
