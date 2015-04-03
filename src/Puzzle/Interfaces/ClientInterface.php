<?php

/**
 * Puzzle\Interfaces\ClientInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to version 3 of the GPL license,
 * that is bundled with this package in the file LICENSE, and is
 * available online at http://www.gnu.org/licenses/gpl.txt
 *
 * PHP version 5
 *
 * @category  Puzzle
 * @package   Puzzle
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2015 Philipp Dittert
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/dittertp/Puzzle
 */

namespace Puzzle\Interfaces;

/**
 * class ClientInterface
 *
 * @category  Puzzle
 * @package   Puzzle
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2015 Philipp Dittert
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/dittertp/Puzzle
 */

interface ClientInterface
{
    /**
     * Perform a GET call to server
     *
     * @param string $url         The url to make the call to.
     * @param array  $httpOptions Extra option to pass to curl handle.
     *
     * @return string The response from curl if any
     */
    public function get($url, $httpOptions = array());

    /**
     * Perform a POST call to the server
     *
     * @param string $url         The url to make the call to.
     * @param mixed  $fields      The data to post. Pass an array to make a http form post.
     * @param array  $httpOptions Extra option to pass to curl handle.
     *
     * @return string The response from curl if any
     */
    public function post($url, $fields = array(), $httpOptions = array());

    /**
     * Perform a PUT call to the server
     *
     * @param string $url         The url to make the call to.
     * @param mixed  $data        The data to post.
     * @param array  $httpOptions Extra option to pass to curl handle.
     *
     * @return string The response from curl if any
     */
    public function put($url, $data = '', $httpOptions = array());

    /**
     * Perform a DELETE call to server
     *
     * @param string $url         The url to make the call to.
     * @param array  $httpOptions Extra option to pass to curl handle.
     *
     * @return string The response from curl if any
     */
    public function delete($url, $httpOptions = array());
}
