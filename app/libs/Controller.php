<?php

class Controller
{
    protected static $_dbInstances = [];

    protected static $_controllerInstances = [];

    protected static $_modelInstances = [];

    protected static $_router = [];
    
    // 初始化操作
    public static function init()
    {
        // 设置时区
        date_default_timezone_set('Asia/Shanghai');

        if (get_magic_quotes_gpc())
        {
            $_GET = self::stripslashesDeep($_GET);
            $_POST = self::stripslashesDeep($_POST);
            $_COOKIE = self::stripslashesDeep($_COOKIE);
        }

        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);

        Roc::map('controller', [__CLASS__, 'getController']);

        Roc::map('model', [__CLASS__, 'getModel']);

        Roc::map('url', [__CLASS__, 'getUrl']);
        
        Roc::map('db', [__CLASS__, 'getDb']);

        Roc::map('getRunTime', [__CLASS__,'getRunTime']);
        
        // 初始化路由
        self::initRoute();
    }

    public static function getDb($name = 'db')
    {
        if (!isset(self::$_dbInstances[$name]))
        {
            $db_host = Roc::get($name.'.host');

            $db_port = Roc::get($name.'.port');

            $db_user = Roc::get($name.'.user');

            $db_pass = Roc::get($name.'.pass');

            $db_name = Roc::get($name.'.name');

            $db_charset = Roc::get($name.'.charset');

            $pdo = new \PDO('mysql:host='.$db_host.';dbname='.$db_name.';port='.$db_port, $db_user, $db_pass);

            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $pdo->exec('SET CHARACTER SET '.$db_charset); 

            $db = new DBEngine();

            $db->setDb($pdo);

            self::$_dbInstances[$name] = $db;
        }
        
        return self::$_dbInstances[$name];
    }
    
    public static function getRunTime()
    {
        if (!defined('START_TIME'))
        {
            return '';
        }
        
        $stime = explode(' ', START_TIME);

        $etime = explode(' ', microtime());

        return sprintf('%0.3f', round($etime[0] + $etime[1] - $stime[0] - $stime[1], 3));
    }
    
    public static function getController($name)
    {
        $class = '\\' . trim(str_replace('/', '\\', $name), '\\') . 'Controller';

        if (!isset(self::$_controllerInstances[$class]))
        {
            $instance = new $class();

            self::$_controllerInstances[$class] = $instance;
        }
        
        return self::$_controllerInstances[$class];
    }
    
    public static function getModel($name, $initDb = TRUE)
    {
        $class = '\\' . trim(str_replace('/', '\\', $name), '\\') . 'Model';

        if (!isset(self::$_modelInstances[$class]))
        {
            $instance = new $class();

            if ($initDb)
            {
                $instance->setDb(self::db());
            }

            self::$_modelInstances[$class] = $instance;
        }
        
        return self::$_modelInstances[$class];
    }
    
    public static function getUrl($name, array $params = [])
    {
        if (!isset(self::$_router[$name]))
        {
            return '/';
        }
        else
        {
            $url = self::$_router[$name];

            foreach ($params as $k => $v)
            {
                if (preg_match('/^\w+$/', $v))
                {
                    $url = preg_replace('#@($k)(:([^/\(\)]*))?#', $v, $url);
                }
            }
            return $url;
        }
    }
    
    public static function initRoute()
    {
        $router = Roc::get('system.router');
        
        if (is_array($router))
        {
            foreach ($router as $route)
            {
                self::$_router[$route[1]] = $route[0];

                $tmp = explode(':', $route[1]);

                $class = '\\' . trim(str_replace('/', '\\', $tmp[0]), '\\') . 'Controller';

                $func = $tmp[1];

                $pattern = $route[0];

                Roc::route($pattern, [$class, $func]);
            }
        }
    }

    public static function stripslashesDeep($data)
    {
        if (is_array($data))
        {
            return array_map([__CLASS__, __FUNCTION__], $data);
        }
        else
        {
            return stripslashes($data);
        }
    }
}