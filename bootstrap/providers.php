<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\FortifyServiceProvider;
use Tighten\Ziggy\ZiggyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    AuthServiceProvider::class,
    ZiggyServiceProvider::class,
];
