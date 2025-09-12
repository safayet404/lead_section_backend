<?php

namespace App\Http\Controllers;

use App\Models\CourseType;
use Exception;
use Illuminate\Http\Request;

class CourseTypeController extends Controller
{
     public function CreateCourseType(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'string'
            ]);

            CourseType::create([
                'name' => $validated['name']
            ]);
            return response()->json(['status' => 'success', 'message' => 'Course Type Created Succesfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
     public function AllCourseType(Request $request)
    {
        $list = CourseType::all();
                    return response()->json(['status' => 'success', 'list' => $list]);

    }
}
