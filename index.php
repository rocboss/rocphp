<?php

define('START_TIME', microtime());

require 'system/Roc.php';

Roc::set([
    'system.handle_errors' => true,

    'system.controllers.path' => 'app/controllers',

    'system.models.path' => 'app/models',

    'system.views.path' => 'app/views',

    'system.libs.path' => 'app/libs',

    'system.router' => require 'app/config/router.php',

    // 数据库主机地址
    'db.host' => 'localhost',

    // 数据库端口
    'db.port' => 3306,

    // 数据库用户名
    'db.user' => 'root',

    // 数据库密码
    'db.pass' => '123123',

    // 数据库名称
    'db.name' => 'test',

    // 数据库编码
    'db.charset' => 'utf8'
]);

Roc::path(Roc::get('system.controllers.path'));

Roc::path(Roc::get('system.models.path'));

Roc::path(Roc::get('system.libs.path'));

Roc::before('start', ['Controller', 'init']);

Roc::start();

