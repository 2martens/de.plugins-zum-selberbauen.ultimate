<?php
/**
 * Contains the UltimateStyleCompiler class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.style
 * @category	Ultimate CMS
 */
namespace ultimate\system\style;
use ultimate\system\ULTIMATECore;
use wcf\system\application\ApplicationHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\style\StyleCompiler;
use wcf\system\Callback;
use wcf\system\WCF;

/**
 * Extends the WCF StyleCompiler to allow non-WCF stylesheets for ACP.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.style
 * @category	Ultimate CMS
 */
class UltimateStyleCompiler extends StyleCompiler {
	/**
	 * Compiles LESS stylesheets for ACP usage.
	 */
	public function compileACP() {
		// read stylesheets by dependency order
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("file_log.filename REGEXP ?", array('style/([a-zA-Z0-9\_\-\.]+)\.less'));
		$conditions->add("file_log.application NOT IN (?)", array(array('wcf', 'wbb')));
		
		$sql = "SELECT      file_log.filename, package.packageDir
		        FROM        wcf".WCF_N."_package_installation_file_log file_log
		        LEFT JOIN   wcf".WCF_N."_package package
		        ON         (file_log.packageID = package.packageID)
		        ".$conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		$files = array();
		while ($row = $statement->fetchArray()) {
			$files[] = WCF_DIR.$row['packageDir'].$row['filename'];
		}
	
		// read default values
		$sql = "SELECT   variableName, defaultValue
		        FROM     wcf".WCF_N."_style_variable
		        ORDER BY variableID ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$variables = array();
		while ($row = $statement->fetchArray()) {
			$value = $row['defaultValue'];
			if (empty($value)) {
				$value = '~""';
			}
				
			$variables[$row['variableName']] = $value;
		}
	
		$this->compileStylesheet(
			WCF_DIR.'acp/style/style-noWCF',
			$files,
			$variables,
			'',
			new Callback(function($content) {
				// fix relative paths
				$content = str_replace('../font/', '../../font/', $content);
				$content = str_replace('../icon/', '../../icon/', $content);
				$content = str_replace('../images/', '../../images/', $content);
	
				return "/* stylesheet for ACP, generated on ".gmdate('r')." -- DO NOT EDIT */\n\n" . $content;
			})
		);
	}
	
	/**
	 * Prepares the style compiler by adding variables to environment.
	 *
	 * @param	string[]	$variables
	 * @return	string
	 */
	protected function bootstrap(array $variables) {
		// apply style variables
		$this->compiler->setVariables($variables);
	
		return '';
	}
}
