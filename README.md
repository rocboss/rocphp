## ROCPHP v1.1.0 开发文档构建中...

---

## ROCPHP v1.0.0 开发文档 by ROC

第一个问题 Why ?

为什么要开发ROCPHP？

其实ROCPHP并不是在重复造轮子，毕竟现在已经有很多优秀的PHP框架。

这只是在本人构建PHP工程时，为了简洁、高效、易用、轻量级等特性，经过几次重构和对其他框架优点的吸收采纳，从而总结出来的一套用于快速搭建风格优雅、轻量级的restful web应用的框架。

如果你是一个文静、最求简洁、喜欢优雅地写代码，甚至想在几杯coffee的闲暇时间就能整出一个小app的coder，那么相信ROCPHP会适合你~

#### 五分钟快速了解ROCPHP

1. 目录结构

    `index.php` 框架单入口文件
    
    `app` app应用程序目录

    __`app/config/` app的系统配置文件和路由规则目录
    
    __`app/controller` app的控制器目录（注意ROCPHP框架中 M 和 C 并没有很明确严苛的界定）
    
    __`app/template` app的模板目录
    
    __`app/cache` app的模板缓存目录
    
    `system` 系统框架目录
    
    __`system/core` 系统核心类库，负责事件分发和自动加载
    
    __`system/net` 系统网络请求处理类库，包括请求、响应、路由等
    
    __`system/util` 系统工具类库，可根据app进行响应的扩充
    
    __`system/db` 系统数据库操作类库
    
    __`system/template` 系统模板引擎类库
    
    注意：一般情况下不需要修改 `system` 文件夹下面的框架代码。
    
2. 单一入口模式。请求统一发送至index.php文件，index.php在接受到请求后，会自动请求框架路由然后进行事件分发，而这时候我们就需要具体的分发规则（`app/config/router_config.php`）了，也就是根据请求的URL来执行分配不同的资源（所以很方便你搭建一个restful的web应用）

3. 事件分发到`app/controller/`下面具体的类的方法中执行相应的类的方法。

#### 五分钟快速安装一个ROCPHP框架
1. 要求：PHP >= 5.3 ，如使用了数据库，则需要开启`pdo_mysql`扩展

2. 下载最新版本的ROCPHP [Coding站下载](https://coding.net/u/rocboss/p/ROCPHP/git "ROCPHP源码Coding.net下载") [OSC站下载](http://git.oschina.net/rocboss/rocphp "ROCPHP源码Oschina.net下载") [Github站下载](https://github.com/rocboss/rocphp "ROCPHP源码Github.com下载"), 解压放到你的web目录中

3. 配置你服务器的伪静态

    在 Apache 下， 编辑你的 .htaccess 文件：

    ```
      RewriteEngine On
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteRule ^(.*)$ index.php [QSA,L]
    ```
   
    在 Nginx 下, 添加以下声明：

    ```
      location / {
          try_files $uri $uri/ /index.php;
      }
    ```

4. 打开你的网址, 将会输出 Hello world 字样

#### 基础教程

首先，打开 `app/config/router_config.php`，在数组里面添加一条规则如下所示：

```php
<?php
    # 路由表
    $router_config = array(
        '/' => array('home', 'index'),
        '/test' => array('home', 'my_test')
    );
?>
```

然后在 `app/controller/home.php` 中增加一个`public`方法，如下：

```php
<?php
    public function my_test()
    {
        echo 'This is a testing page';
    }
?>
```
在路由表规则中你可以使用 `POST` 、`GET` 、`POST|GET` 来控制允许的访问类型，如：

```php
<?php
    # 路由表
    $router_config = array(
        '/' => array('home', 'index'),
        'GET /test' => array('home', 'my_test'),
        'POST /test1' => array('home', 'my_test1'),
        'GET|POST /test2' => array('home', 'my_test2'),
    );
?>
```

在路由表中允许正则匹配、通配符`*`、参数路由规则，如：

```php
<?php
     # 路由表
    $router_config = array(
        '/' => array('home', 'index'),
        '/get/username/*' => array('home', 'getUsername'),
        '/user/[0-9]+' => array('user', 'index'),
        '/user/@uid:[0-9]+' => array('user', 'info')
    );
?>
```

`user.php`对应的`public`方法：

```php
<?php
    public function index()
    {
        return '没有传参';
    }
    
    public function info($uid)
    {
        return '参数路由的回调';
    }
?>
```
    
在ROCPHP框架中你可以映射你的自定义方法

```php
<?php
    # 映射你的方法
    $this->app->map('myFunction', function($name) {
        echo "Hello $name!";
    });

    # 调用你的方法
    $this->app->myFunction('ROC');
?>
```

ROCPHP可以注册扩展第三方类

```php
<?php
    # 注册一个自定义类
    $this->app->register('myClass', 'A_Class');

    # 实例化你的类
    $user = new $this->app->myClass();
?>
```
ROCPHP可以允许你覆盖默认的功能来满足自己的需求,而不需要修改任何代码

如：当ROCPHP没有匹配到URL的路由, 会调用`notFound`方法发送一个通用的`HTTP 404`响应，你可以覆盖此行为通过使用`map`方法。

```php
<?php
    $this->app->map('notFound', function() {
        include '404.php';
    });
?>
```

你还可以替换ROCPHP框架的核心组件 例如可以用你的自定义类替换默认的路由器类

```php
<?php
    # 注册你自己的路由类
    $this->app->register('router', 'MyRouter');

    # 当ROCPHP加载这个实例时，将会调用你的类
    $myrouter = $this->app->router();
?>
```
注意，核心的 `map` 和 `register` 方法是不可以覆盖。

ROCPHP可以保存变量,这样就可以在应用程序任何地方使用。

```php
<?php
    $this->app->set('id', '123');
    
    $id = $this->app->get('id');
    
    # 检测是否存在某个变量
    if ($this->app->has('id'))
    {
        echo 'id变量存在';
    }
?>
```
#### 基础数据库操作 `$this->app->db()`

数据库配置文件在`app/config/db_config.php`中，应用程序要使用数据库，则必须开启`pdo_mysql`扩展

**`select` 操作**

```php
<?php
    $this->app->db()->select($table, $columns, $where);
    
    /***
        table [string]
        表名.
        
        columns [string/array]
        要查询的字段名.
        
        where (optional) [array]
        查询的条件
    ***/

   $this->app->db()->select($table, $join, $columns, $where);
   
    /***
        table [string]
        表名
        
        join [array]
        多表查询,不使用可以忽略
        
        columns [string/array]
        要查询的字段名
        
        where (optional) [array]
        查询的条件
    ***/
    
    # 示例
    $this->app->select("post", array(
        '[>]account' => array('author_id' => 'user_id'),
        
        '[>]album' => 'user_id',
     
        '[>]photo' => array('user_id', 'avatar_id')
    ), array(
        'post.post_id',
        
        'post.title',
        
        'account.city'
    ), array(
        'post.user_id' => '100',
        
        'ORDER' => 'post.post_id DESC',
        
        'LIMIT' => '20'
    ));
    
    # 等价于
    $this->app->db()->query("SELECT `post`.`post_id`, `post`.`title`, `account`.`city` FROM `post` LEFT JOIN `account` ON `post`.`author_id` = `account`.`user_id` LEFT JOIN `album` USING (`user_id`) LEFT JOIN `photo` USING (`user_id`, `avatar_id`) WHERE `post`.`user_id` = 100 ORDER BY `post`.`post_id` DESC LIMIT 20");

    # 可以使用别名，以防止字段冲突
    $this->app->db()->select('account', array(
        'uid',
        'nickname(my_nickname)'
    ), array(
        "LIMIT" => '20'
    ));
?>
```

**`insert` 操作**

```php
<?php
    $this->app->db()->insert($table, $data);
    
    /***
        table [string]
        表名
        
        data [array]
        插入到表里的数据
        
        return: [number] 返回插入的id
    ***/
    
?>
```

**`update` 操作**

```php
<?php
    $this->app->db()->update($table, $data, $where);
    
    // return: [number] 受影响的行数
?>
```

**`delete` 操作**

```php
<?php
    $this->app->db()->delete($table, $where);
    
    // return: [number] 返回被删除的行数
?>
```

**`replace` 操作**

```php
<?php
    $this->app->db()->replace($table, $column, $search, $replace, $where);
    
    $this->app->db()->replace($table, $column, $replacement, $where);
    
    $this->app->db()->replace($table, $columns, $where);
    
    /***
        table [String] 表名
        
        columns [string/array] 需要替换的目标字段
        
        search [string] 被查找的将要被替换的目标字段值
        
        replace [string] 替换的新值
        
        replacement [array] 数组形式替换值
        
        where (optional) [array] 条件
?>
```

**`get` 操作（从表中返回一行数据）**

```php
<?php
    $this->app->db()->get($table, $columns, $where);
    
    // columns [string/array] 返回的字段列
    
    // return: [string/array] 返回查询到的数据
?>
```

**`has` 操作（确定数据是否存在）**

```php
<?php
    $this->app->db()->has($table, $where);
    
    $this->app->db()->has($table, $join, $where);
    
    // return: [boolean] 返回 TRUE 或者 FALSE
?>
```

**`count` 操作（获取数据表中的行数）**

```php
<?php
    $this->app->db()->count($table, $where);
    
    $this->app->db()->count($table, $join, $column, $where);
    
    // $column [string] 要统计的字段
?>
```

**`max` 操作（获取数据表中统计字段值最大的）**

```php
<?php
    $this->app->db()->max($table, $column, $where);
    
    $this->app->db()->max($table, $join, $column, $where);
    
    // $column [string]
?>
```

**`min` 操作（获取数据表中统计字段值最小的），用法同上**

**`avg` 操作（获取数据表中某个列字段的平均值），用法同上**

**`max` 操作（获取数据表中某个列字段的值相加总和），用法同上**

**`query` 原生操作**

```php
<?php
    $this->app->db()->query($query);
?>
```
#### 基础模板引擎操作 `$this->app->view()`

**`assign` 赋值操作**

```php
<?php
    $this->app->view()->assign('name', 'ROC');
    
    $this->app->view()->assign('data', array('username'=>'ROC', 'sex'=>'男'));
?>
```

**`display` 赋值至指定的模板文件**

```php
<?php
    $this->app->view()->display($tpl);
    
    // 在框架中，调用 `setViewBase($title, $tpl)` 方法可同时赋值标题
    $this->setViewBase('首页', 'index');
?>
```
###### 模板中基础调用方法

```
{$name} 输出变量

{$arr.key} 调用一维数组

{$arr.key.key2} 调用二维数组

{include('_header.tpl.php')} 调用模板

{:intval($a)} 输出有返回值的函数值

{~var_dump($a)} 输出无返回值的函数值

{loop $array $vaule} {/loop} 或者 {loop $array $key $value} {/loop} 模板中使用foreach循环

{if condition} [ {elseif condition} | {else} ] {/if}  if条件判断

```

#### 至此ROCPHP的基础使用方法大体介绍了一下，有些地方可能不是很详尽，还请包涵。同时还有许多调用方法和技巧没有介绍，这些留给读者慢慢发掘吧。同时如发现什么错误，也欢迎指出，谢谢！

