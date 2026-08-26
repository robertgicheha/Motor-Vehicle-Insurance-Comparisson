<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$view = view('layouts.sidebar');
echo "Sidebar blade compiled OK\n";
unlink(__FILE__);
