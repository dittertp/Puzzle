<?php

/**
 * Puzzle\Exceptions\ConfigurationException
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

namespace Puzzle\Exceptions;

/**
 * class ConfigurationException
 *
 * @category  Puzzle
 * @package   TechDivision_Puzzle
 * @author    Philipp Dittert <pd@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

class ConfigurationException extends \Exception
{
    /**
     * creates class instance
     *
     * @param string $message the message
     */
    public function __construct($message = 'Not Found')
    {
        parent::__construct($message, 404);
    }
}
