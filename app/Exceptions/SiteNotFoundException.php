<?php

namespace App\Exceptions;

use Exception;

class SiteNotFoundException extends Exception
{
    protected $message = 'site not found';
}
