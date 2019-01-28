<?php

Breadcrumbs::for('admin.transport.main.index', function ($trail) {
    $trail->parent('admin.transport');
    $trail->push('行程管理', route('admin.transport.main.index'));
});

Breadcrumbs::for('admin.transport.main.show', function ($trail, $id) {
    $trail->parent('admin.transport.main.index');
    $trail->push('行程查看', route('admin.transport.main.show', $id));
});

Breadcrumbs::for('admin.transport.main.create', function ($trail) {
    $trail->parent('admin.transport.main.index');
    $trail->push('添加行程', route('admin.transport.main.create'));
});
