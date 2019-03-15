<?php

use App\Http\Controllers\Backend\Company\User\UserController;
use App\Http\Controllers\Backend\Company\User\UserStatusController;
use App\Http\Controllers\Backend\Company\User\UserConfirmationController;
use App\Http\Controllers\Backend\Company\User\UserSessionController;

/*
 * All route names are prefixed with 'admin.auth'.
 */
Route::group([
    'prefix'     => 'company',
    'as'         => 'company.',
    'namespace'  => 'Company',
    'middleware' => 'role:'.config('access.users.admin_role'),
], function () {
    /*
     * User Management
     */
    Route::group(['namespace' => 'User'], function () {
        /*
         * User Status'
         */
        Route::get('user/deactivated', [UserStatusController::class, 'getDeactivated'])->name('user.deactivated');
        Route::get('user/deleted', [UserStatusController::class, 'getDeleted'])->name('user.deleted');

        /*
         * User CRUD
         */
        Route::get('user', [UserController::class, 'index'])->name('user.index');
        Route::get('user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('user', [UserController::class, 'store'])->name('user.store');
    });

    /*
         * Specific User
         */
    Route::group(['prefix' => 'user/{user}'], function () {
        // User
        Route::get('/', [UserController::class, 'show'])->name('user.show');
        Route::get('edit', [UserController::class, 'edit'])->name('user.edit');
        Route::patch('/', [UserController::class, 'update'])->name('user.update');
        Route::delete('/', [UserController::class, 'destroy'])->name('user.destroy');

        // Account
        Route::get('account/confirm/resend', [UserConfirmationController::class, 'sendConfirmationEmail'])->name('user.account.confirm.resend');

        // Status
        Route::get('mark/{status}', [UserStatusController::class, 'mark'])->name('user.mark')->where(['status' => '[0,1]']);

        // Confirmation
        Route::get('confirm', [UserConfirmationController::class, 'confirm'])->name('user.confirm');
        Route::get('unconfirm', [UserConfirmationController::class, 'unconfirm'])->name('user.unconfirm');

        // Session
        Route::get('clear-session', [UserSessionController::class, 'clearSession'])->name('user.clear-session');

        // Deleted
        Route::get('delete', [UserStatusController::class, 'delete'])->name('user.delete-permanently');
        Route::get('restore', [UserStatusController::class, 'restore'])->name('user.restore');
    });

});
