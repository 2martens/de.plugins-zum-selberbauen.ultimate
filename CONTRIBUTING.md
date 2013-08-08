Contribution
============

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
	/**
	 * Contains an object of Test.
	 * @var	\ultimate\system\test\Test
	 */
	public $test = null;
	
	/**
	 * Contains the name of a test.
	 * @var	string
	 */
	protected $test2 = 'test2';
	
	/**
	 * Contains an array of options.
	 * @var	string[]
	 */
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



CONTRIBUTING
============

First of all: Thanks for your interest in contributing to the Ultimate CMS! However, you have to meet some requirements in order to get your changes accepted.

General requirements
--------------------
- Testing is the key, you MUST try out your changes before submitting pull requests. It saves me and yourself a lot of time.
- The code SHOULD be written by yourself, otherwise you have to check the license beforehand with regard to compatibility and give the proper credit to the original author.

Files
-----
- Unix newlines (\n) MUST be used in every file (php, tpl, less, js, etc.)
- All files MUST be saved in UTF-8 encoding

Formatting
----------
- Tabs MUST be used for indentation, you HAVE TO use a tab size of 4
    - empty lines MUST be indentated as deep as the previous line
    - multi-line database strings MUST contain tabs until the level of indentation of the previous line is achieved, 
    from there on only spaces are to be used
    - because it is very important: Within database strings are no tabs allowed other than those previously mentioned.
- All identifiers and comments MUST be written in English
- PHP
    - The closing PHP tag MUST be omitted
    - Every file MUST end with a newline character (\n)

Additionally: Have a look at existing files to find out what they should look like.