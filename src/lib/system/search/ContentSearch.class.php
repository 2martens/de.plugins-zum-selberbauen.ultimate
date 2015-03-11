<?php
/**
 * Contains the ContentSearch class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.search
 * @category	Ultimate CMS
 */
namespace ultimate\system\search;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\form\IForm;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\search\AbstractSearchableObjectType;

/**
 * An implementation of ISearchableObjectType for searching in contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.search
 * @category	Ultimate CMS
 */
class ContentSearch extends AbstractSearchableObjectType {
	/**
	 * Array of cached contents.
	 * @var \ultimate\data\content\SearchResultContent[]
	 */
	public $contentCache = array();
	
	/**
	 * Caches the data for the given object ids.
	 * 
	 * @param	integer[]	$objectIDs
	 * @param	mixed[]		$additionalData
	 */
	public function cacheObjects(array $objectIDs, array $additionalData = null) {
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contentsSearchResult');
		foreach ($objectIDs as $contentID) {
			$this->contentCache[$contentID] = $contents[$contentID];
		}
	}
	
	/**
	 * Returns the application abbreviation.
	 * 
	 * @return	string
	 */
	public function getApplication() {
		return 'ultimate';
	}
	
	/**
	 * Returns the object with the given object id.
	 * 
	 * @param	integer	$objectID
	 * 
	 * @return	\ultimate\data\content\Content|null	null if there is no such object
	 */
	public function getObject($objectID) {
		if (isset($this->contentCache[$objectID])) return $this->contentCache[$objectID];
		return null;
	}
	
	/**
	 * Returns the name of the form template for this object type.
	 * 
	 * @return	string
	 */
	public function getFormTemplateName() {
		return '';
	}
	
	/**
	 * Returns the database table name of this message.
	 * 
	 * @return	string
	 */
	public function getTableName() {
		return 'ultimate'.WCF_N.'_content';
	}
	
	/**
	 * Returns the database table name of this message.
	 * 
	 * @return	string
	 */
	public function getIDFieldName() {
		return $this->getTableName().'.contentID';
	}
	
	/**
	 * Returns the database field name of the subject field.
	 * 
	 * @return	string
	 */
	public function getSubjectFieldName() {
		return $this->getTableName().'.contentTitle';
	}
	
	/**
	 * Returns the database field name of the username.
	 * 
	 * @return	string
	 */
	public function getUsernameFieldName() {
		return 'wcf'.WCF_N.'_user.username';
	}
	
	/**
	 * Returns the database field name of the time.
	 * 
	 * @return	string
	 */
	public function getTimeFieldName() {
		return $this->getTableName().'.publishDate';
	}
	
	/**
	 * Returns additional search information.
	 * 
	 * @return	null
	 */
	public function getAdditionalData() {
		return null;
	}
	
	/**
	 * Returns the search conditions of this message type.
	 * 
	 * @param	\wcf\form\IForm	$form
	 * 
	 * @return	\wcf\system\database\util\PreparedStatementConditionBuilder|null
	 */
	public function getConditions(IForm $form = null) {
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add($this->getTableName().'.status = ?', array(3));
		return $conditionBuilder;
	}
	
	/**
	 * Provides the ability to add additional joins to sql search query.
	 * 
	 * @return	string
	 */
	public function getJoins() {
		return 'LEFT JOIN wcf'.WCF_N.'_user ON (wcf'.WCF_N.'_user.userID = '.$this->getTableName().'.authorID)';
	}
	
	/**
	 * Shows the form part of this object type.
	 * 
	 * @param	\wcf\form\IForm	$form
	 */
	public function show(IForm $form = null) {
		
	}
	
	/**
	 * Provides the option to replace the default search index SQL query by an own version.
	 * 
	 * @param	\wcf\system\database\util\PreparedStatementConditionBuilder	$fulltextCondition
	 * @param	\wcf\system\database\util\PreparedStatementConditionBuilder	$searchIndexConditions
	 * @param	\wcf\system\database\util\PreparedStatementConditionBuilder	$additionalConditions
	 * @param	string														$orderBy
	 * 
	 * @return	string
	 */
	public function getSpecialSQLQuery(PreparedStatementConditionBuilder &$fulltextCondition = null, PreparedStatementConditionBuilder &$searchIndexConditions = null, PreparedStatementConditionBuilder &$additionalConditions = null, $orderBy = 'time DESC') {
		return '';
	}
}
