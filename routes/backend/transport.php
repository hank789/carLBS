<?php

use App\Http\Controllers\Backend\Transport\UserController;
use App\Http\Controllers\Backend\Transport\MainController;

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
    Route::group([], function () {

        /*
         * User CRUD
         */
        Route::get('user', [UserController::class, 'index'])->name('user.index');

        /*
         * Specific User
         */
        Route::group(['prefix' => 'user/{id}'], function () {
            // User
            Route::get('/', [UserController::class, 'show'])->name('user.show');

            // Status
            Route::get('mark/{status}', [UserController::class, 'mark'])->name('user.mark')->where(['status' => '[0,1]']);
        });
    });

    //主行程信息
    Route::get('main', [MainController::class, 'index'])->name('main.index');
    Route::get('main/create', [MainController::class, 'create'])->name('main.create');
    Route::post('main', [MainController::class, 'store'])->name('main.store');
    /*
         * Specific Main
         */
    Route::group(['prefix' => 'main/{id}'], function () {
        // User
        Route::get('/', [MainController::class, 'show'])->name('user.show');

        // Status
        Route::get('mark/{status}', [MainController::class, 'mark'])->name('user.mark')->where(['status' => '[0,1]']);

        Route::get('edit', [MainController::class, 'edit'])->name('main.edit');
        Route::patch('/', [MainController::class, 'update'])->name('main.update');
        Route::delete('/', [MainController::class, 'destroy'])->name('main.destroy');
    });
});
