<?php

namespace App\Http\Controllers;

use App\Models\{Booking, WorkingDay, Room};
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function all(Request $request)
    {
        $isStaff = filter_var($request->input('staff'), FILTER_VALIDATE_BOOLEAN);

        $booking = Booking::query()
            ->with('room', 'user')
            ->when($request->room, function ($query, $room) {
                $query->where('room_id', $room);
            })
            ->when($isStaff, function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->when($request->date, function ($query, $date) {
                $query->where('booked_at', $date);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('booked_at')
            ->orderBy('booked_from')
            ->get();
    
        return response()->json($booking);
    }

    public function store(Request $request)
    {
        $booking = Booking::create([
            'room_id' => $request->room,
            'user_id' => $request->user()->id,
            'booked_at' => $request->booking_date,
            'status' => 'Active',
            'booked_from' => $request->from_time,
            'booked_to' => $request->to_time,
            'meeting_name' => $request->meeting_name,
            'request' => $request->description,
            'description' => $request->meeting_name,
        ]);

        return response()->json(['message' => 'Success']);
    }

    public function getDisabledDates(Request $request)
    {
        $roomId = $request->room_id;
    
        // Validate room ID
        if (!is_numeric($roomId) || $roomId <= 0) {
            return response()->json([
                'error' => 'Invalid or missing room ID.',
            ], 400);
        }
    
        $rangeStart = Carbon::today();
        $rangeEnd = Carbon::today()->addMonths(2);
    
        // Log for debugging
        \Log::info('Fetching disabled dates for room ID:', ['room_id' => $roomId]);
    
        // Fetch bookings within the range
        $bookings = Booking::query()
            ->where('room_id', $roomId)
            ->whereBetween('booked_at', [$rangeStart, $rangeEnd])
            ->select('booked_at', 'booked_from', 'booked_to')
            ->orderBy('booked_at')
            ->orderBy('booked_from')
            ->get();
    
        $groupedByDate = $bookings->groupBy('booked_at');
    
        $disabledDates = [];
    
        for ($date = $rangeStart->copy(); $date->lte($rangeEnd); $date->addDay()) {
            $currentDateStr = $date->format('Y-m-d');
            $dayBookings = $groupedByDate->get($currentDateStr, collect());
            
            if ($this->isDayFullyBooked($dayBookings, $currentDateStr)) {
                $disabledDates[] = $currentDateStr;
            }
        }
    
        return response()->json([
            'disabled_dates' => $disabledDates,
        ]);
    }
    
    private function isDayFullyBooked($dayBookings, $dateString)
    {
        if ($dayBookings->isEmpty()) {
            return false;
        }

        $setting = WorkingDay::first();
        $workingStart = Carbon::parse("{$dateString} {$setting->start_at}");
        $workingEnd = Carbon::parse("{$dateString} {$setting->end_at}");

        $intervals = $dayBookings->map(function ($b) use ($dateString) {
            return [
                Carbon::parse("{$dateString} {$b->booked_from}"),
                Carbon::parse("{$dateString} {$b->booked_to}"),
            ];
        })->toArray();

        usort($intervals, function ($a, $b) {
            return $a[0]->greaterThan($b[0]) ? 1 : -1;
        });

        $pointer = $workingStart->copy();
    
        foreach ($intervals as [$intervalStart, $intervalEnd]) {
            if ($intervalStart->gt($pointer)) {
                return false;
            }

            $pointer = $pointer->max($intervalEnd);

            if ($pointer->gte($workingEnd)) {
                return true;
            }
        }

        return false;
    }

    public function show(Booking $booking)
    {
        $data = $booking->load('room.media', 'user');

        return response()->json($data);
    }

    public function cancel(Booking $booking)
    {
        $booking->status = 'Cancelled';
        $booking->save();

        return response()->json(['message' => 'Success']);
    }

    public function getTopThreeBookingForToday(Room $room)
    {
        $now = now();
    
        // Load all bookings for the room with users
        $room->load([
            'bookings' => function ($query) {
                $query->where('status', 'Active')
                    ->with('user') // Include user for each booking
                    ->orderBy('booked_at')
                    ->orderBy('booked_from');
            }
        ]);
    
        $bookings = $room->bookings;
    
        // Past Booking
        $pastBooking = $bookings
            ->filter(fn($b) => 
                ($b->booked_at < $now->toDateString()) || 
                ($b->booked_at === $now->toDateString() && $b->booked_to < $now->format('H:i:s'))
            )
            ->sortByDesc(fn($b) => [$b->booked_at, $b->booked_to])
            ->first();
    
        // Present Booking
        $presentBooking = $bookings
            ->filter(fn($b) => 
                $b->booked_at === $now->toDateString() && 
                $b->booked_from <= $now->format('H:i:s') && 
                $b->booked_to >= $now->format('H:i:s')
            )
            ->first();
    
        // Future Booking
        $futureBooking = $bookings
            ->filter(fn($b) => 
                ($b->booked_at === $now->toDateString() && $b->booked_from > $now->format('H:i:s')) ||
                $b->booked_at > $now->toDateString()
            )
            ->sortBy(fn($b) => [$b->booked_at, $b->booked_from])
            ->first();
    
        // Add placeholders for missing bookings with empty state content
        if (!$pastBooking) {
            $pastBooking = (object) [
                'id' => null,
                'meeting_name' => 'No Past Meetings',
                'description' => 'There are no past meetings for this room.',
                'booked_at' => null,
                'booked_from' => null,
                'booked_to' => null,
                'user' => null,
            ];
        }
    
        if (!$presentBooking) {
            $presentBooking = (object) [
                'id' => null,
                'meeting_name' => 'No Current Meetings',
                'description' => 'There is no ongoing meeting for this room.',
                'booked_at' => $now->toDateString(),
                'booked_from' => null,
                'booked_to' => null,
                'user' => null,
            ];
        }
    
        if (!$futureBooking) {
            $futureBooking = (object) [
                'id' => null,
                'meeting_name' => 'No Upcoming Meetings',
                'description' => 'There are no upcoming meetings scheduled for this room.',
                'booked_at' => $now->addDay()->toDateString(),
                'booked_from' => null,
                'booked_to' => null,
                'user' => null,
            ];
        }
    
        // Create the sequence
        $sequence = collect([
            [
                'type' => 'past',
                'booking' => $pastBooking,
            ],
            [
                'type' => 'present',
                'booking' => $presentBooking,
            ],
            [
                'type' => 'future',
                'booking' => $futureBooking,
            ],
        ]);
    
        // Return room details along with the sequence
        return [
            'room' => $room,
            'bookings' => $sequence,
        ];
    }
}
