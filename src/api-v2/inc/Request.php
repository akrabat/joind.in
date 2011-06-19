<?php
/**
 * Request
 *
 * PHP version 5
 *
 * @category Inc
 * @package  API
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */

/**
 * Request
 *
 * @category Inc
 * @package  API
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class Request
{
    public $verb;
    public $url_elements;
    public $path_info;
    public $accept;
    public $host;
    public $parameters = array();
    public $view;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        
        if (isset($_SERVER['PATH_INFO'])) {
            $this->url_elements = explode('/', $_SERVER['PATH_INFO']);
            $this->path_info = $_SERVER['PATH_INFO'];
        }
        
        $this->accept = explode(',', $_SERVER['HTTP_ACCEPT']);
        $this->host = $_SERVER['HTTP_HOST'];
        
        parse_str($_SERVER['QUERY_STRING'], $parameters);
        $this->parameters = $parameters;
    }

    /**
     * Get a parameter
     *
     * @param string $param   parameter
     * @param string $default default value if parameter doesn't exist
     * 
     * @return string
     */
    public function getParameter($param, $default = '')
    {
        $value = $default;
        if (isset($this->parameters[$param])) {
            $value = $this->parameters[$param];
        }
        return $value;
    }
    
    /**
     * Get URL element
     *
     * @param string $index   index within url element list
     * @param string $default default value if parameter doesn't exist
     * 
     * @return string
     */
    public function getUrlElement($index, $default = '') 
    {
        $index = (int)$index;
        $element = $default;
        
        if (isset($this->url_elements[$index])) {
            $element = $this->url_elements[$index];
        }

        return $element;
    }
    
    /**
     * Does the client accept a given header?
     *
     * @param string $header header
     * 
     * @return boolean
     */
    public function accepts($header)
    {
        $result = false;
        foreach ($this->accept as $accept) {
            if (strstr($accept, $header) !== false) {
                return true;
            }
        }
    }
    
    /**
     * Determine if one of the accept headers matches one of the desired
     * formats
     * 
     * @param array $formats list of formats
     * 
     * @return string
     */
    public function preferredContentTypeOutOf($formats)
    {
        foreach ($formats as $format) {
            if ($this->accepts($format)) {
                return $format;
            }
        }
        
        return 'json';
    } 
}
