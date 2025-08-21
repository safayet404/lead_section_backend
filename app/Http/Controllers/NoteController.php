<?php

namespace App\Http\Controllers;

use App\Models\ManagerNote;
use Exception;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function CreateNote(Request $request)
    {
        try {
            $validated = $request->validate(['note' => 'required|string', 'lead_id' => 'required|exists:leads,id', 'user_id' => 'required|exists:users,id']);

            ManagerNote::create([
                'note' => $validated['note'],
                'lead_id' => $validated['lead_id'],
                'user_id' => $validated['user_id'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Manager Note Created']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function NoteList(Request $request)
    {
        $list = ManagerNote::with('user')->get();

        return response()->json(['status' => 'success', 'list' => $list]);
    }

    public function SingleNote(Request $request)
    {
        try {
            $id = $request->id;
            $note = ManagerNote::find($id);

            return response()->json(['status' => 'success', 'note' => $note]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function NoteUpdate(Request $request)
    {
        try {
            $id = $request->id;

            $note = ManagerNote::find($id);
            $data = $request->only(['note', 'lead_id', 'user_id']);
            if ($note) {
                $note->fill($data)->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Note Updated']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function NoteDelete(Request $request)
    {
        try {
            $id = $request->id;

            $note = ManagerNote::find($id);

            if ($note) {
                $note->delete();
            } else {
                return response()->json(['status' => 'failed', 'message' => 'No note found']);
            }

            return response()->json(['status' => 'success', 'message' => 'Note Delete Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }

    }
}
