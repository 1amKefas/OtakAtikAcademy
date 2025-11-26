#!/usr/bin/env php
<?php

// Script untuk delete user dengan email tertentu
// Usage: php artisan tinker
// > User::where('email', 'pakongede21@gmail.com')->delete();

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'tinker',
    ]),
    new \Symfony\Component\Console\Output\BufferedOutput,
);

exit($status);
