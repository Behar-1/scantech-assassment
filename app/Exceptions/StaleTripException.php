<?php

namespace App\Exceptions;


class StaleTripException extends DomainException
{
    public function __construct(
        public readonly int $tripId,
        public readonly int $expectedVersion,
        public readonly int $currentVersion,
    ) {
        parent::__construct(
            'This trip was changed by another dispatcher. The current trip has been loaded; please review it before saving again.'
        );
    }
}