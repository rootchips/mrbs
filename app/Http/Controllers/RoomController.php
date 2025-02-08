<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $data = Room::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('description', 'ILIKE', "%{$search}%")
                        ->orWhere('total_pax', 'ILIKE', "%{$search}%")
                        ->orWhere('equipments', 'ILIKE', "%{$search}%")
                        ->orWhere('status', 'ILIKE', "%{$search}%");
                });
            })
            ->paginate(10);

        return response()->json($data);
    }

    public function portal()
    {
        $data = Room::query()
            ->where('status', 'Available')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        sleep(3);

        $room = new Room;
        $room->name = $request->name;
        $room->description = $request->description;
        $room->total_pax = $request->total_pax;
        $room->equipments = $request->equipments;
        $room->status = $request->status;
        $room->save();

        if ($request->hasFile('image_file')) {
            foreach ($request->file('image_file') as $photo) {
                $room->addMedia($photo)->toMediaCollection('rooms');
            }
        }

        return response()->json(['message' => 'Success']);
    }

    public function show(Room $room)
    {
        $data = $room->load('bookings.user', 'media');

        return response()->json($data);
    }

    public function update(Room $room, Request $request)
    {
        sleep(3);
        $room->name = $request->name;
        $room->description = $request->description;
        $room->total_pax = $request->total_pax;
        $room->equipments = $request->equipments;
        $room->status = $request->status;
        $room->save();

        if ($request->hasFile('image_file')) {
            foreach ($request->file('image_file') as $photo) {
                $room->addMedia($photo)->toMediaCollection('rooms');
            }
        }

        return response()->json(['message' => 'Success']);
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return response()->json(['message', 'success']);
    }
}
