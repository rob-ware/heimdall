<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServerController;

use App\Mail\MultipleMacsFound;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

//Route for mailing!
Route::get('/email', function() {
    Mail::to('r.ware@ulster.ac.uk')->send(new MultipleMacsFound());
    return new MultipleMacsFound();
});

Route::get('/server', [ServerController::class, 'show']);
