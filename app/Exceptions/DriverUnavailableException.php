<?php

namespace App\Exceptions;

class DriverUnavailableException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'The selected driver is no longer available. Please refresh the trip and choose another driver.'
        );
    }
}