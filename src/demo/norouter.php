<?php
error_reporting(E_ALL);

define('APP_DIR', dirname(__FILE__));

require '../Plus/Plus.php';

require './config.inc.php';

$plus = Plus::getInstance();

// 获得类名
if (isset($_GET['c'])) {
    $className = ucfirst($_GET['c']) . 'Controller';
} else {
    $className = 'IndexController';
}

// 获得方法名
if (isset($_GET['a'])) {
    $actionName = $_GET['a'] . 'Action';
} else {
    $actionName = 'indexAction';
}

try {
    Plus::loadClass($className, './controllers');
    $dispatchInfo = array(
        'controller' => $className,
        'action' => $actionName
    );

    $plus->setDispatchInfo($dispatchInfo);

    $plus->dispatch();
} catch (Exception $e) {
    // 处理404
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    header('Location:404.html');
}