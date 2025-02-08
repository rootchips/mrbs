<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $data = Room::when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('description', 'ILIKE', "%{$search}%")
                        ->orWhere('total_pax', 'ILIKE', "%{$search}%")
                        ->orWhere('equipments', 'ILIKE', "%{$search}%")
                        ->orWhere('status', 'ILIKE', "%{$search}%");
                });
            })
            ->select(['id', 'name', 'description', 'total_pax', 'equipments', 'status'])
            ->paginate(10);

        return response()->json($data);
    }

    public function portal()
    {
        return response()->json(
            Room::where('status', 'Available')->select(['id', 'name'])->get()
        );
    }

    public function store(Request $request)
    {
        $room = Room::create($request->only(['name', 'description', 'total_pax', 'equipments', 'status']));

        $this->handleFileUpload($request, $room);

        return response()->json(['message' => 'Success']);
    }

    public function show(Room $room)
    {
        return response()->json(
            $room->load([
                'bookings' => function ($query) {
                    $query->select('id', 'room_id', 'user_id')
                        ->with(['user:id,name']);
                },
                'media'
            ])
        );
    }

    public function update(Room $room, Request $request)
    {
        $room->update($request->only(['name', 'description', 'total_pax', 'equipments', 'status']));

        $this->handleFileUpload($request, $room);

        return response()->json(['message' => 'Success']);
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json(['message' => 'Success']);
    }

    private function handleFileUpload(Request $request, Room $room)
    {
        if ($request->hasFile('image_file')) {
            foreach ($request->file('image_file') as $photo) {
                $room->addMedia($photo)->toMediaCollection('rooms');
            }
        }
    }
}