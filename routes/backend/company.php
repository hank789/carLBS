<?php

use App\Http\Controllers\Backend\Company\User\UserController;
use App\Http\Controllers\Backend\Company\User\UserStatusController;
use App\Http\Controllers\Backend\Company\User\UserConfirmationController;
use App\Http\Controllers\Backend\Company\User\UserSessionController;
use App\Http\Controllers\Backend\Company\VendorController;
use App\Http\Controllers\Backend\Company\ManageController;
/*
 * All route names are prefixed with 'admin.auth'.
 */
Route::group([
    'prefix'     => 'company',
    'as'         => 'company.',
    'namespace'  => 'Company',
    //'middleware' => 'role:'.config('access.users.admin_role'),
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
        Route::post('user/store', [UserController::class, 'store'])->name('user.store');
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

    //供应商
    Route::group(['prefix' => 'vendor'], function () {
        Route::get('/', [VendorController::class, 'index'])->name('vendor.index');
        Route::get('create', [VendorController::class, 'create'])->name('vendor.create');
        Route::post('store', [VendorController::class, 'store'])->name('vendor.store');

        Route::get('{id}/mark/{status}', [VendorController::class, 'mark'])->name('vendor.mark')->where(['status' => '[-1,0,1,2]']);

        Route::get('{id}/edit', [VendorController::class, 'edit'])->name('vendor.edit');
        Route::patch('{id}/update', [VendorController::class, 'update'])->name('vendor.update');
    });

    //公司
    Route::group(['prefix' => 'manage'], function () {
        Route::get('/', [ManageController::class, 'index'])->name('manage.index');
        Route::get('create', [ManageController::class, 'create'])->name('manage.create');
        Route::post('store', [ManageController::class, 'store'])->name('manage.store');

        Route::get('{id}/mark/{status}', [ManageController::class, 'mark'])->name('manage.mark')->where(['status' => '[-1,0,1,2]']);

        Route::get('{id}/edit', [ManageController::class, 'edit'])->name('manage.edit');
        Route::patch('{id}/update', [ManageController::class, 'update'])->name('manage.update');
    });
});
