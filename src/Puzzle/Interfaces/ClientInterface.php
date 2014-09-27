<?php

namespace Puzzle\Interfaces;

interface ClientInterface
{

	/**
	 * Perform a GET call to server
	 *
	 * @param string $url The url to make the call to.
	 * @param array $http_options Extra option to pass to curl handle.
	 * @return string The response from curl if any
	 */
	public function get($url, $http_options = array());

	/**
	 * Perform a POST call to the server
	 *
	 * @param string $url The url to make the call to.
	 * @param string|array The data to post. Pass an array to make a http form post.
	 * @param array $http_options Extra option to pass to curl handle.
	 * @return string The response from curl if any
	 */
    public function post($url, $fields = array(), $http_options = array());

	/**
	 * Perform a PUT call to the server
	 *
	 * @param string $url The url to make the call to.
	 * @param string|array The data to post.
	 * @param array $http_options Extra option to pass to curl handle.
	 * @return string The response from curl if any
	 */
    public function put($url, $data = '', $http_options = array());

	/**
	 * Perform a DELETE call to server
	 *
	 * @param string $url The url to make the call to.
	 * @param array $http_options Extra option to pass to curl handle.
	 * @return string The response from curl if any
	 */
    public function delete($url, $http_options = array());
}