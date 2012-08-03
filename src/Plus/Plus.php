<?php
/**
 *
 */

/**
 * Define
 */
defined('PLUS_DIR') || define('PLUS_DIR', dirname(__FILE__));
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

require_once PLUS_DIR . '/Config.php';

class Plus
{
    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Plus_Dispatcher
     */
    protected static $_instance = null;

    /**
     * Run time config
     *
     * @var Plus_Config
     */
    public static $config;

    /**
     * Run time config's reference, for better performance
     *
     * @var array
     */
    protected static $_config;

    /**
     * Object register
     *
     * @var array
     */
    protected static $_reg = array();

    /**
     * Router
     *
     * @var Plus_Router
     */
    protected $_router;

    /**
     * Path info
     *
     * @var string
     */
    protected $_pathInfo = null;

    /**
     * Dispathc info
     *
     * @var array
     */
    protected $_dispatchInfo = null;

    /**
     * Constructor
     *
     */
    protected function __construct()
    {
        $config['_class'] = array(
            'Plus_Router'      => PLUS_DIR . '/Router.php',
            'Plus_Model'       => PLUS_DIR . '/Model.php',
            'Plus_View'        => PLUS_DIR . '/View.php',
            'Plus_Controller'  => PLUS_DIR . '/Controller.php',
            'Plus_Com'         => PLUS_DIR . '/Com.php',
            'Plus_Com_Widget'  => PLUS_DIR . '/Com/Widget.php',
            'Plus_Exception'   => PLUS_DIR . '/Exception.php'
        );

        self::$config = new Plus_Config($config);
        self::$_config = &self::$config->reference();

        Plus::registerAutoload();
    }

    /**
     * Bootstrap
     *
     * @param mixed $arg string as a file and array as config
     * @return Plus
     */
    public static function boot($config = 'config.inc.php')
    {
        if (is_string($config)) {
            include $config;
        }

        if (!is_array($config)) {
            throw new Exception('Boot config must be an array, if you use config file, the variable should be named $config');
        }

        self::$config->merge($config);
        return self::$_instance;
    }

    /**
     * Singleton instance
     *
     * @return Plus
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Get Config
     *
     * @param string $name
     * @param mixed $default
     * @param mixed $delimiter
     * @return mixed
     */
    public static function config($name = null, $default = null, $delimiter = '.')
    {
        return self::$config->get($name, $default, $delimiter);
    }

    /**
     * Register
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public static function reg($name = null, &$value = null, $default = null)
    {
        if (null === $name) {
            return self::$_reg;
        }

        if (null === $value) {
            return isset(self::$_reg[$name]) ? self::$_reg[$name] : $default;
        }

        self::$_reg[$name] = $value;
        return self::$_instance;
    }

    /**
     * Load class
     *
     * @param string $className
     * @param string $dir
     * @return boolean
     */
    public static function loadClass($className, $dir = '')
    {
        if (class_exists($className, false) || interface_exists($className, false)) {
            return true;
        }

        if (isset(self::$_config['_class'][$className])) {
            include self::$_config['_class'][$className];
            return true;
        }

        if (empty($dir)) {
            $dir = ('Plus' == substr($className, 0, 4)) ? substr(PLUS_DIR, 0, -4) : '';
        } else {
            $dir = rtrim($dir,'\\/') . DIRECTORY_SEPARATOR;
        }

        $file = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        $classFile = $dir . $file;

        if (file_exists($classFile)) {
            include $classFile;
        }

        return (class_exists($className, false) || interface_exists($className, false));
    }

    /**
     * User define class path
     *
     * @param array $classPath
     * @return Plus
     */
    public static function setClassPath($class, $path = '')
    {
        if (is_array($class)) {
            self::$_config['_class'] = $class + self::$_config['_class'];
        } else {
            self::$_config['_class'][$class] = $path;
        }

        return self::$_instance;
    }

    /**
     * Register autoload function
     *
     * @param string $func
     * @param boolean $enable
     */
    public static function registerAutoload($func = 'Plus::loadClass', $enable = true)
    {
        $enable ? spl_autoload_register($func) : spl_autoload_unregister($func);
    }

    /**
     * Set router
     *
     * @param Plus_Router $router
     * @return Plus
     */
    public function setRouter($router = null)
    {
        if (null === $router) {
            $router = Plus_Router::getInstance();
        }

        $this->_router = $router;

        return $this;
    }

    /**
     * Get router
     *
     * @return Plus_Router
     */
    public function getRouter()
    {
        if (null === $this->_router) {
            $this->setRouter();
        }

        return $this->_router;
    }

    /**
     * Set path info
     *
     * @param string $pathinfo
     * @return Plus
     */
    public function setPathInfo($pathinfo = null)
    {
        if (null === $pathinfo) {
            $pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        }

        $this->_pathInfo = $pathinfo;

        return $this;
    }

    /**
     * Get path info
     *
     * @return string
     */
    public function getPathInfo()
    {
        if (null === $this->_pathInfo) {
            $this->setPathInfo();
        }

        return $this->_pathInfo;
    }

    /**
     * Set dispatch info
     *
     * @param array $dispatchInfo
     * @return Plus
     */
    public function setDispatchInfo($dispatchInfo = null)
    {
        if (null === $dispatchInfo) {
            $router = $this->getRouter();
            // add urls to router from config
            if (isset(self::$_config['_urls'])) $router->add(self::$_config['_urls'], false);
            $pathInfo = $this->getPathInfo();
            $dispatchInfo = $router->match($pathInfo);
        }

        $this->_dispatchInfo = $dispatchInfo;

        return $this;
    }

    /**
     * Get dispatch info
     *
     * @return array
     */
    public function getDispatchInfo()
    {
        if (null === $this->_dispatchInfo) {
            $this->setDispatchInfo();
        }

        return $this->_dispatchInfo;
    }

    /**
     * Dispatch
     *
     */
    public function dispatch()
    {
        if (!$this->getDispatchInfo()) {
            throw new Plus_Exception_Dispatch('No dispatch info found');
        }

        extract($this->_dispatchInfo);

        if (isset($file)) {
            if (!file_exists($file)) {
                throw new Plus_Exception_Dispatch("Can't find dispatch file:{$file}");
            }
            require_once $file;
        }

        if (isset($controller)) {
            if (!self::loadClass($controller, self::config('_controllersHome'))) {
                throw new Plus_Exception_Dispatch("Can't load controller:{$controller}");
            }
            $cls = new $controller();
        }

        if (isset($action)) {
            $func = isset($cls) ? array($cls, $action) : $action;
            if (!is_callable($func, true)) {
                throw new Plus_Exception_Dispatch("Can't dispatch action:{$action}");
            }
            call_user_func($func);
        }
    }
}