<?php

use App\Http\Controllers\Backend\Map\YingyanController;

/*
 * All route names are prefixed with 'admin.auth'.
 */
Route::group([
    'prefix'     => 'map',
    'as'         => 'map.',
    'namespace'  => 'Map'
], function () {

    //获取终端实例
    Route::get('entity/search', [YingyanController::class, 'searchEntity'])->name('yingyan.searchEntity');
    Route::get('entity/boundsearch', [YingyanController::class, 'boundsearchEntity'])->name('yingyan.boundsearchEntity');
    Route::get('track/getdistance', [YingyanController::class, 'getDistance'])->name('yingyan.getDistance');
    Route::get('track/gettrack', [YingyanController::class, 'getTrack'])->name('yingyan.getTrack');
    Route::get('entity/listcolumn', [YingyanController::class, 'columnsList'])->name('yingyan.columnsList');
    Route::get('track/gethistory', [YingyanController::class, 'trackList'])->name('yingyan.trackList');
    Route::get('analysis/staypoint', [YingyanController::class, 'getstaypoint'])->name('yingyan.getstaypoint');
    Route::get('analysis/drivingbehavior', [YingyanController::class, 'getBehaviorAnalysis'])->name('yingyan.getBehaviorAnalysis');
    Route::get('getAddress', [YingyanController::class, 'getAddress'])->name('yingyan.getAddress');
});
