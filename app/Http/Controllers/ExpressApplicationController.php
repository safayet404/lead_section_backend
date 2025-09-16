<?php

namespace App\Http\Controllers;

use App\Models\ApplicationFile;
use App\Models\ExpressApplication;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExpressApplicationController extends Controller
{
    public function CreateApplication(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email',
                'country_of_residence' => 'required|string|max:255',
                'whatsapp_number' => 'required|string|max:20',
                'country_to_apply' => 'required|string|max:255',
                'intake' => 'required|string|max:255',
                'course_type' => 'required|string|max:255',
                'university' => 'required|string|max:255',
                'course' => 'required|string|max:255',
                'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            $application = ExpressApplication::create($validated);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // Store file on public disk
                    $path = $file->store('applications/'.$application->id, 'public');

                    ApplicationFile::create([
                        'application_id' => $application->id,
                        'file_path' => $path,
                        'file_type' => null,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Reload application with files & add full URL for each
            $application->load('files');
            $application->files->transform(function ($file) {
                $file->file_url = Storage::url($file->file_path);

                return $file;
            });

            return response()->json([
                'message' => 'Express Application submitted successfully',
                'data' => $application,
            ], 201);
        } catch (Exception $e) {
            Log::error('Application creation failed: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function ExpressApplicationList(Request $request)
    {
        try {
            $list = ExpressApplication::with('files')->get();

            return response()->json(['status' => 'success', 'message' => 'Express Application List', 'list' => $list]);
        } catch (Exception $e) {
            return response()->json(['status' => 'success', 'message' => $e->getMessage()]);
        }
    }

    public function SingleExpressApplication(Request $request)
    {
        try {
            $id = $request->id;

            $application = ExpressApplication::find($id);

            return response()->json(['status' => 'success', 'application' => $application]);
        } catch (Exception $e) {
            return response()->json(['status' => 'success', 'Single Application' => $e->getMessage()]);
        }
    }
}
