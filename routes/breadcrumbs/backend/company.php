<?php
/**
 * @companyor: wanghui
 * @date: 2019/3/15 7:48 PM
 * @email:    hank.HuiWang@gmail.com
 */

Breadcrumbs::for('admin.company.user.index', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push('账户管理', route('admin.company.user.index'));
});

Breadcrumbs::for('admin.company.user.deactivated', function ($trail) {
    $trail->parent('admin.company.user.index');
    $trail->push(__('menus.backend.access.users.deactivated'), route('admin.company.user.deactivated'));
});

Breadcrumbs::for('admin.company.user.deleted', function ($trail) {
    $trail->parent('admin.company.user.index');
    $trail->push(__('menus.backend.access.users.deleted'), route('admin.company.user.deleted'));
});

Breadcrumbs::for('admin.company.user.create', function ($trail) {
    $trail->parent('admin.company.user.index');
    $trail->push(__('labels.backend.access.users.create'), route('admin.company.user.create'));
});

Breadcrumbs::for('admin.company.user.show', function ($trail, $id) {
    $trail->parent('admin.company.user.index');
    $trail->push(__('menus.backend.access.users.view'), route('admin.company.user.show', $id));
});

Breadcrumbs::for('admin.company.user.edit', function ($trail, $id) {
    $trail->parent('admin.company.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.company.user.edit', $id));
});

Breadcrumbs::for('admin.company.user.change-password', function ($trail, $id) {
    $trail->parent('admin.company.user.index');
    $trail->push(__('menus.backend.access.users.change-password'), route('admin.company.user.change-password', $id));
});


Breadcrumbs::for('admin.company.vendor.index', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push('账户管理', route('admin.company.vendor.index'));
});
Breadcrumbs::for('admin.company.vendor.create', function ($trail) {
    $trail->parent('admin.company.vendor.index');
    $trail->push(__('labels.backend.access.users.create'), route('admin.company.vendor.create'));
});
Breadcrumbs::for('admin.company.vendor.edit', function ($trail, $id) {
    $trail->parent('admin.company.vendor.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.company.vendor.edit', $id));
});

Breadcrumbs::for('admin.company.manage.index', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push('公司管理', route('admin.company.manage.index'));
});
Breadcrumbs::for('admin.company.manage.create', function ($trail) {
    $trail->parent('admin.company.manage.index');
    $trail->push(__('labels.backend.access.users.create'), route('admin.company.manage.create'));
});
Breadcrumbs::for('admin.company.manage.edit', function ($trail, $id) {
    $trail->parent('admin.company.manage.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.company.manage.edit', $id));
});