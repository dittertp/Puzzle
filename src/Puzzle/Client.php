<?php

/**
 * Puzzle\Client
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
 * @package   Puzzle
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2015 Philipp Dittert
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/dittertp/Puzzle
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
    protected $httpOptions = array();

    /**
     * @var array
     */
    protected $httpHeaders = array();

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
     * new class instance
     *
     * @param string $host the host
     * @param mixed  $port (optional) the port
     *
     * @throws ConfigurationException
     */
    public function __construct($host, $port)
    {
        $this->setHost($host, $port);
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

        $this->handle = $handle;

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
     * @param string $host the hostname
     * @param mixed  $port the port
     *
     * @return void
     * @throws \Puzzle\Exceptions\ConfigurationException
     */
    protected function setHost($host, $port = null)
    {
        $this->host = $host;
        if ($port !== null) {
            if (is_numeric($port)) {
                $this->port = (int) $port;
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
            throw $exception;

        } catch (ServerErrorResponseException $exception) {
            throw $exception;
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
     * @param mixed  $params optional query string parameters
     * @param mixed  $body   the "post" body
     *
     * @return mixed
     * @throws ClientErrorResponseException
     * @throws ServerErrorResponseException
     * @throws \Exception
     */
    public function put($uri, $params = null, $body = null)
    {
        return $this->performRequest("put", $uri, $params, $body);
    }

    /**
     * perform a post request
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
    public function post($uri, $params = null, $body = null)
    {
        return $this->performRequest("post", $uri, $params, $body);
    }

    /**
     * perform a patch
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
    public function patch($uri, $params = null, $body = null)
    {
        return $this->performRequest("patch", $uri, $params, $body);
    }

    /**
     * perform a head request
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
    public function head($uri, $params = null, $body = null)
    {
        return $this->performRequest("head", $uri, $params, $body);
    }

    /**
     * perform a delete request
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
    public function delete($uri, $params = null, $body = null)
    {
        return $this->performRequest("delete", $uri, $params, $body);
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
        $methodString = $this->getMethod($method);

        if (method_exists($this, $methodString)) {
            return $this->$methodString($method, $uri, $params, $body);
        } else {
            throw new InvalidRequestMethodException("request method '{$method}' not implemented");
        }
    }

    /**
     * returns the request method
     *
     * @param string $method the request method
     *
     * @return string
     */
    protected function getMethod($method)
    {
        $method = strtolower($method);

        if ($method === "patch") {
            return "putRequest";
        }

        return $method . "Request";
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
        $this->checkBody($body, $method);

        // put requests requires content-length header
        $this->setHttpHeader('Content-Length: ' . strlen($body));

        return $this->execute($method, $uri, $params, $body);
    }

    /**
     * checks if body is not null
     *
     * @param mixed  $body   the body or null
     * @param string $method the request method
     *
     * @return void
     * @throws InvalidRequestException
     */
    protected function checkBody($body, $method)
    {
        if (is_null($body)) {
            throw new InvalidRequestException("body is required for '{$method}' requests");
        }
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

        curl_setopt_array($this->getHandle(), $this->getOptions());

        if (!is_null($this->getHttpHeaders())) {
            curl_setopt($this->getHandle(), CURLOPT_HTTPHEADER, $this->getHttpHeaders());
        }

        // wrap curl_exec for easier unit testing
        $result = $this->curlExec();

        $this->checkForCurlErrors();

        $response = array();
        $response["data"] = $this->getSerializer()->deserialize($result);
        $response["status"] = $this->getStatusCode();


        if ($response['status'] >= 400 && $response['status'] < 500) {
            $statusCode = $response['status'];
            $exceptionText = "{$statusCode} Client Exception: {$result}";

            throw new ClientErrorException($exceptionText, $statusCode);

        } else if ($response['status'] >= 500) {
            $statusCode = $response['status'];
            $exceptionText = "{$statusCode} Server Exception: {$result}";

            throw new ServerErrorException($exceptionText, $statusCode);
        }


        return $response;
    }

    /**
     * executes curl_exec
     *
     * @return mixed
     */
    protected function curlExec()
    {
        return curl_exec($this->getHandle());
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
     * @param mixed  $params query string parameters
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


        if ($params === null) {
            $params = array();
        }

        $url .= $this->buildQueryString($params);

        return $url;
    }

    /**
     * build the query string
     *
     * @param array $params query string parameters
     *
     * @return string
     */
    protected function buildQueryString(array $params)
    {
        $qs = "";
        foreach ($params as $key => $value) {
            if ($qs === "") {
                $qs = "?";
            } else {
                $qs .= "&&";
            }
            $qs .= $key . "=" . $value;
        }
        return $qs;
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
        return preg_replace("(^https?://)", "", $host);
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
