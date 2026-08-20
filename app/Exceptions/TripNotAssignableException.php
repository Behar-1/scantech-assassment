<?php

namespace App\Exceptions;

class TripNotAssignableException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'This trip cannot be assigned in its current state.'
        );
    }
}