# Events API Documentation

## Base URL

```
http://your-domain.com/api/events
```

## Endpoints

### 1. Get All Events

**GET** `/api/events`

Returns all events with optional filtering by status.

**Query Parameters:**

-   `status` (optional): Filter by event status (`completed` or `upcoming`)

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Regenetics 2024/2025",
            "date": "1 September 2024",
            "description": "The PUMA Informatics Regeneration...",
            "images": [
                "https://example.com/image1.jpg",
                "https://example.com/image2.jpg"
            ],
            "status": "completed",
            "location": "Main Hall",
            "category": "Recruitment",
            "content": "Detailed content..."
        }
    ]
}
```

### 2. Get Completed Events Only

**GET** `/api/events/completed`

Returns only events with status "completed", ordered by event date (most recent first).

**Response:** Same structure as "Get All Events"

### 3. Get Upcoming Events Only

**GET** `/api/events/upcoming`

Returns only events with status "upcoming", ordered by event date (soonest first).

**Response:** Same structure as "Get All Events"

### 4. Get Single Event

**GET** `/api/events/{id}`

Returns details of a specific event including cabinet information.

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Regenetics 2024/2025",
    "date": "1 September 2024",
    "description": "The PUMA Informatics Regeneration...",
    "images": [
      "https://example.com/image1.jpg"
    ],
    "status": "completed",
    "location": "Main Hall",
    "category": "Recruitment",
    "content": "Detailed content...",
    "cabinet": {
      "id": 1,
      "name": "Cabinet 2024/2025",
      ...
    }
  }
}
```

### 5. Create New Event

**POST** `/api/events`

Creates a new event.

**Request Body:**

```json
{
    "title": "Event Title",
    "description": "Event description",
    "event_date": "2024-09-01",
    "event_date_end": "2024-09-02",
    "location": "Main Hall",
    "cabinet_id": 1,
    "status": "upcoming",
    "content": "Detailed content",
    "category": "Recruitment",
    "images": [
        "https://example.com/image1.jpg",
        "https://example.com/image2.jpg"
    ]
}
```

**Validation Rules:**

-   `title`: required, string, max 255 characters
-   `description`: required, string
-   `event_date`: required, valid date
-   `event_date_end`: optional, valid date, must be after or equal to event_date
-   `location`: optional, string, max 255 characters
-   `cabinet_id`: optional, must exist in cabinets table
-   `status`: required, must be "completed" or "upcoming"
-   `content`: optional, string
-   `category`: optional, string, max 255 characters
-   `images`: optional, array of strings (URLs or file paths)

**Response:**

```json
{
  "success": true,
  "message": "Event created successfully",
  "data": {
    "id": 1,
    "title": "Event Title",
    ...
  }
}
```

### 6. Update Event

**PUT** `/api/events/{id}`

Updates an existing event.

**Request Body:** Same as Create Event (all fields are optional with "sometimes" validation)

**Response:**

```json
{
  "success": true,
  "message": "Event updated successfully",
  "data": {
    "id": 1,
    "title": "Updated Event Title",
    ...
  }
}
```

### 7. Delete Event

**DELETE** `/api/events/{id}`

Deletes an event and its associated images.

**Response:**

```json
{
    "success": true,
    "message": "Event deleted successfully"
}
```

## Frontend Integration Example

### Fetching Events in Vue Component

```typescript
// In your Events.vue component
import { ref, onMounted } from "vue";

const events = ref([]);
const loading = ref(false);
const error = ref(null);

const fetchCompletedEvents = async () => {
    loading.value = true;
    try {
        const response = await fetch(
            "http://your-domain.com/api/events/completed"
        );
        const data = await response.json();

        if (data.success) {
            events.value = data.data;
        }
    } catch (err) {
        error.value = err.message;
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchCompletedEvents();
});
```

### Using Axios

```typescript
import axios from "axios";

const api = axios.create({
    baseURL: "http://your-domain.com/api",
});

// Get completed events
const { data } = await api.get("/events/completed");

// Get single event
const { data: eventData } = await api.get(`/events/${eventId}`);

// Create event
const { data: newEvent } = await api.post("/events", {
    title: "New Event",
    description: "Description",
    event_date: "2024-09-01",
    status: "upcoming",
});
```

## Notes

-   All responses follow the same structure with `success` and `data` fields
-   Dates are formatted as "j F Y" (e.g., "1 September 2024") in the response
-   Images are returned as an array of URLs
-   The API uses Laravel's route model binding for the `{event}` parameter
-   CORS should be configured in Laravel for frontend access
