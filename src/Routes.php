<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use ClarionApp\EloquentMultiChainBridge\Controllers\DataStreamRegistryController;

Route::group(['middleware'=>config('multichain.middleware'), 'prefix'=>'api/clarion-app/eloquent-multichain-bridge' ], function () {
    Route::resource('data-stream-registry', DataStreamRegistryController::class);
});
