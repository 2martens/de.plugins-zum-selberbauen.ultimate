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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.style
 * @category	Ultimate CMS
 */
namespace ultimate\system\style;
use wcf\system\application\ApplicationHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\style\StyleCompiler;
use wcf\system\Callback;
use wcf\system\WCF;

/**
 * Extends the WCF-StyleCompiler to address Ultimate's needs.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.style
 * @category	Ultimate CMS
 */
class UltimateStyleCompiler extends StyleCompiler {
	/**
	 * Compiles the visualEditor stylesheets.
	 *
	 * @param	\wcf\data\style\Style	$style
	 */
	public function compileVisualEditor(\wcf\data\style\Style $style) {
		$files = array(
			ULTIMATE_DIR.'style/visualEditor/visualEditor.less'
		);
		
		// load style variables
		$sql = 'SELECT variableName, variableValue
		        FROM   wcf'.WCF_N.'_style_variable
		        WHERE  styleID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($style->__get('styleID')));
		
		$variables = array();
		$individualCss = $individualLess = '';
		while ($row = $statement->fetchArray()) {
			if ($row['variableName'] == 'individualCss') {
				$individualCss = $row['variableValue'];
			}
			else if ($row['variableName'] == 'individualLess') {
				$individualLess = $row['variableValue'];
			}
			else {
				$variables[$row['variableName']] = $row['variableValue'];
			}
		}
		
		$this->compileStylesheet(
			ULTIMATE_DIR.'style/visualEditor-'.ApplicationHandler::getInstance()->getPrimaryApplication()->packageID.'-'.$style->styleID,
			$files,
			$variables,
			$individualCss,
			$individualLess,
			new Callback(function($content) use ($style) {
				return "/* stylesheet for '".$style->styleName."', generated on ".gmdate('r')." -- DO NOT EDIT */\n\n" . $content;
			})
		);
	}
	
	/**
	 * Compiles the visualEditorGrid stylesheets.
	 *
	 * @param	\wcf\data\style\Style	$style
	 */
	public function compileVisualEditorGrid(\wcf\data\style\Style $style) {
		$files = array(
			ULTIMATE_DIR.'style/visualEditor/grid.less',
			ULTIMATE_DIR.'style/visualEditor/visualEditorIFrame.less',
			ULTIMATE_DIR.'style/visualEditor/visualEditorIFrameGrid.less',
			ULTIMATE_DIR.'style/visualEditor/gridUtil.less'
		);
	
		// load style variables
		$sql = 'SELECT variableName, variableValue
		        FROM   wcf'.WCF_N.'_style_variable
		        WHERE  styleID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($style->__get('styleID')));
	
		$variables = array();
		$individualCss = $individualLess = '';
		while ($row = $statement->fetchArray()) {
			if ($row['variableName'] == 'individualCss') {
				$individualCss = $row['variableValue'];
			}
			else if ($row['variableName'] == 'individualLess') {
				$individualLess = $row['variableValue'];
			}
			else {
				$variables[$row['variableName']] = $row['variableValue'];
			}
		}
	
		$this->compileStylesheet(
			ULTIMATE_DIR.'style/visualEditor-'.ApplicationHandler::getInstance()->getPrimaryApplication()->packageID.'-'.$style->styleID,
			$files,
			$variables,
			$individualCss,
			$individualLess,
			new Callback(function($content) use ($style) {
				return "/* stylesheet for '".$style->styleName."', generated on ".gmdate('r')." -- DO NOT EDIT */\n\n" . $content;
			})
		);
	}
	
	/**
	 * Compiles LESS stylesheets for ACP usage.
	 */
	public function compileACP() {
		$files = glob(WCF_DIR.'style/*.less');
		$files[] = ULTIMATE_DIR.'style/ultimate.less';
	
		$this->compileStylesheet(
			WCF_DIR.'acp/style/style',
			$files,
			array(),
			'',
			'',
			new Callback(function($content) {
				// fix relative paths
				$content = str_replace('../icon/', '../../icon/', $content);
				$content = str_replace('../images/', '../../images/', $content);
	
				return "/* stylesheet for ACP, generated on ".gmdate('r')." -- DO NOT EDIT */\n\n" . $content;
			})
		);
	}
	
	/**
	 * Prepares the style compiler, adding variables to environment and appending
	 * individual LESS declarations to override variables.less's values.
	 *
	 * @param	string[]	$variables
	 * @param	string		$individualLess
	 * @return	string
	 */
	protected function bootstrap(array $variables, $individualLess = '') {
		// add reset like a boss
		$content = $this->prepareFile(WCF_DIR.'style/bootstrap/reset.less');
	
		// override LESS variables
		$variablesContent = $this->prepareFile(WCF_DIR.'style/bootstrap/variables.less');
		if ($individualLess) {
			list($keywords, $values) = explode('=', explode("\n", $individualLess));
			if (count($keywords) != count($values)) {
				throw new SystemException("Could not override LESS variables, invalid input");
			}
				
			foreach ($keywords as $i => $keyword) {
				$variablesContent = preg_replace(
					'~^@'.$keyword.':.*$~imU',
					'@'.$keyword.': '.$values[$i].';',
					$variablesContent
				);
			}
		}
		$content .= $variablesContent;
	
	
		// apply style variables
		$this->compiler->setVariables($variables);
	
		// add mixins
		$content .= $this->prepareFile(WCF_DIR.'style/bootstrap/mixins.less');
		$content .= $this->prepareFile(ULTIMATE_DIR.'style/bootstrap/ultimateMixins.less');
	
		return $content;
	}
}
