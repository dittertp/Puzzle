<?php

/**
 * Puzzle\Interfaces\ClientInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Puzzle
 * @package   TechDivision_Puzzle
 * @author    Philipp Dittert <pd@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace Puzzle\Interfaces;

/**
 * class ClientInterface
 *
 * @category  Puzzle
 * @package   TechDivision_Puzzle
 * @author    Philipp Dittert <pd@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
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
