<?php

namespace App\Exceptions;

use Exception;

class InvalidBlacklistsFormatException extends Exception
{
    protected $message = 'invalid blacklists format';
}
