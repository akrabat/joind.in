<?php
/**
 * Api model base class
 *
 * PHP version 5
 *
 * @category Model
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */

/**
 * Api model base class
 *
 * @category Model
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class ApiModel
{
    /**
     * retrieve set default fields
     * 
     * @return array
     */
    public static function getDefaultFields()
    {
        return array();
    }
    
    /**
     * retrieve set of verbose fields
     * 
     * @return array
     */
    public static function getVerboseFields()
    {
        return array();
    }

    /**
     * Transform results
     * 
     * @param type    $results values to be transformed
     * @param boolean $verbose return verbose set of fields?
     * 
     * @return array 
     */
    public static function transformResults($results, $verbose)
    {
        $fields = $verbose ? static::getVerboseFields() : static::getDefaultFields();
        $retval = array();

        // format results to only include named fields
        foreach ($results as $row) {
            $entry = array();
            foreach ($fields as $key => $value) {
                // special handling for dates
                if (substr($key, -5) == '_date' && !empty($row[$value])) {
                    $entry[$key] = date('c', $row[$value]);
                } else {
                    $entry[$key] = mb_convert_encoding($row[$value], 'UTF-8');
                }
            }
            $retval[] = $entry;
        }
        return $retval;
    }

    /**
     * Build limit clause
     * 
     * @param int $resultsperpage number of results per page
     * @param int $start          number of first result
     * 
     * @return string 
     */
    protected static function buildLimit($resultsperpage, $start)
    {
        if ($resultsperpage == 0) {
            // special case, no limits
            $limit = '';
        } else {
            $limit = ' LIMIT '
                . $start . ','
                . $resultsperpage;
        }
        return $limit;
    }

    /**
     * Add pagination links to results list
     *
     * @param array   $list    results list
     * @param Request $request Request object
     * 
     * @return string 
     */
    protected static function addPaginationLinks($list, $request)
    {
        $base_url =  'http://' . $request->host . $request->path_info . '?';
        
        $list['meta']['count'] = count($list);
        
        $this_page = $base_url . http_build_query($request->parameters);
        $list['meta']['this_page'] = $this_page;
        
        $next_params = $prev_params = $request->parameters;
        $start = $next_params['start'] + $next_params['resultsperpage'];
        $next_params['start'] = $start;
        $list['meta']['next_page'] = $base_url . http_build_query($next_params);
        
        if ($prev_params['start'] >= $prev_params['resultsperpage']) {
            $start = $prev_params['start'] - $prev_params['resultsperpage'];
            $prev_params['start'] = $start;
            $list['meta']['prev_page'] = $base_url . http_build_query($prev_params);
        }
        
        return $list;
    }

}
