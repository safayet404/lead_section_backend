<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Exception;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function CreateEvent(Request $request)
    {
        try {
            $validated = $request->validate(['name' => 'required|string']);

            Event::create([
                'name' => $validated['name']
            ]);
            return response()->json(['status' => 'success', 'message' => 'Event Created']);
        } catch (Exception $e) {
           return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function EventList(Request $request)
    {
        $list = Event::all();
        return response()->json(['status' => 'success', 'events' => $list]);
    }

    public function SingleEvent(Request $request)
    {
        try {
            $id = $request->id;
            $event = Event::find($id);
            return response()->json(['status' => 'success', 'event' => $event]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

      public function EventUpdate(Request $request)
    {
        try {
            $id = $request->id;

            $event = Event::find($id);
            $data = $request->only(['name']);
            if ($event) {
                $event->fill($data)->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Lead Updated']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function EventDelete(Request $request)
    {
        try {
            $id = $request->id;

            $event = Event::find($id);

            if ($event) {
                $event->delete();
            } else {
                return response()->json(['status' => 'failed', 'message' => 'No event found']);
            }

            return response()->json(['status' => 'success', 'message' => 'Event Delete Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
        
    }
}
