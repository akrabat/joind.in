<?php
/**
 * Events controller
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
 * Events controller
 *
 * @category Controller
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class EventsController extends ApiController
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
        // only GET is implemented so far
        if ($request->verb == 'GET') {
            return $this->getAction($request, $db);
        }
        return false;
    }
    
    /**
     * get action
     *
     * @param Request $request request
     * @param PDO     $db      database adapater
     * 
     * @return array|false
     */
    public function getAction($request, $db)
    {
        $event_id = $this->getItemId($request);

        // verbosity
        $verbose = $this->getVerbosity($request);

        // pagination settings
        $start = $this->getStart($request);
        $resultsperpage = $this->getResultsPerPage($request);

        if (isset($request->url_elements[4])) {
            switch ($request->url_elements[4]) {
            case 'talks':
                $list = TalkModel::getTalksByEventId(
                    $db, $event_id, $resultsperpage, $start, $verbose
                );
                $list = TalkModel::addHypermedia($list, $request);
                break;
            case 'comments':
                $list = EventCommentModel::getEventCommentsByEventId(
                    $db, $event_id, $resultsperpage, $start, $verbose
                );
                break;
            default:
                throw new InvalidArgumentException('Unknown Subrequest', 404);
                break;
            }
        } else {
            if ($event_id) {
                $list = EventModel::getEventById($db, $event_id, $verbose);
            } else {
                $list = EventModel::getEventList(
                    $db, $resultsperpage, $start, $verbose
                );
            }
            // add links
            $list = EventModel::addHypermedia($list, $request);
        }

        return $list;
    }
}
