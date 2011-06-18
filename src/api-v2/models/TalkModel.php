<?php
/**
 * Talk model
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
 * Talk model
 *
 * @category Model
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class TalkModel extends ApiModel
{
    /**
     * retrieve set default fields
     * 
     * @return array
     */
    public static function getDefaultFields()
    {
        $fields = array(
            'talk_id' => 'ID',
            'event_id' => 'event_id',
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'start_date' => 'date_given',
            'speaker_name' => 'speaker_name'
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
            'talk_id' => 'ID',
            'event_id' => 'event_id',
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'slides_link' => 'slides_link',
            'language' => 'lang_name',
            'start_date' => 'date_given',
            'speaker_name' => 'speaker_name'
            );
        return $fields;
    }
    
    /**
     * Get list of talks for this event
     *
     * @param PDO     $db             database adapter
     * @param int     $event_id       id of event
     * @param int     $resultsperpage number of results per page
     * @param int     $start          start index
     * @param boolean $verbose        return verbose set of fields?
     * 
     * @return type 
     */
    public static function getTalksByEventId($db, $event_id, $resultsperpage, 
        $start, $verbose = false
    ) {
        $sql = static::getBasicSQL();
        $sql .= ' and t.event_id = :event_id';
        $sql .= static::buildLimit($resultsperpage, $start);

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
        $base_url = 'http://' . $host . '/v2/talks/';

        // loop again and add links specific to this item
        if (is_array($list) && count($list)) {
            foreach ($list as $key => $row) {
                $id = $row['talk_id'];
                $list[$key]['uri'] = $base_url . $id;
                $list[$key]['verbose_uri'] = $base_url . $id . '?verbose=yes';
                $list[$key]['comments_link'] = $base_url . $id . '/comments';
                $list[$key]['event_link'] = $base_url . $id;
            }

            if (count($list) > 1) {
                $list = static::addPaginationLinks($list, $request);
            }
        }

        return $list;
    }

    /**
     * Get a single talk
     *
     * @param PDO     $db      database adapter
     * @param int     $talk_id id of event
     * @param boolean $verbose return verbose set of fields?
     * 
     * @return array|false 
     */
    public static function getTalkById($db, $talk_id, $verbose = false)
    {
        $sql = static::getBasicSQL();
        $sql .= ' and t.ID = :talk_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array("talk_id" => $talk_id));
        if ($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    /**
     * Get basic SQL
     *
     * @return string 
     */
    public static function getBasicSQL()
    {
        $sql = 'select t.*, l.lang_name, ts.speaker_name from talks t '
            . 'inner join events e on e.ID = t.event_id '
            . 'inner join lang l on l.ID = t.lang '
            . 'left join talk_speaker ts on ts.talk_id = t.ID '
            . 'where t.active = 1 and '
            . 'e.active = 1 and '
            . '(e.pending = 0 or e.pending is NULL) and '
            . 'e.private <> "y"';
        return $sql;

    }
}
