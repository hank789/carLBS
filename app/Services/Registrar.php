<?php namespace App\Services;

use App\Models\Auth\ApiUser;
use Carbon\Carbon;
use Validator;
use Ramsey\Uuid\Uuid;

class Registrar {

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return ApiUser
     */
    public function create(array $data)
    {
        $user =  ApiUser::create([
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'gender' => 0,
            'status' => $data['status'],
            'last_login_ip' => $data['visit_ip'],
            'last_login_at' => date('Y-m-d H:i:s')
        ]);

        return $user;
    }


}
