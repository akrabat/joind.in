<?php
/**
 * JSON view class: renders JSON
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
 * HTML View class: renders HTML 5
 *
 * @category View
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
class JsonView extends ApiView
{
    /**
     * Render the view
     *
     * @param array $content data to be rendered
     *
     * @return bool
     */
    public function render($content)
    {
        header('Content-Type: application/json; charset=utf8');
        $content = $this->addCount($content);
        echo json_encode($content);
        return true;
    }
}
