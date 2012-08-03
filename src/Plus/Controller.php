<?php
/**
 *
 */
class Plus_Controller
{
    /**
     * The home directory of model
     *
     * @var string
     */
    protected $_modelsHome = null;

    /**
     * The home directory of view
     *
     * @var string
     */
    protected $_viewsHome = null;

    /**
     * Template file extension
     *
     * @var string
     */
    protected $_tplExt = '.php';

    /**
     * Form keys
     *
     * @var array
     */
    protected $_keys = array();

    /**
     * Error
     *
     * @var array
     */
    protected $_error;

    /**
     * Constructor
     *
     * Init $_modelsHome & $_viewsHome from config if they are null
     */
    public function __construct()
    {
        if (null === $this->_modelsHome) {
            $this->_modelsHome = Plus::config('_modelsHome');
        }
        if (null === $this->_viewsHome) {
            $this->_viewsHome = Plus::config('_viewsHome');
        }
    }

    /**
     * Magic method
     *
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        throw new Exception("Call to undefined method: Plus_Controller::$methodName()");
    }

    /**
     * View
     *
     * @param array $config
     * @return Plus_View
     */
    protected function view($params = array())
    {
        $params = (array)$params + array('basePath' => $this->_viewsHome) + (array) Plus::config('_view');

        return $this->view = new Plus_View($params);
    }

    /**
     * Display the view
     *
     * @param string $tpl
     */
    protected function display($tpl = null, $dir = null)
    {
        if (empty($tpl)) $tpl = $this->defaultTemplate();

        $this->view->display($tpl, $dir);
    }

    /**
     * Get default template file path
     *
     * @return string
     */
    protected function defaultTemplate()
    {
        $plus = Plus::getInstance();
        $dispatchInfo = $plus->getDispatchInfo();

        $tpl = str_replace('_', DIRECTORY_SEPARATOR, substr($dispatchInfo['controller'], 0, -10))
             . DIRECTORY_SEPARATOR
             . substr($dispatchInfo['action'], 0, -6)
             . $this->_tplExt;

        return $tpl;
    }

    /**
     * Instantiated model
     *
     * @param string $name
     * @param string $dir
     * @return Plus_Model
     */
    protected function model($name = null, $dir = null)
    {
        if (null === $name) {
            return $this->model;
        }

        null === $dir && $dir = $this->_modelsHome;
        $class = ucfirst($name) . 'Model';
        if (Plus::loadClass($class, $dir)) {
            return new $class();
        }

        throw new exception("Can't load model '$class' from '$dir'");
    }

    /**
     * Set model home directory
     *
     * @param string $dir
     * @return Plus_Controller
     */
    protected function setModelsHome($dir)
    {
        $this->_modelsHome = $dir;
        return $this;
    }

    /**
     * Set view home directory
     *
     * @param string $dir
     * @return Plus_Controller
     */
    protected function setViewsHome($dir)
    {
        $this->_viewsHome = $dir;
        return $this;
    }

    /**
     * Get var
     *
     * @param sting $key
     * @param mixed $default
     */
    protected function getVar($key = null, $default = null)
    {
        if (null === $key) {
            return array_merge(Plus::reg('_params', null, array()), $_GET, $_POST, $_COOKIE, $_SERVER, $_ENV);
        }

        $funcs = array('param', 'get', 'post', 'cookie', 'server', 'env');

        foreach ($funcs as $func) {
            if (null !== ($return = $this->request->$func($key))) return $return;
        }

        return $default;
    }

    /**
     * Post var
     *
     * @param string $key
     * @param mixed $default
     */
    protected function post($key = null, $default = null)
    {
        return $this->request->post($key, $default);
    }

    /**
     * Get var
     *
     * @param string $key
     * @param mixed $default
     */
    protected function get($key = null, $default = null)
    {
        return $this->request->get($key, $default);
    }

    /**
     * Get data from form
     *
     * @param array $keys
     * @param string $method
     * @return array
     */
    protected function form($keys = null, $method = 'post')
    {
        $data = array();

        if (null === $keys && !$keys = $this->_keys) {
            return $this->request->{$method}();
        }

        if (isset($keys[0])) {
            foreach ($keys as $v) {
                $fKeys[$v] = $v;
            }
        }

        foreach ($fKeys as $k => $v) {
            $tmp = $this->request->{$method}($k);
            if (null !== $tmp) $data[$v] = trim($tmp);
        }

        return $data;
    }

    /**
     * Redirect to other url
     *
     * @param string $url
     */
    protected function redirect($url, $code = 302)
    {
        $this->response->redirect($url, $code);
    }

    /**
     * Dynamic set vars
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value = null)
    {
        $this->$key = $value;
    }

    /**
     * Dynamic get vars
     *
     * @param string $key
     */
    public function __get($key)
    {
        switch ($key) {
            case 'view':
                $this->view();
                return $this->view;

            case 'model':
                $class = get_class($this);
                $this->model = $this->model(substr($class, 0, -10));
                return $this->model;

            case 'helper':
                $this->helper = new Plus_Helper();
                return $this->helper;

            case 'com':
                $this->com = new Plus_Com();
                return $this->com;

            case 'request':
                $this->request = new Plus_Request();
                return $this->request;

            case 'response':
                $this->response = new Plus_Response();
                return $this->response;

            default:
                throw new Plus_Exception('Undefined property: ' . get_class($this) . '::' . $key);
        }
    }
}
