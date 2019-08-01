<?php

namespace App\Listeners\Backend\Auth\User;

use App\Events\Api\ExceptionNotify;
use App\Events\Api\SystemNotify;
use App\Models\Auth\Company;
use App\Models\Auth\Tenant;
use App\Third\AliLot\Service;
use Illuminate\Contracts\Queue\ShouldQueue;
/**
 * Class UserEventListener.
 */
class UserEventListener implements ShouldQueue
{
    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 3;

    /**
     * @param $event
     */
    public function onCreated($event)
    {
        \Log::info('User Created');
        $user = $event->user;
        if ($event->user->tenant_id > 0) {
            $tenant = Tenant::find($event->user->tenant_id);
            $service = new Service();
            $res = $service->getUserInfo($tenant->tenant_id,$tenant->app_id,$event->user->id);
            if ($res && $res['code'] == 200) {
                \Log::info('saasUserInfo',$res);
                $phone = $res['data']['phone'];
                $companyName = '公司'.$phone;
                event(new SystemNotify('新saas用户创建成功:'.$user->id));
            } else {
                $companyName = $user->first_name.'的公司';
                $phone = $user->mobile;
                event(new ExceptionNotify('获取saas用户信息失败:'.($res['message']??'')));
            }
            $company = Company::where('company_name',$companyName)->first();
            if (!$company) {
                $company = Company::create([
                    'company_name' => $companyName,
                    'company_type' => Company::COMPANY_TYPE_MAIN,
                    'status' => Company::COMPANY_STATUS_VALID,
                    'appname' => 2
                ]);
            }
            $user->mobile = $phone;
            $user->company_id = $company->id;
            $user->save();
            $tenant->status = Tenant::STATUS_SUBSCRIBING;
            $tenant->save();

        }
    }

    /**
     * @param $event
     */
    public function onUpdated($event)
    {
        \Log::info('User Updated');
    }

    /**
     * @param $event
     */
    public function onDeleted($event)
    {
        \Log::info('User Deleted');
    }

    /**
     * @param $event
     */
    public function onConfirmed($event)
    {
        \Log::info('User Confirmed');
    }

    /**
     * @param $event
     */
    public function onUnconfirmed($event)
    {
        \Log::info('User Unconfirmed');
    }

    /**
     * @param $event
     */
    public function onPasswordChanged($event)
    {
        \Log::info('User Password Changed');
    }

    /**
     * @param $event
     */
    public function onDeactivated($event)
    {
        \Log::info('User Deactivated');
    }

    /**
     * @param $event
     */
    public function onReactivated($event)
    {
        \Log::info('User Reactivated');
    }

    /**
     * @param $event
     */
    public function onSocialDeleted($event)
    {
        \Log::info('User Social Deleted');
    }

    /**
     * @param $event
     */
    public function onPermanentlyDeleted($event)
    {
        \Log::info('User Permanently Deleted');
    }

    /**
     * @param $event
     */
    public function onRestored($event)
    {
        \Log::info('User Restored');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            \App\Events\Backend\Auth\User\UserCreated::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onCreated'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserUpdated::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onUpdated'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserDeleted::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onDeleted'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserConfirmed::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onConfirmed'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserUnconfirmed::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onUnconfirmed'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserPasswordChanged::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onPasswordChanged'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserDeactivated::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onDeactivated'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserReactivated::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onReactivated'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserSocialDeleted::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onSocialDeleted'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserPermanentlyDeleted::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onPermanentlyDeleted'
        );

        $events->listen(
            \App\Events\Backend\Auth\User\UserRestored::class,
            'App\Listeners\Backend\Auth\User\UserEventListener@onRestored'
        );
    }
}
