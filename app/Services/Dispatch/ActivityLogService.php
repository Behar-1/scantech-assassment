<?php

namespace App\Services\Dispatch;

use App\Enums\ActivityAction;
use App\Models\ActivityLog;
use App\Models\Trip;
use App\Models\User;

class ActivityLogService
{
    public function record(
        User $actor,
        ActivityAction $action,
        Trip $trip,
        array $previousValues,
        array $newValues,
        ?string $reason = null,
    ): ActivityLog {
        return ActivityLog::query()->create([
            'actor_id' => $actor->id,
            'action' => $action->value,
            'entity_type' => Trip::class,
            'entity_id' => $trip->id,
            'previous_values' => $previousValues,
            'new_values' => $newValues,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}