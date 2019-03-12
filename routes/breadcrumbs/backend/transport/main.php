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

Breadcrumbs::for('admin.transport.main.edit', function ($trail, $id) {
    $trail->parent('admin.transport.main.index');
    $trail->push('修改行程', route('admin.transport.main.edit',$id));
});


Breadcrumbs::for('admin.transport.sub.show', function ($trail, $id) {
    $sub = \App\Models\Transport\TransportSub::find($id);
    $trail->parent('admin.transport.main.show',$sub->transport_main_id);
    $trail->push('司机行程查看', route('admin.transport.sub.show', $id));
});