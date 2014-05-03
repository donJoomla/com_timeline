<?php
error_reporting(E_ALL);
date_default_timezone_set('UTC');
define('_JEXEC', 1);
define('JOOMLA_PATH',realpath(dirname(__FILE__).'/../../../'));
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '';
 
if (file_exists(JOOMLA_PATH . '/defines.php'))
{
    include_once JOOMLA_PATH . '/defines.php';
}
 
if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', JOOMLA_PATH);
    require_once JPATH_BASE . '/includes/defines.php';
}
 
require_once JPATH_BASE . '/includes/framework.php';
 
$app = JFactory::getApplication('site');
define('JPATH_COMPONENT',JOOMLA_PATH.'/components/com_timeline');
