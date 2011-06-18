<?php
/**
 * Event model
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
 * Event model
 *
 * @category Model
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class EventModel extends ApiModel
{
    /**
     * retrieve set default fields
     * 
     * @return array
     */    
    public static function getDefaultFields()
    {
        $fields = array(
            'event_id' => 'ID',
            'name' => 'event_name',
            'start_date' => 'event_start',
            'end_date' => 'event_end',
            'description' => 'event_desc',
            'href' => 'event_href',
            'icon' => 'event_icon'
            );
        return $fields;
    }
    
    /**
     * retrieve set of verbose fields
     * 
     * @return array
     */
    public static function getVerboseFields()
    {
        $fields = array(
            'event_id' => 'ID',
            'name' => 'event_name',
            'start_date' => 'event_start',
            'end_date' => 'event_end',
            'description' => 'event_desc',
            'href' => 'event_href',
            'icon' => 'event_icon',
            'latitude' => 'event_lat',
            'longitude' => 'event_long',
            'tz_continent' => 'event_tz_cont',
            'tz_place' => 'event_tz_place',
            'location' => 'event_loc',
            'cfp_start_date' => 'event_cfp_start',
            'cfp_end_date' => 'event_cfp_end',
            'cfp_url' => 'event_cfp_url'
            );
        return $fields;
    }

    /**
     * Get event
     *
     * @param PDO     $db       database adapater
     * @param int     $event_id id of event
     * @param boolean $verbose  return verbose set of fields?
     * 
     * @return array|false 
     */
    public static function getEventById($db, $event_id, $verbose = false)
    {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . '(pending = 0 or pending is NULL) and '
            . 'ID = :event_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(':event_id' => $event_id));
        
        if ($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;

    }
    

    /**
     * Get list of events
     *
     * @param PDO     $db             database adapter
     * @param int     $resultsperpage number of results per page
     * @param int     $start          start index
     * @param boolean $verbose        return verbose set of fields?
     * 
     * @return array|false
     */
    public static function getEventList(
        $db, $resultsperpage, $start, $verbose = false
    ) {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . '(pending = 0 or pending is NULL) and '
            . 'private <> "y" '
            . 'order by event_start desc';
        $sql .= static::buildLimit($resultsperpage, $start);

        $stmt = $db->prepare($sql);
        $response = $stmt->execute();
        if ($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Add hypermedia elements to each item in the list
     *
     * @param array   $list    list of results
     * @param Request $request Request object
     * 
     * @return array 
     */
    public static function addHyperMedia($list, $request)
    {
        $host = $request->host;
        
        $base_url = 'http://' . $host . '/v2/events/';

        // add per-item links 
        if (is_array($list) && count($list)) {
            foreach ($list as $key => $row) {
                $id = $row['event_id'];
                
                $list[$key]['uri'] = $base_url . $id;
                $list[$key]['verbose_uri'] = $base_url . $id . '?verbose=yes';
                $list[$key]['comments_link'] = $base_url . $id . '/comments';
                $list[$key]['talks_link'] = $base_url . $id . '/talks';
            }

            if (count($list) > 1) {
                // add pagination and global links
                $list = static::addPaginationLinks($list, $request);
            }
        }

        return $list;
    }

}
