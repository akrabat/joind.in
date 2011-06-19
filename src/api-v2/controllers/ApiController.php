<?php
/**
 * Base controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */

/**
 * Base controller
 *
 * @category Controller
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
abstract class ApiController
{
    /**
     * Entry point to controller. Handle the request.
     * 
     * @param Request $request request
     * @param PDO     $db      db adapter
     * 
     * @return array|false
     *
     */
    abstract public function handle($request, $db);

    /**
     * Get item id
     *
     * @param Request $request request
     * 
     * @return int|false
     */
    public function getItemId($request)
    {
        // item ID
        if (!empty($request->url_elements[3]) 
            && is_numeric($request->url_elements[3])
        ) {
            $item_id = (int)$request->url_elements[3];
            return $item_id;
        }
        return false;
    }

    /**
     * Get the verbosity setting from the Request
     *
     * @param Request $request request
     * 
     * @return boolean 
     */
    public function getVerbosity($request)
    {
        // verbosity
        if (isset($request->parameters['verbose'])
            && $request->parameters['verbose'] == 'yes'
        ) {
            $verbose = true;
        } else {
            $verbose = false;
        }
        return $verbose;
    }

    /**
     * Get the start setting from the Request
     *
     * @param Request $request request
     * 
     * @return int
     */
    public function getStart($request)
    {
        return (int)$request->parameters['start'];
         
    }
    
    /**
     * Get the results per page setting from the Request
     *
     * @param Request $request request
     * 
     * @return int
     */
    public function getResultsPerPage($request)
    {
        return (int)$request->parameters['resultsperpage'];
    }
}
