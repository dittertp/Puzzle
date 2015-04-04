<?php

/**
 * Puzzle\Interfaces\SerializerInterface
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
 * class SerializerInterface
 *
 * @category  Puzzle
 * @package   Puzzle
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2015 Philipp Dittert
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/dittertp/Puzzle
 */

interface SerializerInterface
{
    /**
     * Serialize assoc array into JSON string
     *
     * @param string|array $data Assoc array to encode into JSON
     *
     * @return string
     */
    public function serialize($data);

    /**
     * Deserialize JSON into an assoc array
     *
     * @param string $data JSON encoded string
     *
     * @return mixed
     */
    public function deserialize($data);
}
