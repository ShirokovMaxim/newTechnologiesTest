<?php

namespace App\Exceptions;

use Exception;

class AdvertiserNotFoundException extends Exception
{
    protected $message = 'advertiser not found';
}
