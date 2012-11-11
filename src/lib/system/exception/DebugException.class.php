<?php
/**
 * Contains the DebugException class.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * The Ultimate CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.exception
 * @category	Ultimate CMS
 */
namespace ultimate\system\exception;
use wcf\system\exception\LoggedException;
use wcf\util\StringUtil;

/**
 * Creates a simple logged exception.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.exception
 * @category	Ultimate CMS
 */
class DebugException extends LoggedException {
	/**
	 * Contains the description.
	 * @var string
	 */
	protected $description = '';
	
	/**
	 * Contains the log id.
	 * @var integer
	 */
	protected $logID = 0;
	
	/**
	 * Creates a new Debug Exception.
	 * 
	 * @param	string		$message		log message
	 * @param	integer		$code			log code
	 * @param	\Exception	$previous		repacked exception
	 * @param	string		$description	log description
	 */
	public function __construct($message = '', $code = 0, \Exception $previous = null, $description = '') {
		parent::__construct(StringUtil::trim($message), intval($code), $previous);
		$this->description = StringUtil::trim($description);
		$this->logID = $this->logError();
	}
	
	/**
	 * Returns the log description.
	 * 
	 * @return	string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Returns the log id.
	 * 
	 * @return	integer
	 */
	public function getLogID() {
		return $this->logID;
	}
}
