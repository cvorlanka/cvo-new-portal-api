<?php

use App\Http\Controllers\FrameSearchController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

// All routes here are prefixed with /api (see bootstrap/app.php).

Route::prefix('frames')->controller(FrameSearchController::class)->group(function () {
    Route::get('territories', 'userTerritories'); // ?email=
    Route::get('search', 'frameSearch');          // ?territory_id=&search=&period=
});

Route::prefix('resources')->controller(ResourceController::class)->group(function () {
    Route::get('types', 'types');
    Route::get('{type}/categories', 'categories'); // must stay above {type}/{category}
    Route::get('{type}/{category}', 'files');
});
