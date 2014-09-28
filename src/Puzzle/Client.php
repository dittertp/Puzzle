<?php

namespace Puzzle;

use Puzzle\Interfaces\ClientInterface;
use Puzzle\Exceptions\ConfigurationException;
use Puzzle\Exceptions\ClientErrorResponseException;
use Puzzle\Exceptions\ClientException;
use Puzzle\Exceptions\ServerErrorResponseException;
use Puzzle\Exceptions\ServerErrorException;
use Puzzle\Exceptions\InvalidRequestMethodException;
use Puzzle\Exceptions\InvalidRequestException;

//class Client implements ClientInterface
class Client
{

    const SCHEME_SSL = "https://";

    const SCHEME_PLAIN = "http://";

	protected $handle;

	protected $httpOptions;

    protected $httpHeaders;

	protected $host;

    protected $port;

    protected $scheme;

	public function __construct()
    {
        $this->init();
	}

    public function reset()
    {
        $this->init();
    }

    protected function init()
    {
        $handle = curl_init();
        if ($handle === false) {
            throw new ServerErrorException("curl extension not loaded");
        }
        $this->handle = $handle;

        $this->port = null;
        $this->host = null;

        $this->scheme = self::SCHEME_PLAIN;
        $this->httpOptions = array();
        $this->httpOptions[CURLOPT_RETURNTRANSFER] = true;
        $this->httpOptions[CURLOPT_FOLLOWLOCATION] = false;
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
     *
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
        if ($port != null) {
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

    /*
    public function get($url, $http_options = array())
    {
		$http_options = array_merge($this->http_options, $http_options);
		$this->handle = curl_init($url);

		if(! curl_setopt_array($this->handle, $http_options)){
			throw new RestClientException("Error setting cURL request options");
		}

		$this->response_object = curl_exec($this->handle);
		$this->httpParseMessage($this->response_object);

		curl_close($this->handle);
		return $this->response_object;
	}

    public function post($url, $fields = array(), $http_options = array())
    {
		$http_options = array_merge($this->http_options, $http_options);
		$http_options[CURLOPT_POST] = true;
		$http_options[CURLOPT_POSTFIELDS] = $fields;
		if(is_array($fields)){
			$http_options[CURLOPT_HTTPHEADER] =
				array('Content-Type: multipart/form-data');
		}
		$this->handle = curl_init($url);

		if(! curl_setopt_array($this->handle, $http_options)){
			throw new RestClientException("Error setting cURL request options.");
		}

		$this->response_object = curl_exec($this->handle);
		$this->http_parse_message($this->response_object);

		curl_close($this->handle);
		return $this->response_object;
	}

    public function put($url, $data = '', $http_options = array())
    {
		$http_options = array_merge($this->http_options, $http_options);
		$http_options[CURLOPT_CUSTOMREQUEST] = 'PUT';
		$http_options[CURLOPT_POSTFIELDS] = $data;
		$this->handle = curl_init($url);

		if(! curl_setopt_array($this->handle, $http_options)){
			throw new RestClientException("Error setting cURL request options.");
		}

		$this->response_object = curl_exec($this->handle);
		$this->http_parse_message($this->response_object);

		curl_close($this->handle);
		return $this->response_object;
	}

    public function delete($url, $http_options = array())
    {
		$http_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
		$this->handle = curl_init($url);

		if(! curl_setopt_array($this->handle, $this->getOptions())){
			throw new RestClientException("Error setting cURL request options.");
		}

		$response = curl_exec($this->handle);
		$this->http_parse_message($this->response_object);

		curl_close($this->handle);
		return $this->response_object;
	}


    protected function httpParseMessage($res)
    {

		if(! $res){
			throw new ServerErrorException(curl_error($this->getHandle()), -1);
		}

		$responseInfo = curl_getinfo($this->getHandle());
		$code = $responseInfo['http_code'];

		if($code == 404) {
			throw new ServerErrorException(curl_error($this->getHandle()));
		}

		if($code >= 400 && $code <=600) {
			throw new ServerErrorException('Server response status was: ' . $code .
				' with response: [' . $res . ']', $code);
		}

		if(!in_array($code, range(200,207))) {
			throw new ServerErrorException('Server response status was: ' . $code .
				' with response: [' . $res . ']', $code);
		}
	}
*/

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
     * @throws \Exception
     * @throws \Puzzle\Exceptions\ServerErrorResponseException
     * @throws \Exception
     * @throws \Puzzle\Exceptions\ClientErrorResponseException
     */
    public function performRequest($method, $uri, $params = null, $body = null)
    {
        try {

            // serialize(json) body if it's not already a string
            $body = $this->serialize($body);

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
     * Serialize assoc array into JSON string
     *
     * @param string|array $data Assoc array to encode into JSON
     *
     * @return string
     */
    public function serialize($data)
    {
        if (is_string($data) === true) {
            return $data;
        } else {
            $data = json_encode($data);
            if ($data === '[]') {
                return '{}';
            } else {
                return $data;
            }
        }
    }

    protected function processRequest($method, $uri, $params = null, $body = null)
    {
        $methodString = strtolower($method) . "Request";

        if (method_exists($this, $methodString)) {
            return $this->$methodString($method, $uri, $params, $body);
        } else {
            throw new InvalidRequestMethodException("invalid request method or not implemented");
        }
    }

    /**
     * get request implementation
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param string $params optional query string parameters
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
     * post request implementation
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param string $params optional query string parameters
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
     * @param string $params optional query string parameters
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
     * Executes the curl request
     *
     * @param string $method the request method
     * @param string $uri    the uri
     * @param string $params optional query string parameters
     * @param string $body   body/post parameters
     *
     * @return mixed
     * @throws \Puzzle\Exceptions\ClientException
     */
    protected function execute($method, $uri, $params, $body)
    {
        $this->setMethod(strtoupper($method));
        $url = $this->buildUrl($uri, $params);
        $this->setUrl($url);
        $this->setBody($body);

        if (!curl_setopt_array($this->getHandle(), $this->getOptions())) {
            throw new ClientException("Error setting cURL request options.");
        }

        if (!curl_setopt($this->getHandle(), CURLOPT_HTTPHEADER, $this->getHttpHeaders())) {
            throw new ClientException("Error setting cURL Header options.");
        }


        $response["data"] = curl_exec($this->getHandle());
        $response["statusCode"] = $this->getStatusCode();

        return $response;
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
     * @param int    $key   the curl option key
     * @param mixed  $value the value to set
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

    protected function getScheme()
    {
        return $this->scheme;
    }

    protected function getHost()
    {
        return $this->host;
    }

    protected function getPort()
    {
        return $this->port;
    }

    protected function setUrl($url)
    {
        curl_setopt($this->getHandle(), CURLOPT_URL, $url);
    }

    protected function setMethod($value)
    {
        curl_setopt($this->getHandle(), CURLOPT_CUSTOMREQUEST, $value);
    }

    protected function setBody($body)
    {
        curl_setopt($this->getHandle(), CURLOPT_POSTFIELDS, $body);
    }

    protected function setHttpHeader($header)
    {
        $headers = $this->getHttpHeaders();
        $headers[] = $header;
        $this->setHttpHeaders($headers);

    }

    protected function getHttpHeaders()
    {
        return $this->httpHeaders;
    }

    protected function setHttpHeaders($headers)
    {
        $this->httpHeaders = $headers;
    }
}
