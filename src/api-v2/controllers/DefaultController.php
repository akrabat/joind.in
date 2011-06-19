<?php
/**
 * Default controller
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
 * Default controller
 *
 * @category Controller
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class DefaultController
{
    /**
     * Handle request
     *
     * @param Request $request request
     * @param PDO     $db      database adapater
     * 
     * @return array|false
     */
    public function handle($request, $db)
    {
        $retval = array();

        // just add the available methods, with links
        $retval['events'] = 'http://' . $request->host . '/v2/events';

        return $retval;
    }
}
