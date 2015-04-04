<?php

namespace Puzzle;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('Puzzle\Client');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }


    public function testGet()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";

        $curlDataResponse = '{"a":"b","c":"f"}';
        $statusCode = '200';

        $expected = array("status" => 200, "data" => array("a"=>"b", "c"=>"f"));

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->get("dummy");


        $this->assertEquals($expected, $result);

    }

    public function testPut()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";

        $curlDataResponse = '{"a":"b","c":"f"}';
        $statusCode = '200';

        $expected = array("status" => 200, "data" => array("a"=>"b", "c"=>"f"));

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->put("dummy", null, array("a"=>"b", "c"=>"f"));


        $this->assertEquals($expected, $result);

    }

    public function testPost()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";

        $curlDataResponse = '{"a":"b","c":"f"}';
        $statusCode = '200';

        $expected = array("status" => 200, "data" => array("a"=>"b", "c"=>"f"));

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->post("dummy", null, array("a"=>"b", "c"=>"f"));


        $this->assertEquals($expected, $result);

    }

    public function testPatch()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";

        $curlDataResponse = '{"a":"b","c":"f"}';
        $statusCode = '200';

        $expected = array("status" => 200, "data" => array("a"=>"b", "c"=>"f"));

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->patch("dummy", null, array("a"=>"b", "c"=>"f"));


        $this->assertEquals($expected, $result);

    }

    public function testHead()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";

        $curlDataResponse = '';
        $statusCode = 200;

        $expected = array("status" => 200, "data" => '');

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->head("dummy", null, array("a"=>"b", "c"=>"f"));


        $this->assertEquals($expected, $result);

    }

    public function testDelete()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";

        $curlDataResponse = '';
        $statusCode = 200;

        $expected = array("status" => 200, "data" => '');

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->delete("dummy", null, array("a"=>"b", "c"=>"f"));


        $this->assertEquals($expected, $result);

    }

    public function testStripScheme()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";
        $stripScheme = self::getMethod("stripScheme");

        $client = new Client($host, $port);
        $result = $stripScheme->invokeArgs($client, array($host));

        $this->assertEquals("127.0.0.1/", $result);

        $host = "http://127.0.0.1/";
        $port = "9200";
        $client = new Client($host, $port);
        $result = $stripScheme->invokeArgs($client, array($host));

        $this->assertEquals("127.0.0.1/", $result);


        $host = "htp://127.0.0.1/";
        $port = "9200";
        $client = new Client($host, $port);
        $result = $stripScheme->invokeArgs($client, array($host));

        $this->assertEquals("htp://127.0.0.1/", $result);
    }


    public function testBuildUrl()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";
        $expected = "https://127.0.0.1/:9200/dummy?a=b&&c=d";

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('buildHostString'))
            ->getMock();
        $client->expects($this->any())->method('buildHostString')
            ->will($this->returnValue("https://127.0.0.1/:9200"));

        $buildUrl = self::getMethod("buildUrl");

        $result = $buildUrl->invokeArgs($client, array("/dummy", array("a"=>"b", "c"=>"d")));

        $this->assertEquals($expected, $result);

    }

    public function testBuildQueryString()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";
        $expected = "?a=b&&c=d";

        $client = new Client($host, $port);

        $buildQueryString = self::getMethod("buildQueryString");

        $result = $buildQueryString->invokeArgs($client, array(array("a"=>"b", "c"=>"d")));

        $this->assertEquals($expected, $result);

    }

    public function testSetHttpHeader()
    {
        //$client = new Client();
        $host = "https://127.0.0.1/";
        $port = "9200";
        $header = 'Content-Length: 1337';

        $client = $this->getMockBuilder('Puzzle\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $setHttpHeader = self::getMethod("setHttpHeader");
        $getHttpHeaders = self::getMethod("getHttpHeaders");

        $result = $setHttpHeader->invokeArgs($client, array($header));

        $this->assertEquals(array($header), $getHttpHeaders->invokeArgs($client, array()));

    }

    public function testEnableSSL()
    {
        $host = "https://127.0.0.1/";
        $port = "9200";

        $client = new Client($host, $port);
        $client->enableSSL();

        $getScheme = self::getMethod("getScheme");
        $result = $getScheme->invokeArgs($client, array());

        $this->assertEquals("https://", $result);

        $client = new Client($host, $port);
        $client->enableSSL(true);
        $getScheme = self::getMethod("getScheme");
        $result = $getScheme->invokeArgs($client, array());

        $this->assertEquals("https://", $result);

    }

    public function testDisableSSL()
    {
        $host = "https://127.0.0.1/";
        $port = "9200";

        $client = new Client($host, $port);
        $client->disableSSL();

        $getScheme = self::getMethod("getScheme");
        $result = $getScheme->invokeArgs($client, array());

        $this->assertEquals("http://", $result);

    }

    /**
     * @expectedException \Puzzle\Exceptions\InvalidRequestException
     */
    public function testCheckBodyException()
    {
        $host = "https://127.0.0.1/";
        $port = "9200";

        $client = new Client($host, $port);

        $checkBody = self::getMethod("checkBody");
        $result = $checkBody->invokeArgs($client, array(null, "POST"));

    }

    /**
     * @expectedException \Puzzle\Exceptions\ConfigurationException
     */
    public function testSetHost()
    {
        $host = "https://127.0.0.1/";
        $port = "9aaa";

        $client = new Client($host, $port);
    }

    public function testCheckBody()
    {
        $host = "https://127.0.0.1/";
        $port = "9200";

        $client = new Client($host, $port);

        $checkBody = self::getMethod("checkBody");
        $result = $checkBody->invokeArgs($client, array("example body content", "POST"));

        $this->assertNull($result);
    }

    public function testSetAuthentication()
    {
        $expected = array(
            CURLOPT_USERPWD => "username:password",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false
        );
        $host = "https://127.0.0.1/";
        $port = "9200";

        $client = new Client($host, $port);
        $client->setAuthentication("username", "password");
        $getOptions = self::getMethod("getOptions");
        $result = $getOptions->invokeArgs($client, array());

        $this->assertEquals($expected, $result);

    }

    public function testPerformRequest()
    {
        $host = "https://127.0.0.1/";
        $port = "9200";
        $queryString = array("a"=>"b", "c"=>"f");
        $body = "dummy body response";

        $curlDataResponse = '{"a":"b","c":"f"}';
        $statusCode = '200';

        $expected = array("status" => 200, "data" => array("a"=>"b", "c"=>"f"));

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->performRequest("post", "/dummy", $queryString, $body);


    }

    /**
     * @expectedException \Puzzle\Exceptions\InvalidRequestMethodException
     */
    public function testPerformRequestInvalidMethod()
    {
        $host = "https://127.0.0.1/";
        $port = "9200";
        $queryString = array("a"=>"b", "c"=>"f");
        $body = "dummy body response";

        $curlDataResponse = '{"a":"b","c":"f"}';
        $statusCode = '200';

        $expected = array("status" => 200, "data" => array("a"=>"b", "c"=>"f"));

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->performRequest("invalidMethod", "/dummy", $queryString, $body);
    }

    /**
     * @expectedException \Puzzle\Exceptions\ClientErrorException
     */
    public function testPerformRequestClientError()
    {
        $host = "https://127.0.0.1/";
        $port = "9200";
        $queryString = array("a"=>"b", "c"=>"f");
        $body = array("dummy body response");

        $curlDataResponse = '{"a":"b","c":"f"}';
        $statusCode = '403';

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->performRequest("post", "dummy", null, null);
    }

    /**
     * @expectedException \Puzzle\Exceptions\ServerErrorException
     */
    public function testPerformRequestServerError()
    {
        $host = "https://127.0.0.1/";
        $port = "9200";
        $queryString = array("a"=>"b", "c"=>"f");
        $body = array("dummy body response");

        $curlDataResponse = '{"a":"b","c":"f"}';
        $statusCode = '504';

        $client = $this->getMockBuilder('Puzzle\Client')
            ->setConstructorArgs(array($host, $port))
            ->setMethods(array('curlExec', 'checkForCurlErrors', 'getStatusCode'))
            ->getMock();
        $client->expects($this->any())->method('checkForCurlErrors')
            ->will($this->returnValue(false));
        $client->expects($this->any())->method('curlExec')
            ->will($this->returnValue($curlDataResponse));
        $client->expects($this->any())->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $result = $client->performRequest("post", "/dummy", $queryString, $body);
    }

}