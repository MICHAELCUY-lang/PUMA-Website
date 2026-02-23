# PUMA Frontend Documentation

## Tech Stack
- **Framework**: Vue.js 3 (Composition API)
- **Build Tool**: Vite
- **Language**: TypeScript
- **Styling**: Tailwind CSS
- **State Management**: Pinia (if used in future) / Composables
- **Animation**: AOS (Animate On Scroll)

## Key Features & Dynamic Content
The frontend is now fully connected to the Laravel Backend.

### 1. Dynamic Home Page (`Home.vue`)
- **Banners**: Fetches **active** banners from the backend. The hero section background updates dynamically based on the uploaded banner in Admin Panel.
- **Events**: Displays 'Our Events' section, fetching both completed and upcoming events.

### 2. Events / Timeline (`Timeline.vue`)
- Accessed via **Events** in Navbar.
- Displays a chronological timeline of events.
- **Dynamic Data**: All events are fetched from the `/api/events` endpoint.
- **Status Support**:
  - `Completed`: Shows as "Closed Case" (Dark styling)
  - `Upcoming`: Shows as "Pending Lead" (Light/Gray styling)

### 3. News (`News.vue`)
- Fetches and displays news articles from the backend.
- Supports filtering by category and "Featured" status.

## Development

### Setup
```bash
cd PUMA-Website
npm install
```

### Run Dev Server
```bash
npm run dev
```

### Directory Structure
- `src/components/views/`: Main page views (Home, Timeline, About).
- `src/components/views/admin/`: Admin Dashboard components.
- `src/composables/`: Logic for API calls (`useEvents`, `useNews`, `useBanners`).
- `src/assets/`: Static assets (Logos, Icons).
 
