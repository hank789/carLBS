<?php

namespace App\Events\Api\Auth;

use App\Models\Auth\ApiUser;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserLoggedOut.
 */
class UserLoggedOut
{
    use SerializesModels;

    /**
     * @var
     */
    public $user;

    /**
     * @param $user
     */
    public function __construct(ApiUser $user)
    {
        $this->user = $user;
    }
}
