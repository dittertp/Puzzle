<?php

/**
 * Puzzle\Client
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

namespace Puzzle;

use Puzzle\Exceptions\ConnectionException;
use Puzzle\Interfaces\ClientInterface;
use Puzzle\Interfaces\SerializerInterface;
use Puzzle\Exceptions\ConfigurationException;
use Puzzle\Exceptions\ClientErrorResponseException;
use Puzzle\Exceptions\ClientErrorException;
use Puzzle\Exceptions\TransportException;
use Puzzle\Exceptions\ServerErrorResponseException;
use Puzzle\Exceptions\ServerErrorException;
use Puzzle\Exceptions\InvalidRequestMethodException;
use Puzzle\Exceptions\InvalidRequestException;
use Puzzle\Serializer\DefaultSerializer;

/**
 * class Client
 *
 * @category  Puzzle
 * @package   TechDivision_Puzzle
 * @author    Philipp Dittert <pd@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

class Client
{
    /**
     * @var string
     */
    const SCHEME_SSL = "https://";

    /**
     * @var string
     */
    const SCHEME_PLAIN = "http://";

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var resource
     */
    protected $handle;

    /**
     * @var array
     */
    protected $httpOptions;

    /**
     * @var array
     */
    protected $httpHeaders;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * creates class instance
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * resets all attributes
     *
     * @return void
     */
    public function reset()
    {
        $this->init();
    }

    /**
     * initialize default values
     *
     * @return void
     * @throws Exceptions\ServerErrorException
     */
    protected function init()
    {
        $handle = curl_init();
        if ($handle === false) {
            throw new ServerErrorException("curl extension not loaded");
        }
        $this->handle = $handle;

        $this->port = null;
        $this->host = null;

        $this->setSerializer(new DefaultSerializer());
        $this->scheme = self::SCHEME_PLAIN;
        $this->httpOptions = array();
        $this->httpOptions[CURLOPT_RETURNTRANSFER] = true;
        $this->httpOptions[CURLOPT_FOLLOWLOCATION] = false;
    }

    /**
     * Sets a serializer instance
     *
     * @param SerializerInterface $serializer the serializer instance
     *
     * @return void
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Returns serializer instance
     *
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Returns curl resource
     *
     * @return resource
     */
    protected function getHandle()
    {
        return $this->handle;
    }

    /**
     * Sets username and password for basic authentication
     *
     * @param string $username the username
     * @param string $password the password
     *
     * @return void
     */
    public function setAuthentication($username, $password)
    {
        $this->httpOptions[CURLOPT_USERPWD] = $username . ":" . $password;
    }

    /**
     * Sets hostname and optional port
     *
     * @param string  $host the hostname
     * @param integer $port the port
     *
     * @return void
     * @throws \Puzzle\Exceptions\ConfigurationException
     */
    public function setHost($host, $port = null)
    {
        $this->host = $host;
        if ($port !== null) {
            if (is_numeric($port)) {
                $this->port = $port;
            } else {
                throw new ConfigurationException("Port '{$port}' is not numeric");
            }
        }
    }

    /**
     * closes curl resource
     *
     * @return void
     */
    protected function close()
    {
        curl_close($this->getHandle());
    }

    /**
     * Return's http status response code
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return curl_getinfo($this->getHandle(), CURLINFO_HTTP_CODE);
    }

    /**
     * performs the request
     *
     * @param string $method request method
     * @param string $uri    uri string
     * @param mixed  $params optional query string parameters
     * @param mixed  $body   "post" body
     *
     * @return mixed
     * @throws \Puzzle\Exceptions\ServerErrorResponseException
     * @throws \Puzzle\Exceptions\ClientErrorResponseException
     */
    public function performRequest($method, $uri, $params = null, $body = null)
    {
        try {

            // serialize(json) body if it's not already a string
            $body = $this->getSerializer()->serialize($body);

            return $this->processRequest(
                $method,
                $uri,
                $params,
                $body
            );

        } catch (ClientErrorResponseException $exception) {
            throw $exception;   //We need 4xx errors to go straight to the user, no retries

        } catch (ServerErrorResponseException $exception) {
            throw $exception;   //We need 5xx errors to go straight to the user, no retries
        }
    }

    /**
     * perform a get request
     *
     * @param string $uri    uri string
     * @param mixed  $params optional query string parameters
     * @param mixed  $body   the "post" body
     *
     * @return mixed
     * @throws ClientErrorResponseException
     * @throws ServerErrorResponseException
     * @throws \Exception
     */
    public function get($uri, $params = null, $body = null)
    {
        return $this->performRequest("get", $uri, $params, $body);
    }

    /**
     * perform a put request
     *
     * @param string $uri    uri string
     * @param mixed  $body   the "post" body
     *
     * @return mixed
     * @throws ClientErrorResponseException
     * @throws ServerErrorResponseException
     * @throws \Exception
     */
    public function put($uri, $body = null)
    {
        return $this->performRequest("put", $uri, null, $body);
    }

    /**
     * perform a post request
     *
     * @param string $uri    uri string
     * @param mixed  $body   the "post" body
     *
     * @return mixed
     * @throws ClientErrorResponseException
     * @throws ServerErrorResponseException
     * @throws \Exception
     */
    public function post($uri, $body = null)
    {
        return $this->performRequest("post", $uri, null, $body);
    }

    /**
     * perform a patch
     *
     * @param string $uri    uri string
     * @param mixed  $body   the "post" body
     *
     * @return mixed
     * @throws ClientErrorResponseException
     * @throws ServerErrorResponseException
     * @throws \Exception
     */
    public function patch($uri, $body = null)
    {
        return $this->performRequest("patch", $uri, null, $body);
    }

    /**
     * perform a head request
     *
     * @param string $uri    uri string
     * @param mixed  $body   the "post" body
     *
     * @return mixed
     * @throws ClientErrorResponseException
     * @throws ServerErrorResponseException
     * @throws \Exception
     */
    public function head($uri, $body = null)
    {
        return $this->performRequest("head", $uri, null, $body);
    }

    /**
     * perform a delete request
     *
     * @param string $uri    uri string
     * @param mixed  $body   the "post" body
     *
     * @return mixed
     * @throws ClientErrorResponseException
     * @throws ServerErrorResponseException
     * @throws \Exception
     */
    public function delete($uri, $body = null)
    {
        return $this->performRequest("delete", $uri, null, $body);
    }

    /**
     * precess the request
     *
     * @param string $method the request method
     * @param string $uri    the uri string
     * @param mixed  $params the optional query string parameters
     * @param string $body   the (post) body
     *
     * @return mixed
     * @throws Exceptions\InvalidRequestMethodException
     */
    protected function processRequest($method, $uri, $params = null, $body = null)
    {
        $methodString = strtolower($method) . "Request";

        if (method_exists($this, $methodString)) {
            return $this->$methodString($method, $uri, $params, $body);
        } else {
            throw new InvalidRequestMethodException("invalid request method '{$method}' or not implemented");
        }
    }

    /**
     * get request implementation
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param mixed  $params optional query string parameters
     * @param string $body   body/post parameters
     *
     * @return string
     * @throws \Puzzle\Exceptions\InvalidRequestException
     */
    protected function getRequest($method, $uri, $params, $body)
    {
        // get requests has no specific necessary requirements

        return $this->execute($method, $uri, $params, $body);
    }

    /**
     * delete request implementation
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param mixed  $params optional query string parameters
     * @param string $body   body/post parameters
     *
     * @return string
     * @throws \Puzzle\Exceptions\InvalidRequestException
     */
    protected function deleteRequest($method, $uri, $params, $body)
    {
        return $this->execute($method, $uri, $params, $body);
    }

    /**
     * head request implementation
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param mixed  $params optional query string parameters
     * @param string $body   body/post parameters
     *
     * @return string
     * @throws \Puzzle\Exceptions\InvalidRequestException
     */
    protected function headRequest($method, $uri, $params, $body)
    {
        // head requests has no specific necessary requirements

        return $this->execute($method, $uri, $params, $body);
    }

    /**
     * post request implementation
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param mixed  $params optional query string parameters
     * @param string $body   body/post parameters
     *
     * @return string
     * @throws \Puzzle\Exceptions\InvalidRequestException
     */
    protected function postRequest($method, $uri, $params, $body)
    {
        // post requests has no specific necessary requirements

        return $this->execute($method, $uri, $params, $body);
    }

    /**
     * put request implementation
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param mixed  $params optional query string parameters
     * @param string $body   body/post parameters
     *
     * @return string
     * @throws \Puzzle\Exceptions\InvalidRequestException
     */
    protected function putRequest($method, $uri, $params, $body)
    {
        if (is_null($body)) {
            throw new InvalidRequestException("body is required for '{$method}' requests");
        }

        // put requests requires content-length header
        $this->setHttpHeader('Content-Length: ' . strlen($body));

        return $this->execute($method, $uri, $params, $body);
    }

    /**
     * patch request implementation
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param mixed  $params optional query string parameters
     * @param string $body   body/post parameters
     *
     * @return string
     * @throws \Puzzle\Exceptions\InvalidRequestException
     */
    protected function patchRequest($method, $uri, $params, $body)
    {
        if (is_null($body)) {
            throw new InvalidRequestException("body is required for '{$method}' requests");
        }

        // put requests requires content-length header
        $this->setHttpHeader('Content-Length: ' . strlen($body));

        return $this->execute($method, $uri, $params, $body);
    }

    /**
     * Executes the curl request
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param mixed  $params optional query string parameters
     * @param string $body   body/post parameters
     *
     * @return mixed
     * @throws Exceptions\ClientErrorException
     * @throws Exceptions\ServerErrorException
     * @throws Exceptions\TransportException
     */
    protected function execute($method, $uri, $params, $body)
    {
        $this->setMethod(strtoupper($method));
        $url = $this->buildUrl($uri, $params);
        $this->setUrl($url);
        $this->setBody($body);

        if (!curl_setopt_array($this->getHandle(), $this->getOptions())) {
            throw new TransportException("Error setting cURL request options.");
        }

        if (!is_null($this->getHttpHeaders())) {
            if (!curl_setopt($this->getHandle(), CURLOPT_HTTPHEADER, $this->getHttpHeaders())) {
                throw new TransportException("Error setting cURL Header options.");
            }
        }

        $result = curl_exec($this->getHandle());

        $this->checkForCurlErrors();

        $response = array();
        $response["data"] = $this->getSerializer()->deserialize($result);
        $response["status"] = $this->getStatusCode();


        if ($response['status'] >= 400 && $response['status'] < 500) {

            $statusCode = $response['status'];
            $responseBody = $response["data"];
            $exceptionText = "{$statusCode} Client Exception: {$responseBody}";

            throw new ClientErrorException($exceptionText, $statusCode);

        } else if ($response['status'] >= 500) {
            $statusCode = $response['status'];
            $responseBody = $response["data"];
            $exceptionText = "{$statusCode} Server Exception: {$responseBody}";

            throw new ServerErrorException($exceptionText, $statusCode);
        }


        return $response;
    }

    /**
     * checks if a  curl error occurred
     *
     * @return void
     * @throws ConnectionException
     */
    protected function checkForCurlErrors()
    {
        if (curl_errno($this->getHandle())) {
            $exceptionText = "Connection Error: " . curl_error($this->getHandle());
            throw new ConnectionException($exceptionText);
        }
    }

    /**
     * Enables ssl for the connection
     *
     * @param bool $strict optional value if ssl should be strict (check server certificate)
     *
     * @return void
     */
    public function enableSSL($strict = false)
    {
        $this->setScheme(self::SCHEME_SSL);
        if ($strict === false) {
            $this->setOption(CURLOPT_SSL_VERIFYPEER, 0);
            $this->setOption(CURLOPT_SSL_VERIFYHOST, 0);
        } else {
            $this->setOption(CURLOPT_SSL_VERIFYPEER, 0);
            $this->setOption(CURLOPT_SSL_VERIFYHOST, 1);
        }
    }

    /**
     * Disables ssl for the connection
     *
     * @return void
     */
    public function disableSSL()
    {
        $this->scheme = self::SCHEME_PLAIN;
    }

    /**
     * Sets the http/https scheme string
     *
     * @param string $scheme http/http scheme string
     *
     * @return void
     */
    protected function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Sets a http option (e.g. use strict ssl)
     *
     * @param integer $key   the curl option key
     * @param integer $value the value to set
     *
     * @return void
     */
    protected function setOption($key, $value)
    {
        $this->httpOptions[$key] = $value;
    }

    /**
     * Returns all set http options as array
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->httpOptions;
    }

    /**
     * build complete url
     *
     * @param string $uri    uri string
     * @param array  $params query string parameters
     *
     * @return string
     */
    protected function buildUrl($uri, $params)
    {
        $host = $this->buildHostString();

        if (strpos($uri, "/") !== 0) {
            $uri = "/" . $uri;
        }

        $url = $host . $uri;

        foreach ($params as $key => $value) {
            $url = $url . "?" . $key . "=" . $value;
        }

        return $url;
    }

    /**
     * build host string (scheme - hostname/ip - (optional) port
     *
     * @return string
     *
     * @throws Exceptions\ConfigurationException
     */
    protected function buildHostString()
    {
        $scheme = $this->getScheme();
        $host = $this->getHost();
        $port = $this->getPort();

        if (is_null($host)) {
            throw new ConfigurationException("no host was set");
        }

        $hostString = $this->prepareHost($scheme, $host);

        if (!is_null($port)) {
            $hostString = $hostString . ":" . $port;
        }

        return $hostString;
    }

    /**
     * Adds given scheme to hostname
     *
     * @param string $scheme the http scheme
     * @param string $host   the hostname
     *
     * @return string
     * @throws Exceptions\ConfigurationException
     */
    protected function prepareHost($scheme, $host)
    {
        if (is_null($host)) {
            throw new ConfigurationException("no host was set");
        }

        $host = $this->stripScheme($host);

        if (substr($host, -1) === "/") {
            $host = substr($host, 0, -1);
        }

        return $scheme . $host;
    }

    /**
     * strip http/https scheme from hostname
     *
     * @param string $host the hostname
     *
     * @return string
     */
    protected function stripScheme($host)
    {
        if (strpos($host, self::SCHEME_SSL) === 0) {
            return substr($host, strlen(self::SCHEME_SSL));
        }

        if (strpos($host, self::SCHEME_PLAIN) === 0) {
            return substr($host, strlen(self::SCHEME_PLAIN));
        }
        return $host;
    }

    /**
     * Returns scheme string
     *
     * @return string
     */
    protected function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Returns hostname
     *
     * @return string
     */
    protected function getHost()
    {
        return $this->host;
    }

    /**
     * Returns port
     *
     * @return int
     */
    protected function getPort()
    {
        return $this->port;
    }

    /**
     * Sets given url as curl "url" parameter
     *
     * @param string $url complete url to query
     *
     * @return void
     */
    protected function setUrl($url)
    {
        curl_setopt($this->getHandle(), CURLOPT_URL, $url);
    }

    /**
     * Sets given request method as curl "request" parameter
     *
     * @param string $value the request method
     *
     * @return void
     */
    protected function setMethod($value)
    {
        curl_setopt($this->getHandle(), CURLOPT_CUSTOMREQUEST, $value);
    }

    /**
     * Sets given string as curl post body
     *
     * @param string $body the "post" body
     *
     * @return void
     */
    protected function setBody($body)
    {
        curl_setopt($this->getHandle(), CURLOPT_POSTFIELDS, $body);
    }

    /**
     * Adds a new http header to header list
     *
     * @param string $header http header to set
     *
     * @return void
     */
    public function setHttpHeader($header)
    {
        $headers = $this->getHttpHeaders();
        $headers[] = $header;
        $this->setHttpHeaders($headers);

    }

    /**
     * Returns http headers as array
     *
     * @return array
     */
    protected function getHttpHeaders()
    {
        return $this->httpHeaders;
    }

    /**
     * Saving given array of http headers to attributes
     *
     * @param array $headers http headers as array
     *
     * @return void
     */
    protected function setHttpHeaders(array $headers)
    {
        $this->httpHeaders = $headers;
    }
}
