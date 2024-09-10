<?php

namespace App\Exception;

use Exception;

class InvalidPaymentProviderException extends Exception
{
    public function __construct($provider)
    {
        parent::__construct('Invalid payment provider: ' . $provider);
    }
}
