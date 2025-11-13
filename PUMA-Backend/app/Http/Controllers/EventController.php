<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of all events.
     * Optionally filter by status (completed/upcoming).
     */
    public function index(Request $request)
    {
        $query = Event::with('images', 'cabinet');

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Order by event date descending (most recent first)
        $events = $query->orderBy('event_date', 'desc')->get();

        // Transform the data to match frontend structure
        $transformedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'date' => \Carbon\Carbon::parse($event->event_date)->format('j F Y'),
                'description' => $event->description,
                'images' => $event->images->pluck('image_url')->toArray(),
                'status' => $event->status,
                'location' => $event->location,
                'category' => $event->category,
                'content' => $event->content,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedEvents
        ]);
    }

    /**
     * Get only completed events.
     */
    public function completed()
    {
        $events = Event::with('images', 'cabinet')
            ->where('status', 'completed')
            ->orderBy('event_date', 'desc')
            ->get();

        $transformedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'date' => \Carbon\Carbon::parse($event->event_date)->format('j F Y'),
                'description' => $event->description,
                'images' => $event->images->pluck('image_url')->toArray(),
                'status' => $event->status,
                'location' => $event->location,
                'category' => $event->category,
                'content' => $event->content,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedEvents
        ]);
    }

    /**
     * Get only upcoming events.
     */
    public function upcoming()
    {
        $events = Event::with('images', 'cabinet')
            ->where('status', 'upcoming')
            ->orderBy('event_date', 'asc')
            ->get();

        $transformedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'date' => \Carbon\Carbon::parse($event->event_date)->format('j F Y'),
                'description' => $event->description,
                'images' => $event->images->pluck('image_url')->toArray(),
                'status' => $event->status,
                'location' => $event->location,
                'category' => $event->category,
                'content' => $event->content,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedEvents
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());

        $imageUrls = [];

        // Handle URL strings
        if ($request->has('images')) {
            $images = is_array($request->images) ? $request->images : [$request->images];
            foreach ($images as $image) {
                if (!empty($image) && filter_var($image, FILTER_VALIDATE_URL)) {
                    $imageUrls[] = $image;
                }
            }
        }

        // Handle file uploads
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $path = $file->store('events', 'public');
                $imageUrls[] = asset('storage/' . $path);
            }
        }

        // Save all images
        foreach ($imageUrls as $url) {
            $event->images()->create([
                'image_url' => $url
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event->load('images')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('images', 'cabinet');

        $transformedEvent = [
            'id' => $event->id,
            'title' => $event->title,
            'date' => \Carbon\Carbon::parse($event->event_date)->format('j F Y'),
            'description' => $event->description,
            'images' => $event->images->pluck('image_url')->toArray(),
            'status' => $event->status,
            'location' => $event->location,
            'category' => $event->category,
            'content' => $event->content,
            'cabinet' => $event->cabinet,
        ];

        return response()->json([
            'success' => true,
            'data' => $transformedEvent
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $event->update($request->validated());

        // Handle image updates if provided
        if ($request->has('images') || $request->hasFile('image_files')) {
            // Delete old images
            $event->images()->delete();

            $imageUrls = [];

            // Handle URL strings
            if ($request->has('images')) {
                $images = is_array($request->images) ? $request->images : [$request->images];
                foreach ($images as $image) {
                    if (!empty($image) && filter_var($image, FILTER_VALIDATE_URL)) {
                        $imageUrls[] = $image;
                    }
                }
            }

            // Handle file uploads
            if ($request->hasFile('image_files')) {
                foreach ($request->file('image_files') as $file) {
                    $path = $file->store('events', 'public');
                    $imageUrls[] = asset('storage/' . $path);
                }
            }

            // Save all images
            foreach ($imageUrls as $url) {
                $event->images()->create([
                    'image_url' => $url
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event->load('images')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->images()->delete();
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }
}
