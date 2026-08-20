# Technical Decisions

## 1. Pessimistic locking for driver assignment

### Decision

Driver assignment uses a database transaction with `lockForUpdate()` on all
affected driver rows.

### Problem

Two dispatchers can observe the same driver as available and both attempt to
assign that driver.

### Alternatives considered

#### UI-only disabling

Rejected.

UI state is stale and cannot protect concurrent requests.

#### Optimistic locking on Driver

Possible, but assignment is a critical availability operation where serializing
access to the driver row provides clearer correctness semantics.

#### Application mutex/cache lock

Rejected for the core invariant because the database remains the source of
truth and distributed lock failure semantics add unnecessary complexity.

### Result

The database serializes competing assignments and the second transaction
re-evaluates driver availability after acquiring the lock.

---

## 2. Centralized trip lifecycle

### Decision

Trip transitions are defined by the `TripStatus` enum and executed by
`TripLifecycleService`.

### Problem

The original Livewire component accepted arbitrary status values and allowed
invalid transitions such as pending -> completed.

### Alternatives considered

#### Keep rules in Livewire

Rejected because other entry points could bypass them.

#### Model event observers

Rejected because lifecycle transitions are explicit application operations
and hidden side effects in observers would make the workflow harder to reason
about.

### Result

All application entry points use the same transition service.

---

## 3. Optimistic concurrency using Trip.version

### Decision

Every mutable trip operation accepts the version loaded by the user and
compares it with the current database version inside a transaction.

### Problem

An old browser tab can overwrite newer changes.

### Alternatives considered

#### Last-write-wins

Rejected because it silently loses operational changes.

#### Pessimistic locking for the entire browser interaction

Rejected because a database lock cannot reasonably remain open between HTTP
requests.

### Result

Stale writes are rejected and recorded as activity conflicts.

---

## 4. Livewire remains a presentation/orchestration layer

### Decision

Livewire validates input, authorizes the current user, calls application
services, and translates domain failures into UI messages.

### Problem

The original component contained queries, persistence, authorization,
transitions, notifications, and audit logic.

### Result

Business rules are now testable without rendering Livewire.

---

## 5. Server-side pagination

### Decision

The dispatch board uses database pagination instead of loading the complete
trip collection.

### Problem

The previous implementation called `get()` and paginated in PHP.

### Result

Only the requested page is loaded, and relationships are eager loaded.

---

## 6. No caching for assignment availability

### Decision

Driver availability used for assignment is always read from the database
inside the transaction.

### Reason

Cached availability can be stale and therefore unsafe for a critical
assignment invariant.

---

## 7. Audit failures are transactional

### Decision

Required audit records are created inside the same transaction as the
business operation.

### Reason

An assignment without its required audit trail is considered an incomplete
operation.