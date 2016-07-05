<?php
/**
 * Application bootstrap class
 */
class Bootstrap
{
    protected static $_dbInstances = [];
    protected static $_controllerInstances = [];
    protected static $_serviceInstances = [];
    protected static $_modelInstances = [];
    protected static $_router = [];
    protected static $_config = [];

    // Initialization
    public static function init()
    {
        // Set timezone
        date_default_timezone_set('Asia/Shanghai');

        // GPC
        if (get_magic_quotes_gpc()) {
            $_GET = self::__stripslashesDeep($_GET);
            $_POST = self::__stripslashesDeep($_POST);
            $_COOKIE = self::__stripslashesDeep($_COOKIE);
        }

        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);

        // Map
        Roc::map('controller', [__CLASS__, 'getController']);
        Roc::map('service', [__CLASS__, 'getService']);
        Roc::map('model', [__CLASS__, 'getModel']);
        Roc::map('db', [__CLASS__, 'getMysqlDb']);
        Roc::map('redis', [__CLASS__, 'getRedis']);

        // Init route
        self::initRoute();
    }

    /**
     * Get controller
     * @method getController
     * @param  [String]        $name [description]
     * @return [Object]              [description]
     */
    public static function getController($name)
    {
        $class = '\\' . trim(str_replace('/', '\\', $name), '\\') . 'Controller';

        if (!isset(self::$_controllerInstances[$class])) {
            $instance = new $class();
            self::$_controllerInstances[$class] = $instance;
        }

        return self::$_controllerInstances[$class];
    }

    /**
     * Get service
     * @method getService
     * @param  [String]     $name [description]
     * @return [Object]           [description]
     */
    public static function getService($name)
    {
        $class = '\\' . trim(str_replace('/', '\\', ucfirst($name)), '\\') . 'Service';
        if (!isset(self::$_serviceInstances[$class])) {
            $instance = new $class();

            self::$_serviceInstances[$class] = $instance;
        }

        return self::$_serviceInstances[$class];
    }

    /**
     * Get model
     * @method getModel
     * @param  [type]   $name   [description]
     * @param  [Bool]   $initDb [description]
     * @return [type]           [description]
     */
    public static function getModel($name = null, $initDb = true)
    {
        if (is_null($name)) {
            return self::getMysqlDb();
        }

        $class = '\\' . trim(str_replace('/', '\\', ucfirst($name)), '\\') . 'Model';
        if (!isset(self::$_modelInstances[$class])) {
            $instance = new $class();

            if ($initDb) {
                $instance->setDb(self::getMysqlDb());
            }

            self::$_modelInstances[$class] = $instance;
        }

        return self::$_modelInstances[$class];
    }

    /**
     * Init route
     * @method initRoute
     * @return [type]    [description]
     */
    public static function initRoute()
    {
        $router = Roc::get('system.router');

        if (is_array($router)) {
            // Custom routing
            foreach ($router as $route) {
                self::$_router[$route[1]] = $route[0];

                $tmp = explode(':', $route[1]);
                $class = '\\' . trim(str_replace('/', '\\', $tmp[0]), '\\') . 'Controller';
                $func = $tmp[1];
                $pattern = $route[0];

                Roc::route($pattern, [$class, $func]);
            }
        }

        // Regular routing
        Roc::route('/@module/@controller/@action/*', function() {

            $params = func_get_args();

            $module = array_shift($params);
            $controller = array_shift($params);
            $action = array_shift($params);
            $route_obj = array_shift($params);
            $params = explode('/', $route_obj->splat);

            unset($route_obj);

            $className = '\\'.$module.'\\'.ucfirst($controller).'Controller';
            $actionName = 'action'.str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));

            if (is_callable([$className, $actionName])) {
                call_user_func_array([$className, $actionName], $params);
            } else {
                return true;
            }
        }, true);
    }

    /**
     * Get mysql database
     * @method getMysqlDb
     * @param  string $name [description]
     * @return [type]       [description]
     */
    public static function getMysqlDb($name = 'master.db')
    {
        if (!isset(self::$_dbInstances[$name])) {
            $db_host = Roc::get($name.'.host');
            $db_port = Roc::get($name.'.port');
            $db_user = Roc::get($name.'.user');
            $db_pass = Roc::get($name.'.pass');
            $db_name = Roc::get($name.'.name');
            $db_charset = Roc::get($name.'.charset');

            try {
                $pdo = new \PDO('mysql:host='.$db_host.';dbname='.$db_name.';port='.$db_port, $db_user, $db_pass);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $pdo->exec('SET CHARACTER SET '.$db_charset);

                $db = new DBEngine();
                $db->setDb($pdo);

                self::$_dbInstances[$name] = $db;
            } catch (Exception $e) {
                echo json_encode([
                    'code' => 500,
                    'msg' => 'Fail To Connect MySQL Server.',
                    'data'=> $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        return self::$_dbInstances[$name];
    }

    /**
     * Redis
     * @method getRedis
     * @return boolean  [description]
     */
    public static function getRedis()
    {
        try {
            $redis = new \Redis();

            $connect = $redis->connect(Roc::get('redis.host'), Roc::get('redis.port'));
        } catch (Exception $e) {
            echo json_encode([
                'code' => 500,
                'msg' => 'Please check if the Redis extension is installed, and the connection information is correct.',
                'data' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $isSuccess = true;

        if ($connect) {
            $auth = Roc::get('redis.auth');
            if (!empty($auth)) {
                if (!$redis->auth($auth)) {
                    $isSuccess = false;
                }
            }
        } else {
            $isSuccess = false;
        }

        if ($isSuccess === false) {
            echo json_encode([
                'code' => 500,
                'msg' => 'Redis server connection failed',
                'data' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $redis->select(Roc::get('redis.db'));

        return $redis;
    }

    /**
     * Remove the backslash
     * @method __stripslashesDeep
     * @param  [type]           $data [description]
     * @return [type]                 [description]
     */
    private static function __stripslashesDeep($data)
    {
        if (is_array($data)) {
            return array_map([__CLASS__, __FUNCTION__], $data);
        } else {
            return stripslashes($data);
        }
    }
}
