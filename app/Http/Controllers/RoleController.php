<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function CreateRole(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:25']);

        try {
            Role::create([
                'name' => $validated['name'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Role created successfull']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function RoleList(Request $request)
    {
        $list = Role::all();

        return response()->json(['status' => 'success', 'list' => $list]);
    }

    public function SingleRole(Request $request)
    {
        $id = $request->id;

        $role = Role::find($id);

        return response()->json(['status' => 'success', 'role' => $role]);
    }

    public function RoleUpdate(Request $request)
    {

        try {
            $id = $request->id;

            $role = Role::find($id);

            $data = $request->only(['name']);
            if ($role) {

                $role->fill($data)->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Role Updated']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }

    }

    public function RoleDelete(Request $request)
    {
        try {
            $id = $request->id;
            $role = Role::find($id);

            if ($role) {
                $role->delete();
            } else {
                return response()->json(['status' => 'failed', 'message' => 'This role is not found']);
            }

            return response()->json(['status' => 'success', 'message' => 'Role deleted successfull']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
