<?php

# 引入框架入口文件
require 'system/Entrance.php';

# 引入数据库配置文件
require 'app/config/db_config.php';

# 引入路由规则配置文件
require 'app/config/router_config.php';

# 实例化ROC框架，动态调用
$app = ROC::app();

# 路由分发（注册规则）
foreach ($router_config as $path => $rule)
{   
    $app->route($path, $rule);
}

# 路由分发（实例化Class）
foreach ($router_config as $path => $rule)
{
    if ($rule == $app->getNowRoute())
    {
        # 清除之前注册的路由
        $app->clearRoutes();

        # 实例化Class
        $class = '\app\controller\\'.$rule[0];

        $rule[0] = new $class($app, $db_config);

        # 只注册当前URL对应的路由
        $app->route($path, $rule);

        break;
    }
}

# 启动框架
$app->start();

?>
