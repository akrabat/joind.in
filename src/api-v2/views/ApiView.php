<?php
/**
 * Api view base class
 *
 * PHP version 5
 *
 * @category View
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */

/**
 * Api view base class
 *
 * @category View
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class ApiView
{
    /**
     * Ensure that $data['meta']['count'] exists
     * 
     * @param int $data data to be rendered
     * 
     * @return int 
     */
    protected function addCount($data) 
    {
        if (is_array($data)) {
            // do nothing, this is added earlier
        } else {
            $data['meta']['count'] = 0;
        }
        return $data;
    }
}
