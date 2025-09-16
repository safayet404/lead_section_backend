<?php

namespace App\Http\Controllers;

use App\Models\ChannelPartner;
use Exception;
use Illuminate\Http\Request;

class ChannelPartnerController extends Controller
{
    public function CreateChannelPartner(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'contact_person' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'required|string',
            ]);

            ChannelPartner::create([
                'name' => $validated['name'],
                'contact_person' => $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Channel Partner Created']);
        } catch (Exception $e) {

            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function AllChannelPartner(Request $request)
    {
        return ChannelPartner::all();
    }
}
