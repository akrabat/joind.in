<?php
/**
 * Event comment model
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
* Event comment model
 *
 * @category Model
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class EventCommentModel extends ApiModel
{
    /**
     * retrieve set default fields
     * 
     * @return array
     */
    public static function getDefaultFields()
    {
        $fields = array(
            'comment_id' => 'ID',
            'event_id' => 'event_id',
            'user_id' => 'user_id',
            'comment' => 'comment'
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
            'comment_id' => 'ID',
            'event_id' => 'event_id',
            'user_id' => 'user_id',
            'comment' => 'comment',
            'created_date' => 'date_made'
            );
        return $fields;
    }

    /**
     * Get comments for this event
     * 
     * @param PDO     $db       database adapater
     * @param int     $event_id id of event
     * @param boolean $verbose  return verbose set of fields?
     * 
     * @return array|false
     */
    public static function getEventCommentsByEventId(
        $db,
        $event_id, 
        $verbose = false
    ) {
        $sql = 'select * from event_comments where event_id = :event_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(
            array(':event_id' => $event_id)
        );
        
        if ($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        
        return false;
    }

}
