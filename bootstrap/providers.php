<?php

use Illuminate\Support\Collection;

return Collection::make([
    App\Providers\NewsServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    App\Providers\AppServiceProvider::class,
])->filter()->all();
