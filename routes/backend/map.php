<?php

use App\Http\Controllers\Backend\Map\YingyanController;

/*
 * All route names are prefixed with 'admin.auth'.
 */
Route::group([
    'prefix'     => 'map',
    'as'         => 'map.',
    'namespace'  => 'Map',
    'middleware' => 'role:'.config('access.users.admin_role'),
], function () {

    //获取终端实例
    Route::get('entity/search', [YingyanController::class, 'searchEntity'])->name('yingyan.searchEntity');

});
