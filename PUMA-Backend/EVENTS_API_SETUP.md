# Events API Setup Complete! 🎉

## What Was Created

### 1. **EventController.php** - Complete API Controller

-   `index()` - Get all events with optional status filter
-   `completed()` - Get only completed events
-   `upcoming()` - Get only upcoming events
-   `show($id)` - Get single event details
-   `store()` - Create new event
-   `update($id)` - Update existing event
-   `destroy($id)` - Delete event

### 2. **API Routes** (routes/api.php)

-   All endpoints are prefixed with `/api/events`
-   RESTful route structure

### 3. **Request Validation**

-   `StoreEventRequest.php` - Validation for creating events
-   `UpdateEventRequest.php` - Validation for updating events

### 4. **Model Updates**

-   Updated `EventImage` model with fillable fields and relationships

### 5. **Bootstrap Configuration**

-   Added API routes to `bootstrap/app.php`

## API Endpoints Available

```
GET    /api/events              - Get all events
GET    /api/events/completed    - Get completed events
GET    /api/events/upcoming     - Get upcoming events
GET    /api/events/{id}         - Get single event
POST   /api/events              - Create event
PUT    /api/events/{id}         - Update event
DELETE /api/events/{id}         - Delete event
```

## Next Steps

### 1. Test the API

Start your Laravel server:

```bash
php artisan serve
```

Test with a browser or API client:

```
http://localhost:8000/api/events/completed
```

### 2. Update Your Vue Component

Replace the hardcoded events array with API calls:

```typescript
// In Events.vue
import { ref, onMounted } from "vue";
import axios from "axios";

const events = ref([]);
const loading = ref(false);

const fetchEvents = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            "http://localhost:8000/api/events/completed"
        );
        if (response.data.success) {
            events.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching events:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchEvents();
});
```

### 3. Configure CORS (if needed)

If your frontend is on a different port/domain, enable CORS in Laravel:

```bash
php artisan config:publish cors
```

Then update `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:5173'], // Your Vue dev server
```

### 4. Add Data to Database

You can create events via:

-   Artisan tinker
-   Database seeder
-   API POST request
-   Laravel admin panel (if you have one)

Example with tinker:

```bash
php artisan tinker
```

```php
App\Models\Event::create([
    'title' => 'Regenetics 2024/2025',
    'description' => 'The PUMA Informatics Regeneration...',
    'event_date' => '2024-09-01',
    'status' => 'completed',
    'location' => 'Main Hall',
    'category' => 'Recruitment',
]);
```

## Response Format

All API responses follow this structure:

```json
{
  "success": true,
  "data": [...] or {...},
  "message": "Optional message for create/update/delete"
}
```

## Image Handling

The current implementation stores image URLs as strings. You can:

-   Store URLs to external images
-   Store file paths to uploaded images
-   Integrate with Laravel storage for file uploads

For file uploads, you'll need to add multipart/form-data handling to the controller.

## Need Help?

Check the `API_DOCUMENTATION_EVENTS.md` file for complete API documentation with examples.
