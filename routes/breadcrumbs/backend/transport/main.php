<?php

Breadcrumbs::for('admin.transport.main.index', function ($trail) {
    $trail->parent('admin.transport');
    $trail->push('行程管理', route('admin.transport.user.index'));
});

Breadcrumbs::for('admin.transport.main.show', function ($trail, $id) {
    $trail->parent('admin.transport.main.index');
    $trail->push('行程查看', route('admin.transport.main.show', $id));
});
