<?php

namespace Puzzle\Client;

use Puzzle\Interfaces\ClientInterface;
use Puzzle\Exceptions\ConfigurationException;
use Puzzle\Exceptions\ClientErrorResponseException;
use Puzzle\Exceptions\ServerErrorResponseException;
use Puzzle\Exceptions\ServerErrorException;
use Puzzle\Exceptions\InvalidRequestMethodException;
use Puzzle\Exceptions\InvalidRequestException;

class Client implements ClientInterface
{

	protected $handle;

	protected $httpOptions;

    protected $httpHeaders;

	protected $host;

    protected $port;

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
        $this->httpOptions[CURLOPT_USERPWD] = $username . ": " . $password;
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
		$http_options = array_merge($this->http_options, $http_options);
		$http_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
		$this->handle = curl_init($url);

		if(! curl_setopt_array($this->handle, $http_options)){
			throw new RestClientException("Error setting cURL request options.");
		}

		$this->response_object = curl_exec($this->handle);
		$this->http_parse_message($this->response_object);

		curl_close($this->handle);
		return $this->response_object;
	}

    private function httpParseMessage($res)
    {

		if(! $res){
			throw new HttpServerException(curl_error($this->handle), -1);
		}

		$this->response_info = curl_getinfo($this->handle);
		$code = $this->response_info['http_code'];

		if($code == 404) {
			throw new HttpServerException404(curl_error($this->handle));
		}

		if($code >= 400 && $code <=600) {
			throw new HttpServerException('Server response status was: ' . $code .
				' with response: [' . $res . ']', $code);
		}

		if(!in_array($code, range(200,207))) {
			throw new HttpServerException('Server response status was: ' . $code .
				' with response: [' . $res . ']', $code);
		}
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

    public function performRequest($method, $uri, $params = null, $body = null)
    {
        try {

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

    protected function processRequest($method, $method, $uri, $params = null, $body = null)
    {
        $methodString = strtolower($method) . "Request";

        if (method_exists($this, $methodString)) {
            return $this->$methodString($uri, $params, $body);
        } else {
            throw new InvalidRequestMethodException("invalid request method or not implemented");
        }
    }

    protected function getRequest($method, $uri, $params, $body)
    {

    }

    protected function postRequest($method, $uri, $params, $body)
    {

    }

    protected function putRequest($method, $uri, $params, $body)
    {
        if (is_null($body)) {
            throw new InvalidRequestException("body is required for 'put' requests");
        }

        // put requests requires content-length header
        $this->setHttpHeader('Content-Length: ' . strlen($body));
        $this->setMethod("put");
        $url = $this->buildUrl($uri, $params);
        $this->setUrl($url);
        $this->setBody($body);






    }

    protected function buildUrl($uri, $params)
    {
        $url="";
        if (($host = $this->getHost()) !== false) {
            $url = $host;
        }

        if (($port = $this->getPort()) !== false) {
            $url = $url . ":" . $port;
        }

        $url = $url . $uri;

        foreach ($params as $key => $value) {
            $url = $url . "?" . $key . "=" . $value;
        }



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