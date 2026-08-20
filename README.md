# ScanTech Dispatch Assessment — Starter Codebase

This repository is the supplied starting point for the **Backend Assessment A — Laravel + Livewire — Dispatch Management System**.

The application is intentionally imperfect. Candidates are expected to investigate existing behavior, identify root causes, refactor unsafe boundaries, and implement the requirements in [the assessment brief](ASSESSMENT.docx). Do **not** treat the current implementation as an example of the desired final architecture.

## Stack

- PHP 8.2+
- Laravel 12
- Livewire 3
- Tailwind CSS 4 and Vite
- SQLite by default for quick local setup
- MySQL is also supported through `.env`

# Dispatch Platform Stabilization

## Overview

This assessment stabilizes the critical dispatch workflow without replacing
the existing Laravel/Livewire application.

## Implemented

- Atomic driver assignment
- Atomic driver reassignment
- Driver release on completion
- Driver release on cancellation
- Centralized trip lifecycle
- Optimistic concurrency using Trip.version
- Conflict activity logging
- Server-side authorization
- Server-side pagination
- Debounced search
- Pagination reset on filtering
- Eager loading of drivers
- Dispatch-board indexes
- Audit logging
- Status history
- Automated tests

## Architecture

Livewire is responsible for UI orchestration.

Application services contain business operations:

- AssignmentService
- TripLifecycleService
- TripFareService

Supporting infrastructure:

- DispatchBoardQuery
- ActivityLogService
- TripPolicy

Domain values:

- TripStatus
- DriverStatus
- UserRole
- ActivityAction

## Setup

```bash
composer install
npm install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Seeded assessment roles

For local assessment use, the application opens a role selector instead of a credential form. It creates an authenticated session for one of the seeded users and is only available in the `local` and `testing` environments.

| Role | Dispatch flag |
|---|---:|
| Dispatcher | Yes |
| Supervisor | Yes |
| Administrator | No |

The role selector exists only to make authorization scenarios easy to reproduce. Candidates should not treat it as production authentication.

## Seed data

The seeder creates:

- 60 drivers across `offline`, `available`, `assigned`, and `on_trip` states.
- 770 trips with a large pending backlog and a mixture of active/terminal statuses.
- Intentionally incomplete trip-status history.
- No candidate solution services, policies, state machine, locking strategy, or performance indexes.

The dataset is deliberately large enough to make poor query behavior visible on the dispatch board.

## Candidate task

Use [the assessment brief](ASSESSMENT.docx) as the source of truth. The intended exercise includes, among other things:

- stabilizing assignment and reassignment under concurrent requests;
- centralizing trip lifecycle rules and required side effects;
- using `trips.version` for optimistic concurrency control;
- refactoring the Livewire board so presentation does not own domain rules;
- enforcing server-side authorization;
- improving query behavior and database indexing;
- making status history and audit behavior reliable;
- adding the required automated tests;
- documenting the final solution in `README.md` and `DECISIONS.md`.

## Useful commands

```bash
php artisan migrate:fresh --seed
php artisan test
php artisan route:list
npm run dev
npm run build
```

## Important starter-code note

Some current actions appear to work in a single browser session. That is not evidence that they are safe under concurrency, stale browser state, partial persistence failures, or direct unauthorized requests. Those are part of the assessment.

## Submission hygiene

Do not commit `.env`, `vendor/`, local SQLite databases, logs, secrets, generated build output, or private keys.
