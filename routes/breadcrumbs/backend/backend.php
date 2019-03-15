<?php

Breadcrumbs::for('admin.dashboard', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.dashboard'));
});

Breadcrumbs::for('admin.version.index', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.version.index'));
});

Breadcrumbs::for('admin.version.create', function ($trail) {
    $trail->parent('admin.version.index');
    $trail->push('添加版本', route('admin.version.create'));
});

Breadcrumbs::for('admin.version.edit', function ($trail, $id) {
    $trail->parent('admin.version.index');
    $trail->push('修改版本', route('admin.version.edit',$id));
});


require __DIR__.'/auth.php';
require __DIR__.'/log-viewer.php';
require __DIR__.'/transport.php';
