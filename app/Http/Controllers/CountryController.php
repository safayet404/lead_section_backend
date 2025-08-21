<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Exception;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function CreateCountry(Request $request)
    {
        try {
            $validated = $request->validate(['name' => 'required|string']);

            Country::create([
                'name' => $validated['name'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Country Created']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function CountryList(Request $request)
    {
        $list = Country::all();

        return response()->json(['status' => 'success', 'list' => $list]);
    }

    public function SingleCountry(Request $request)
    {
        try {
            $id = $request->id;
            $country = Country::find($id);

            return response()->json(['status' => 'success', 'country' => $country]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function CountryUpdate(Request $request)
    {
        try {
            $id = $request->id;

            $country = Country::find($id);
            $data = $request->only(['name']);
            if ($country) {
                $country->fill($data)->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Country Updated']);

        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function CountryDelete(Request $request)
    {
        try {
            $id = $request->id;

            $country = Country::find($id);

            if ($country) {
                $country->delete();
            } else {
                return response()->json(['status' => 'failed', 'message' => 'No country found']);
            }

            return response()->json(['status' => 'success', 'message' => 'Country Delete Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }

    }
}
