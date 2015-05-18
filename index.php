<?php

# 引入框架入口文件
require 'system/Entrance.php';

# 引入数据库配置文件
require 'app/config/db_config.php';

# 引入路由规则配置文件
require 'app/config/router_config.php';

# 实例化ROC框架，动态调用
$app = ROC::app();

# 存储已加载类名称
$app->set('loadRule', array());

# 存储已加载类的实例
$app->set('loadRuleClass', array());

# 路由分发
foreach ($router_config as $path => $rule)
{       
    if (is_array($rule) && isset($rule[0]))
    {
        $tmpRule = $app->get('loadRule');

        $tmpRuleClass = $app->get('loadRuleClass');

        if (!in_array($rule[0], $tmpRule))
        {
            array_push($tmpRule, $rule[0]);

            $class = '\app\controller\\'.$rule[0];

            $tmpRuleClass = array_merge($tmpRuleClass, array($rule[0] => new $class($app, $db_config)));

            $rule[0] = $tmpRuleClass[$rule[0]];
        }
        else
        {
            $rule[0] = $tmpRuleClass[$rule[0]];
        }

        $app->set('loadRule', $tmpRule);

        $app->set('loadRuleClass', $tmpRuleClass);
    }

    $app->route($path, $rule);
}

# 清除已加载的类名变量
$app->clear('loadRule');

# 清除已加载的类变量
$app->clear('loadRuleClass');

# 启动框架
$app->start();

?>
