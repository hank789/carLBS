<?php

Breadcrumbs::for('admin.transport.user.index', function ($trail) {
    $trail->parent('admin.transport');
    $trail->push('司机管理', route('admin.transport.user.index'));
});

Breadcrumbs::for('admin.transport.user.show', function ($trail, $id) {
    $trail->parent('admin.transport.user.index');
    $trail->push(__('menus.backend.access.users.view'), route('admin.transport.user.show', $id));
});
