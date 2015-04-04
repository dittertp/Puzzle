<?php

namespace Puzzle\Serializer;

class DefaultSerializerTest extends \PHPUnit_Framework_TestCase
{

    public function testSerialize()
    {
        $input = "already a string content";
        $expected = "already a string content";

        $serializer = new DefaultSerializer();
        $result = $serializer->serialize($input);

        $this->assertEquals($expected, $result);


        $input = array("a"=>"b", "c"=>"f");
        $expected = '{"a":"b","c":"f"}';

        $result = $serializer->serialize($input);
        $this->assertEquals($expected, $result);



        $input = array();
        $expected = '{}';

        $result = $serializer->serialize($input);
        $this->assertEquals($expected, $result);
    }

    public function testDeserialize()
    {
        $input = "already a string content";
        $expected = "already a string content";

        $serializer = new DefaultSerializer();
        $result = $serializer->deserialize($input);

        $this->assertEquals($expected, $result);

        $input = '{"a":"b","c":"f"}';
        $expected = array("a"=>"b","c"=>"f");

        $result = $serializer->deserialize($input);

        $input = '';
        $expected = '';

        $result = $serializer->deserialize($input);

        $this->assertEquals($expected, $result);

    }
}
