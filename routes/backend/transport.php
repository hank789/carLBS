<?php

use App\Http\Controllers\Backend\Transport\UserController;

/*
 * All route names are prefixed with 'admin.auth'.
 */
Route::group([
    'prefix'     => 'transport',
    'as'         => 'transport.',
    'namespace'  => 'Transport',
    'middleware' => 'role:'.config('access.users.admin_role'),
], function () {
    /*
     * User Management
     */
    Route::group(['namespace' => 'User'], function () {

        /*
         * User Status'
         */
        Route::get('user/deactivated', [UserController::class, 'getDeactivated'])->name('user.deactivated');

        /*
         * User CRUD
         */
        Route::get('user', [UserController::class, 'index'])->name('user.index');

        /*
         * Specific User
         */
        Route::group(['prefix' => 'user/{user}'], function () {
            // User
            Route::get('/', [UserController::class, 'show'])->name('user.show');

            // Status
            Route::get('mark/{status}', [UserController::class, 'mark'])->name('user.mark')->where(['status' => '[0,1]']);
        });
    });
});
