<?php
/**
 * Contains the abstract Ultimate CMS test case.
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
 */
namespace ultimate\tests;
use wcf\system\database\statement\PreparedStatement;
use wcf\system\WCF;
use wcf\util\StringUtil;

use ultimate\system\database\DebugMySQLDatabase;

// imports
require_once(__DIR__.'/config.inc.php');
require_once('PHPUnit/Extensions/Database/TestCase.php');

/**
 * Abstract test class for the Ultimate CMS.
 * 
 * @author		Jim Martens
 * @copyright	2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 */
abstract class AbstractUltimateTestCase extends \PHPUnit_Extensions_Database_TestCase {
	/**
	 * Contains the PDO object of the WCF connection.
	 * @var PDO
	 */
	protected static $pdoObject = null;
	
	/**
	 * Contains the database object.
	 * @var \ultimate\system\database\DebugMySQLDatabase
	 */
	protected $dbObject = null;
	
	/**
	 * Contains the connection.
	 * @var \PHPUnit_Extensions_Database_DB_IDatabaseConnection 
	 */
	protected $connection = null;
	
	/**
	 * Contains the dataSet.
	 * @var \PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	protected $dataSet = null;
	
	/**
	 * @see \PHPUnit_Extensions_Database_TestCase::getConnection()
	 */
	public function getConnection() {
		// get configuration
		$dbHost = $dbUser = $dbPassword = $dbName = '';
		$dbPort = 0;
		$dbClass = 'ultimate\system\database\DebugMySQLDatabase';
		require(WCF_DIR.'config.inc.php');
		
		if ($this->connection === null) {
			if (self::$pdoObject === null) {
				$this->dbObject = new DebugMySQLDatabase($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
				self::$pdoObject = $this->dbObject->getPDO();
			}
			$this->connection = $this->createDefaultDBConnection(self::$pdoObject, $dbName);
		}
		return $this->connection;
	}
	
	/**
	 * @see \PHPUnit_Extensions_Database_TestCase::getDataSet()
	 */
	public function getDataSet() {
		if ($this->dataSet === null) {
			$this->dataSet = $this->createXMLDataSet(__DIR__.'/databaseFixture.xml');
		}
		return $this->dataSet;
	}
	
	/**
	 * @see \PHPUnit_Extensions_Database_TestCase::setUp()
	 */
	public function setUp() {
		$this->databaseTester = NULL;
		
		require(__DIR__.'/databaseTablesConfig.inc.php');
		
		// reset auto increment counter for all used tables
		$sql = 'ALTER TABLE table auto_increment = 1';
		foreach ($testedTables as $table) {
			$tmpSql = StringUtil::replace('table', $table, $sql);
			$statement = WCF::getDB()->prepareStatement($tmpSql);
			$statement->execute();
		}
		
		$operation = new \PHPUnit_Extensions_Database_Operation_Composite(array(
            \PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(),
            \PHPUnit_Extensions_Database_Operation_Factory::INSERT()
        ));
		$this->getDatabaseTester()->setSetUpOperation($operation);
		
		$this->getDatabaseTester()->setDataSet($this->getDataSet());
		$this->getDatabaseTester()->onSetUp();
	}
}
