<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\{Booking, Room, WorkingDay};
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class ChatBotController extends Controller
{
    public function chat(Request $request)
    {
        $userInput = $request->input('message');
        $userId = $request->user()->id;
    
        try {
            $data = $this->getContextFromRedis($userId);
            $bookingContext = $data['context'];
            $messages = $data['messages'];
    
            // If context exists, continue the booking flow
            if ($bookingContext) {
                $response = $this->handleBookingFlowWithGroq($bookingContext, $userInput, $userId);
    
                // Append user input and bot response to messages
                $messages[] = ['sender' => 'user', 'text' => $userInput];
                $messages[] = ['sender' => 'bot', 'text' => $response->getData()['reply']];
    
                $this->storeContextInRedis($userId, $bookingContext, $messages);
    
                return $response;
            }
    
            // Classify user intent
            $intentData = $this->classifyIntent($userInput);
            if (!$intentData) {
                $reply = $this->sendToGroq("I couldn't understand the user's intent. Please clarify.");
                $messages[] = ['sender' => 'user', 'text' => $userInput];
                $messages[] = ['sender' => 'bot', 'text' => $reply];
                $this->storeContextInRedis($userId, null, $messages);
    
                return response()->json(['reply' => $reply]);
            }
    
            $intent = $intentData['intent'];
            // Process intent and store messages...
            $messages[] = ['sender' => 'user', 'text' => $userInput];
            $reply = $this->handleIntent($intent, $userId, $messages); // Handle intent logic separately
            $messages[] = ['sender' => 'bot', 'text' => $reply];
    
            $this->storeContextInRedis($userId, null, $messages);
            return response()->json(['reply' => $reply]);
    
        } catch (\Exception $e) {
            \Log::error('Error in Chat Function:', ['exception' => $e]);
            return response()->json(['reply' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    private function classifyIntent($input)
    {
        $keywords = [
            'list', 'meetings', 'meeting', 'today', 'booking', 'bookings', 'my', 'many', 'tomorrow', 'yesterday', 'book', 'hi', 'hello', 'hey', 'how'
        ];
    
        $words = explode(' ', strtolower($input));
        $correctedWords = array_map(function ($word) use ($keywords) {
            return $this->correctWord($word, $keywords);
        }, $words);
        $correctedInput = implode(' ', $correctedWords);
    
        \Log::info('Corrected Input:', ['correctedInput' => $correctedInput]);
    
        $normalizedInput = preg_replace('/\b(can|you|please|it)\b/', '', $correctedInput);
        $normalizedInput = trim(preg_replace('/\s+/', ' ', $normalizedInput));
    
        \Log::info('Normalized Input:', ['normalizedInput' => $normalizedInput]);
    
        $isMyMeeting = strpos($normalizedInput, 'my') !== false;
        $isToday = strpos($normalizedInput, 'today') !== false;
    
        \Log::info('Flags:', ['isMyMeeting' => $isMyMeeting, 'isToday' => $isToday]);
    
        if (strpos($normalizedInput, 'how many') !== false && strpos($normalizedInput, 'booking') !== false) {
            return [
                'intent' => 'count_bookings_today',
                'filterByDate' => true,
            ];
        }

        if (strpos($normalizedInput, 'list') !== false && strpos($normalizedInput, 'my meeting') !== false) {
            return [
                'intent' => 'list_my_meetings',
                'filterByDate' => false,
            ];
        }
    
        if (strpos($normalizedInput, 'book a meeting') !== false || strpos($normalizedInput, 'book meeting') !== false) {
            return [
                'intent' => 'start_booking',
                'filterByDate' => false,
            ];
        }
    
        $hasListKeyword = strpos($normalizedInput, 'list') !== false;
        $hasMeetingOrBooking = strpos($normalizedInput, 'meeting') !== false || strpos($normalizedInput, 'bookings') !== false;
    
        if ($hasListKeyword && $hasMeetingOrBooking) {
            return [
                'intent' => $isMyMeeting ? 'myself' : 'list_all',
                'filterByDate' => $isToday,
            ];
        }

        if (strpos($normalizedInput, 'hi') !== false || strpos($normalizedInput, 'hello') !== false || strpos($normalizedInput, 'hey') !== false || strpos($normalizedInput, 'how are you') !== false) {
            return [
                'intent' => 'spontaneous',
                'filterByDate' => false,
            ];
        }
    
        return null;
    }

    private function correctWord($word, $keywords)
    {
        $highestSimilarity = 0;
        $bestMatch = $word;

        foreach ($keywords as $keyword) {
            similar_text($word, $keyword, $similarity);
            if ($similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $bestMatch = $keyword;
            }
        }

        return $highestSimilarity > 80 ? $bestMatch : $word;
    }

    private function countBookingsForToday()
    {
        $today = Carbon::now()->toDateString();
        $bookingCount = Booking::whereDate('booked_at', $today)->count();

        $prompt = $bookingCount === 0
            ? "There are no bookings scheduled for today."
            : "There are {$bookingCount} bookings scheduled for today.";

        return $this->sendToGroq($prompt);
    }

    private function getGroqToSummarizeBookings($type, $userId = null, $filterByDate = false)
    {
        $query = Booking::query();

        if ($type === 'myself') {
            $query->where('user_id', $userId);
        }

        if ($filterByDate) {
            $query->whereDate('booked_at', Carbon::now()->toDateString());
        }

        $bookings = $query->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'reply' => $filterByDate
                    ? "There are no meetings scheduled for today."
                    : "There are no meetings scheduled."
            ]);
        }

        $formattedData = $bookings->map(function ($booking) use ($type, $filterByDate) {
            $data = [
                'Meeting Name' => $booking->meeting_name,
                'Room' => $booking->room->name,
                'Time' => Carbon::parse($booking->booked_from)->format('g:i A') . ' - ' . Carbon::parse($booking->booked_to)->format('g:i A'),
            ];
    
            if (!$filterByDate) {
                $data['Date'] = Carbon::parse($booking->booked_at)->format('l, d/m/Y');
            }
    
            if ($type !== 'myself') {
                $data['Booked By'] = $booking->user->name;
            }
    
            return $data;
        });
    
        $prompt = "Here is the meeting information:\n\n";
        foreach ($formattedData as $index => $booking) {
            $prompt .= ($index + 1) . ". Meeting Name: {$booking['Meeting Name']},\nTime: {$booking['Time']},\nRoom: {$booking['Room']}";
            if (isset($booking['Date'])) {
                $prompt .= "\nDate: {$booking['Date']}";
            }
            if (isset($booking['Booked By'])) {
                $prompt .= "\nBooked By: {$booking['Booked By']}";
            }
            $prompt .= "\n\n";
        }

        $prompt .= "\nSummarize the booking details in a clear and concise manner with proper HTML code style with table. Focus only on the booking details. Just exclude word Table and HTML";

        $apiResponse = $this->sendToGroq($prompt);

        return response()->json([
            'reply' => $apiResponse, // Processed by Groq/OpenAI
            'bookings' => $formattedData // Raw structured data for UI
        ]);
    }

    private function handleBookingFlowWithGroq($context, $userInput, $userId)
    {
        $step = $context['step'] ?? null;
        $data = $context['data'] ?? [];
    
        if (!$step) {
            return response()->json(['reply' => $this->sendToGroq(
                "It seems the booking process was interrupted. Let's start over. Please provide the room name to begin booking."
            )]);
        }
    
        switch ($step) {
            case 'choose_room':
                // Fetch available rooms
                $availableRooms = Room::where('status', 'Available')->get();
            
                // Validate user input against available room names
                $room = $availableRooms->firstWhere('name', $userInput);
            
                if (!$room) {
                    // If room is invalid or unavailable, prompt again
                    $roomList = $availableRooms->map(fn($room) => "- {$room->name}")->join("\n");
                    return response()->json(['reply' => $this->sendToGroq(
                        "The room '{$userInput}' is either invalid or unavailable. Here are the available rooms:\n\n{$roomList}\n\nPlease choose a valid room by providing the room name."
                    )]);
                }
            
                // If valid room is found, proceed to the next step
                $data['room_id'] = $room->id;
                $data['room_name'] = $room->name;
                $this->storeContextInRedis($userId, ['step' => 'meeting_name', 'data' => $data]);
            
                return response()->json(['reply' => $this->sendToGroq(
                    "Room '{$room->name}' has been selected. Please provide the meeting name. Don't suggest weird meeting name. Just be professional"
                )]);
    
            case 'meeting_name':
                $data['meeting_name'] = $userInput;
                $this->storeContextInRedis($userId, ['step' => 'meeting_description', 'data' => $data]);
    
                return response()->json(['reply' => $this->sendToGroq(
                    "The meeting name '{$userInput}' has been noted. Please provide a brief meeting description. Don't suggest weird meeting name. Just be professional"
                )]);
    
            case 'meeting_description':
                $data['meeting_description'] = $userInput;
                $this->storeContextInRedis($userId, ['step' => 'meeting_date', 'data' => $data]);
    
                return response()->json(['reply' => $this->sendToGroq(
                    "The meeting description '{$userInput}' has been noted. Please provide the meeting date in the format 'YYYY-MM-DD'. Don't suggest weird meeting description. Just be professional"
                )]);
    
                case 'meeting_date':
                    try {
                        // Attempt to parse the user input using Carbon's natural language capabilities
                        $date = Carbon::parse($userInput);
                
                        // Ensure the parsed date is valid and not in the past
                        if ($date->isPast()) {
                            return response()->json(['reply' => $this->sendToGroq(
                                "The date '{$userInput}' is in the past. Please provide a future date."
                            )]);
                        }
                
                        // Check if the date is fully booked
                        $dayBookings = Booking::where('room_id', $context['data']['room_id'])
                            ->whereDate('booked_at', $date->toDateString())
                            ->get();
                
                        if ($this->isDayFullyBooked($dayBookings, $date->toDateString())) {
                            return response()->json(['reply' => $this->sendToGroq(
                                "The selected date '{$date->format('l, d/m/Y')}' is fully booked. Please choose another date."
                            )]);
                        }
                
                        // List available time slots for the day
                        $workingDay = WorkingDay::first();
                        $workingStart = Carbon::parse("{$date->toDateString()} {$workingDay->start_at}");
                        $workingEnd = Carbon::parse("{$date->toDateString()} {$workingDay->end_at}");
                
                        $availableSlots = $this->getAvailableTimeSlots($dayBookings, $workingStart, $workingEnd);
                
                        if (empty($availableSlots)) {
                            return response()->json(['reply' => $this->sendToGroq(
                                "No available time slots on '{$date->format('l, d/m/Y')}'. Please choose another date."
                            )]);
                        }
                
                        $formattedSlots = collect($availableSlots)->map(fn($slot) =>
                            $slot[0]->format('g:i A') . ' - ' . $slot[1]->format('g:i A')
                        )->implode(", ");
                
                        $data['meeting_date'] = $date->toDateString();
                        $this->storeContextInRedis($userId, ['step' => 'meeting_time', 'data' => $data]);
                
                        return response()->json(['reply' => $this->sendToGroq(
                            "The selected date '{$date->format('l, d/m/Y')}' is available. Here are the time slots:\n\n{$formattedSlots}\n\nPlease choose a time slot."
                        )]);
                    } catch (\Exception $e) {
                        // Handle invalid or unrecognized date input gracefully
                        return response()->json(['reply' => $this->sendToGroq(
                            "The date '{$userInput}' is invalid or not recognized. Please provide a valid date, such as 'YYYY-MM-DD', 'Today', 'Tomorrow', 'Monday next week', or 'Next month'."
                        )]);
                    }
                    case 'meeting_time':
                        $times = explode('till', strtolower($userInput));
                        if (count($times) !== 2) {
                            return response()->json(['reply' => $this->sendToGroq(
                                "The time '{$userInput}' is invalid. Please provide the meeting time in the format '10am till 11am'."
                            )]);
                        }
                    
                        try {
                            $startTime = Carbon::parse(trim($times[0]));
                            $endTime = Carbon::parse(trim($times[1]));
                    
                            if ($startTime->gte($endTime)) {
                                return response()->json(['reply' => $this->sendToGroq(
                                    "The end time '{$endTime->format('g:i A')}' is before or equal to the start time '{$startTime->format('g:i A')}'. Please provide a valid time range."
                                )]);
                            }
                    
                            // Check for overlapping bookings
                            $existingBookings = Booking::where('room_id', $context['data']['room_id'])
                                ->whereDate('booked_at', $context['data']['meeting_date'])
                                ->get();
                    
                            foreach ($existingBookings as $booking) {
                                $bookedStart = Carbon::parse("{$booking->booked_at} {$booking->booked_from}");
                                $bookedEnd = Carbon::parse("{$booking->booked_at} {$booking->booked_to}");
                    
                                if ($startTime->between($bookedStart, $bookedEnd) || $endTime->between($bookedStart, $bookedEnd)) {
                                    return response()->json(['reply' => $this->sendToGroq(
                                        "The selected time '{$startTime->format('g:i A')} - {$endTime->format('g:i A')}' overlaps with an existing booking. Please choose another time."
                                    )]);
                                }
                            }
                    
                            $data['start_time'] = $startTime->format('H:i');
                            $data['end_time'] = $endTime->format('H:i');
                            $this->storeContextInRedis($userId, ['step' => 'special_request', 'data' => $data]);
                    
                            return response()->json(['reply' => $this->sendToGroq(
                                "The meeting time from '{$startTime->format('g:i A')}' to '{$endTime->format('g:i A')}' has been noted. Do you have any special requests for this meeting?"
                            )]);
                        } catch (\Exception $e) {
                            return response()->json(['reply' => $this->sendToGroq(
                                "The time '{$userInput}' is invalid. Please provide the meeting time in the format '10am till 11am'."
                            )]);
                        }
    
                case 'special_request':
                    $data['special_request'] = strtolower($userInput) === 'no special requests' || empty(trim($userInput))
                        ? 'None'
                        : $userInput;
                
                    \Log::info('Special Request Data:', ['data' => $data]);
                    $this->storeContextInRedis($userId, null);
                
                    // Call the createBooking method
                    try {
                        $response = $this->createBooking($data, $userId);
                    } catch (\Exception $e) {
                        \Log::error('Error Creating Booking:', ['exception' => $e]);
                        return response()->json(['reply' => 'An error occurred while creating the booking. Please try again later.'], 500);
                    }

                    $date = Carbon::parse($response->booked_at)->format('l, d/m/Y');
                    $time = Carbon::parse($response->booked_from)->format('g:i A') . ' - ' . Carbon::parse($response->booked_to)->format('g:i A');
                
                    return response()->json(['reply' => $this->sendToGroq(
                        "Response this. (Your meeting has been successfully booked with the following details:\n" .
                        "- Room: {$response->room->name}\n" .
                        "- Meeting Name: {$response->meeting_name}\n" .
                        "- Meeting Description: {$response->description}\n" .
                        "- Date: {$date}\n" .
                        "- Time: {$time}\n" .
                        "- Special Request: {$response->request}\n\n" .

                        "If you need to make any changes, please let me know). And please make it way and listing by html."
                    )]);
    
            default:
                return response()->json(['reply' => $this->sendToGroq(
                    "An error occurred during the booking process. Please start over by providing the room name."
                )]);
        }
    }

    private function createBooking($data, $userId)
    {
        $data = Booking::create([
            'user_id' => $userId,
            'room_id' => $data['room_id'],
            'meeting_name' => $data['meeting_name'],
            'description' => $data['meeting_description'],
            'booked_at' => $data['meeting_date'],
            'booked_from' => $data['start_time'],
            'booked_to' => $data['end_time'],
            'status' => 'Active',
            'request' => $data['special_request'],
        ]);

        return $data->load('room', 'user');
    }

    private function sendToGroq($prompt)
    {
        $response = Http::retry(3, 5000)
            ->withHeaders(['Authorization' => 'Bearer ' . config('services.hugging_face.key')])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama3-8b-8192',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant specialized in booking-related tasks. Respond in a professional and friendly tone.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if ($response->successful()) {
            return json_decode($response->body(), true)['choices'][0]['message']['content'] ?? 'Could not summarize the data.';
        }

        return 'Unable to process your request at the moment. Please try again later.';
    }

    private function storeContextInRedis($key, $context, $messages = null)
    {
        Redis::set("chatbot:context:{$key}", json_encode($context));
    
        if (!is_null($messages)) {
            Redis::set("chatbot:messages:{$key}", json_encode($messages));
        }
    
        if (is_null($context)) {
            Redis::del("chatbot:context:{$key}");
            Redis::del("chatbot:messages:{$key}");
        } else {
            Redis::expire("chatbot:context:{$key}", 3600);
            Redis::expire("chatbot:messages:{$key}", 3600);
        }
    }

    private function getContextFromRedis($key)
    {
        $context = Redis::get("chatbot:context:{$key}");
        $messages = Redis::get("chatbot:messages:{$key}");
    
        return [
            'context' => $context ? json_decode($context, true) : null,
            'messages' => $messages ? json_decode($messages, true) : [],
        ];
    }

    private function getAvailableTimeSlots($dayBookings, $workingStart, $workingEnd)
    {
        $intervals = $dayBookings->map(function ($booking) use ($workingStart, $workingEnd) {
            return [
                Carbon::parse("{$booking->booked_at} {$booking->booked_from}")->max($workingStart),
                Carbon::parse("{$booking->booked_at} {$booking->booked_to}")->min($workingEnd),
            ];
        })->toArray();

        usort($intervals, function ($a, $b) {
            return $a[0]->greaterThan($b[0]) ? 1 : -1;
        });

        $availableSlots = [];
        $pointer = $workingStart->copy();

        foreach ($intervals as [$intervalStart, $intervalEnd]) {
            if ($intervalStart->gt($pointer)) {
                $availableSlots[] = [$pointer->copy(), $intervalStart->copy()];
            }
            $pointer = $pointer->max($intervalEnd);
        }

        if ($pointer->lt($workingEnd)) {
            $availableSlots[] = [$pointer->copy(), $workingEnd->copy()];
        }

        return $availableSlots;
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

    public function clearMessage(Request $request)
    {
        $this->storeContextInRedis($request->user()->id, null);
    }
}