<?php

/**
 * Puzzle\Serializer\DefaultSerializer
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

namespace Puzzle\Serializer;

use Puzzle\Interfaces\SerializerInterface;

/**
 * class DefaultSerializer
 *
 * @category  Puzzle
 * @package   TechDivision_Puzzle
 * @author    Philipp Dittert <pd@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

class DefaultSerializer implements SerializerInterface
{
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

    /**
     * Deserialize JSON into an assoc array
     *
     * @param string $data JSON encoded string
     *
     * @return array
     */
    public function deserialize($data)
    {
        return json_decode($data, true);

    }
}
