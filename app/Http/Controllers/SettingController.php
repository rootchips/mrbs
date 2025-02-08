<?php

namespace App\Http\Controllers;

use App\Models\WorkingDay;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $data = WorkingDay::all();

        $result = [
            'days' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->day,
                ];
            })->values()->all(),
            'from' => $data[0]->start_at,
            'to' => $data[0]->end_at,
        ];

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $days = collect($request->days)->pluck('name')->toArray();

        WorkingDay::whereNotIn('day', $days)->delete();

        collect($request->days)->each(function ($item) use ($request) {
            WorkingDay::updateOrCreate(
                ['day' => $item['name']],
                [
                    'start_at' => $request->from,
                    'end_at' => $request->to,
                ]
            );
        });
    }
}
