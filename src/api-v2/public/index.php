<?php

/**
 * Front controller for API v2
 *
 * PHP version 5
 *
 * @category Public
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */

require '../inc/Request.php';

/**
 * Autoloader
 * 
 * @param string $classname name of class to load
 * 
 * @return boolean
 */
function __autoload($classname) 
{
    if (false !== strpos($classname, '.')) {
        // this was a filename, don't bother
        exit;
    }

    if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
        include '../controllers/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
        include '../models/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+View$/', $classname)) {
        include '../views/' . $classname . '.php';
        return true;
    }
}


/**
 * Exception handler
 * 
 * @param Exception $e exception
 * 
 * @global Request $request request object
 * 
 * @return void
 */
function handleException($e)
{
    // pull the correct format before we bail
    global $request;
    header("Status: " . $e->getCode(), false, $e->getCode());
    $request->view->render(array($e->getMessage()));
}
set_exception_handler('handleException');

// config setup
define('BASEPATH', '.');
require '../database.php';
$ji_db = new PDO(
    'mysql:host=' . $db['default']['hostname'] . 
    ';dbname=' . $db['default']['database'],
    $db['default']['username'],
    $db['default']['password']
);

// collect URL and headers
$request = new Request();

// set some default parameters
$resultsPerPage = $request->getParameter('resultsperpage', 20);
$start = $request->getParameter('start', 0);
$request->parameters['resultsperpage'] = $resultsPerPage;
$request->parameters['start'] = $start;


// Which content type to return? Parameter takes precedence over accept headers 
// with final fall back to json 
$format_choices = array('application/json', 'text/html');
$header_format = $request->preferredContentTypeOutOf($format_choices);
$format = $request->getParameter('format', $header_format);

switch ($format) {
case 'text/html':
case 'html':
    $request->view = new HtmlView();
    break;

case 'application/json':
case 'json':
default:
    $request->view = new JsonView();
    break;
}

$version = $request->getUrlElement(1);
switch ($version) {
case 'v2':
    // default routing for version 2
    $return_data = routeV2($request, $ji_db);
    break;

case '':
    // version parameter not specified routes to default controller
    $defaultController = new DefaultController();
    $return_data = $defaultController->handle($request, $ji_db);
    break;

default:
    // unexpected version
    throw new Exception('API version must be specified', 404);
    break;
}

// Handle output
// TODO sort out headers, caching, etc
$request->view->render($return_data);
exit;

/**
 * Router for version 2
 * 
 * @param Request $request request object
 * @param PDO     $ji_db   database adapter
 * 
 * @return array
 */
function routeV2($request, $ji_db)
{
    $return_data = false;
    if (isset($request->parameters['oauth_version']) 
        && ($request->url_elements[2] != 'oauth')
    ) {
        $oauth_model = new OAuthModel();
        $oauth_model->in_flight = true;
        $oauth_model->setUpOAuthAndDb($ji_db);
        $request->user_id = $oauth_model->user_id;
    }

    // Route: call the handle() method of the class with the first URL element
    if (isset($request->url_elements[2])) {
        $class = ucfirst($request->url_elements[2]) . 'Controller';
        if (class_exists($class)) {
            $handler = new $class();
            $return_data = $handler->handle($request, $ji_db);
        } else {
            $controller = $request->url_elements[2];
            throw new Exception("Unknown controller $controller", 400);
        }
    } else {
        throw new Exception('Request not understood', 404);
    }
    
    return $return_data;
}


