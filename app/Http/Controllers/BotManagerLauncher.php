<?php
    //after 65 minutes script will be aborted
    ini_set('max_execution_time', 3900);

    //load laravel
    require __DIR__ . "/../../../vendor/autoload.php";

    $app = require_once __DIR__ . "/../../../bootstrap/app.php";
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = \Illuminate\Http\Request::capture());

    //start the bot manager
    $botManager = new \App\Http\Controllers\BotManager($argv[1]);
    $botManager->handle();
?>
