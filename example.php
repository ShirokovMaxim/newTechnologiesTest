<?php

require __DIR__ . '/vendor/autoload.php';

use App\Exceptions\AdvertiserNotFoundException;
use App\Exceptions\InvalidBlacklistsFormatException;
use App\Exceptions\PublisherNotFoundException;
use App\Exceptions\SiteNotFoundException;
use App\Services\Blacklists;
use Illuminate\Contracts\Console\Kernel;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

try {
    Blacklists::save('s1, p1, s2, s3, p2', 3);
} catch (AdvertiserNotFoundException | InvalidBlacklistsFormatException | PublisherNotFoundException | SiteNotFoundException $e) {
    dd($e->getMessage());
}

$blacklists = Blacklists::get(3);

dd($blacklists);
