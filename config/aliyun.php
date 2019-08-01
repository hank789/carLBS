<?php

return array(
    'accessKeyId' => env('ALIYUN_KEYID'),

    // capture release as git sha
    // 'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),

    // Capture bindings on SQL queries
    'accessSecret' => env('ALIYUN_SECRET'),

    'region' => env('ALIYUN_REGION','cn-hangzhou'),

    'lotSecret' => env('ALIYUN_LOT_SECRET'),
    'lotKey' => env('ALIYUN_LOT_KEY')

);
