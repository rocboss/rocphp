<?php

# 路由配置项
$router_config = array(
    # 匹配首页
    '/' => array('home', 'index'),

    # 未匹配转404，默认不可删除规则，且必须置于最后
    '*' => 'notFound'
);

?>