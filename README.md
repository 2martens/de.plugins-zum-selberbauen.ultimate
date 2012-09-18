Ultimate CMS
===============================

A WCF compatible CMS.


Version notes
-------------

The currently available source code represents an early alpha version of Ultimate CMS and should not be used in productive environments.

Contribution
------------

Developers are always welcome to fork Ultimate CMS and provide features or bug fixes using pull requests. If you make changes or add classes it is mandatory to follow the requirements below:

* Testing is key, you MUST try out your changes before submitting pull requests
* You MUST save your files with Unix-style line endings (\n)
* You MUST NOT include the closing tag of a PHP block at the end of file, provide an empty newline instead
* You MUST use tabs for leading indentation and spaces for styling
    * Tab size of 4 is preferred
    * Empty lines MUST be indented equal to previous line
* All comments within source code MUST be written in English language

Here is an example for a A.class.php file. It contains many of the important things (indentation, documentation, namespace usage).
```php
/**
 * Contains the A class.
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
 * @subpackage	system.test
 * @category	Ultimate CMS
 */
namespace ultimate\system\test;

/**
 * This class presents some test functionality.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.test
 * @category	Ultimate CMS
 */
class A {
	public $test = null;
	protected $test2 = 'test2';
	private $test3 = array('test3');
	
	/**
	 * Returns all users to the given group id.
	 * 
	 * @param	integer	$groupID
	 * @return	\wcf\data\user\User[]
	 */
	public function getUser($groupID) {
		$sql = 'SELECT    user.*
		        FROM      wcf'.WCF_N.'_user user
		        LEFT JOIN wcf'.WCF_N.'_user_to_group userToGroup
		        ON        (userToGroup.userID = user.userID)
		        WHERE     userToGroup.groupID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$groupID
		));
		$user = array();
		while ($user = $statement->fetchObject('\wcf\data\user\User')) {
			$user[$user->__get('userID')] = $user;
		}
		return $user;
	}
}
```

Follow the above conventions if you want your pull requests accepted.

License
-------

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public License
as published by the Free Software Foundation; either version 2.1
of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA