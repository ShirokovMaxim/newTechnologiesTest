<?php

namespace App\Exceptions;

use Exception;

class PublisherNotFoundException extends Exception
{
    protected $message = 'publisher not found';
}
