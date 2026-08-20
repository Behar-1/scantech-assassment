<?php

namespace App\Exceptions;

use App\Enums\TripStatus;

class InvalidTripTransitionException extends DomainException
{
    public function __construct(
        TripStatus $from,
        TripStatus $to,
    ) {
        parent::__construct(
            sprintf(
                'Trip cannot move from "%s" to "%s".',
                $from->label(),
                $to->label(),
            )
        );
    }
}