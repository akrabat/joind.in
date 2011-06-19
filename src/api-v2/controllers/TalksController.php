<?php
/**
 * Talks controller
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
 * Talks controller
 *
 * @category Controller
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class TalksController extends ApiController
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
        // only GET is implemented
        if ($request->verb == 'GET') {
                return $this->getAction($request, $db);
        }
        // should not end up here
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
        $talk_id = $this->getItemId($request);

        // verbosity
        $verbose = $this->getVerbosity($request);

        /*
        // pagination settings
        $start = $this->getStart($request);
        $resultsperpage = $this->getResultsPerPage($request);
        */

        if (isset($request->url_elements[4])) {
            // sub elements
        } else {
            if ($talk_id) {
                $list = TalkModel::getTalkById($db, $talk_id, $verbose);
            } else {
                // listing makes no sense
                return false;
            }
            // add links
            $list = TalkModel::addHypermedia($list, $request);
        }

        return $list;
    }
}
