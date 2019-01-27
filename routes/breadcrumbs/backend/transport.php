<?php

Breadcrumbs::for('admin.transport', function ($trail) {
    $trail->push('车队管理');
});

require __DIR__.'/transport/user.php';
require __DIR__.'/transport/main.php';