<?php
/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (c) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */


require __DIR__.'/../../../../../vendor/autoload.php';



/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../../../../../bootstrap/app.php';



$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

require_once __DIR__ . '/vendor/autoload.php';

use CKSource\CKFinder\CKFinder;


$ckfinder = new CKFinder(__DIR__ . '/../../../config.php');
$ckfinder->run();
