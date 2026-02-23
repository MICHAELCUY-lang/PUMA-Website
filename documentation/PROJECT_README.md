# PUMA Informatics Website

Official website for PUMA Informatics at President University.

## Project Overview

This is a Monorepo containing both the Frontend and Backend services for the PUMA Website.

- **Frontend**: Vue.js 3 + Vite + TypeScript (Located in `PUMA-Website/`)
- **Backend**: Laravel 11 + PHP (Located in `PUMA-Backend/`)

## Folder Structure

```
c:\Users\mbrya\PUMA_FIX\PUMA-Website\
├── PUMA-Website/       # Frontend Application (Vue.js)
├── PUMA-Backend/       # Backend Application (Laravel)
├── documentation/      # Documentation files
│   ├── FRONTEND_README.md
│   ├── BACKEND_README.md
│   ├── MAINTENANCE_SCRIPTS_README.md
│   └── ...
└── README.md           # This file
```

## Getting Started

To run the full application, you need to run both the Frontend and Backend servers concurrently.

### 1. Start Backend (Laravel)

Open a terminal and navigate to the backend folder:
```bash
cd PUMA-Backend
php artisan serve
```
*Runs on: http://localhost:8000*

### 2. Start Frontend (Vue.js)

Open a **separate** terminal and navigate to the frontend folder:
```bash
cd PUMA-Website
npm run dev
```
*Runs on: http://localhost:5173*

## Maintenance & Tools

The `PUMA-Backend/maintenance_scripts/` directory contains useful PHP scripts for:
- Checking database integrity
- Fixing schema issues
- Debugging content visibility (Events/Members)

See `documentation/MAINTENANCE_SCRIPTS_README.md` for more details.

## Documentation

- [**FULL INSTALLATION & DEPLOYMENT GUIDE**](documentation/FULL_GUIDE.md)
- [Frontend Documentation](documentation/FRONTEND_README.md)
- [Backend Documentation](documentation/BACKEND_README.md)
- [API Documentation](documentation/API_DOCUMENTATION_EVENTS.md)
 
