<?php
/**
 * Database configuration
 * You can extend other databases.
 */
return [

# ===> MySQL master server configuration

    # host
    'master.db.host' => '127.0.0.1',

    # server port
    'master.db.port' => 3306,

    # username
    'master.db.user' => 'root',

    # password
    'master.db.pass' => '',

    # name
    'master.db.name' => '',

    # charset, default charset is utf8
    'master.db.charset' => 'utf8',

# ===> MySQL slave server configuration

    # host
    'slave.db.host' => '127.0.0.1',

    # server port
    'slave.db.port' => 3306,

    # username
    'slave.db.user' => 'root',

    # password
    'slave.db.pass' => '',

    # name
    'slave.db.name' => '',

    # charset, default charset is utf8
    'slave.db.charset' => 'utf8',

# ===> Redis server configuration

    # host
    'redis.host' => '127.0.0.1',

    # port
    'redis.port' => 6379,

    # db, default is 0
    'redis.db' => 0,

    # Redis密码
    'redis.auth' => '',
];
