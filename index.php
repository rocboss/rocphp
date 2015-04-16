<?php

# 引入框架入口文件
require 'system/Entrance.php';

# 引入数据库配置文件
require 'app/config/db_config.php';

# 引入路由规则配置文件
require 'app/config/router_config.php';

# 实例化ROC框架，动态调用
$app = ROC::app();

# 存储已加载类
$app->set('loadRuleClass', array());

# 加载app基础类
require_once 'app/controller/base.php';

# 路由分发
foreach ($router_config as $path => $rule)
{       
    if (is_array($rule) && isset($rule[0]) && !in_array($rule[0], $app->get('loadRuleClass')))
    {
        require_once 'app/controller/'.$rule[0].'.php';

        $rule[0] = new $rule[0]($app, $db_config);

        $app->set('loadRuleClass', array_merge($app->get('loadRuleClass'), array($rule[0])));
    }

    $app->route($path, $rule);
}

# 清除变量
$app->clear('loadRuleClass');

# 启动框架
$app->start();

?>
