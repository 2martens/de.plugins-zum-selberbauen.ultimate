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
	 * If true we have an ACP request.
	 * @var boolean
	 */
	protected $isACPRequest = false;
	
	/**
	 * If true we have a visual editor grid request.
	 * @var boolean
	 */
	protected $isVisualEditorGrid = false;
	
	/**
	 * Compiles the visualEditor stylesheets.
	 *
	 * @param	\wcf\data\style\Style	$style
	 */
	public function compileVisualEditor(\wcf\data\style\Style $style) {
		$files = array(
			ULTIMATE_DIR.'style/visualEditor/visualEditor.less'
		);
		
		$variables = $style->getVariables();
		$individualCss = '';
		if (isset($variables['individualCss'])) {
			$individualCss = $variables['individualCss'];
			unset($variables['individualCss']);
		}
		
		$this->compileStylesheet(
			ULTIMATE_DIR.'style/visualEditor-'.$style->styleID,
			$files,
			$variables,
			$individualCss,
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
		$this->isVisualEditorGrid = true;
		
		$variables = $style->getVariables();
		$individualCss = '';
		if (isset($variables['individualCss'])) {
			$individualCss = $variables['individualCss'];
			unset($variables['individualCss']);
		}
	
		$this->compileStylesheet(
			ULTIMATE_DIR.'style/visualEditorGrid-'.$style->styleID,
			$files,
			$variables,
			$individualCss,
			new Callback(function($content) use ($style) {
				return "/* stylesheet for '".$style->styleName."', generated on ".gmdate('r')." -- DO NOT EDIT */\n\n" . $content;
			})
		);
	}
	
	/**
	 * Compiles LESS stylesheets for ACP usage.
	 */
	public function compileACP() {
		$files = array(ULTIMATE_DIR.'style/ultimate.less');
		$this->isACPRequest = true;
		
		// read default values
		$sql = 'SELECT   variableName, defaultValue
		        FROM     wcf'.WCF_N.'_style_variable
		        ORDER BY variableID ASC';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$variables = array();
		while ($row = $statement->fetchArray()) {
			$variables[$row['variableName']] = $row['defaultValue'];
		}
		
		$this->compileStylesheet(
			ULTIMATE_DIR.'acp/style/style',
			$files,
			$variables,
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
	 * Compiles the Ultimate CMS style files.
	 * 
	 * @param	\wcf\data\style\Style	$style
	 */
	public function compile(\wcf\data\style\Style $style) {
		$files = array(
			WCF_DIR.'style/ultimate/ultimateCore.less',
			ULTIMATE_DIR.'style/general/ultimate.less'
		);
		
		// get style variables
		$variables = $style->getVariables();
		$individualCss = '';
		if (isset($variables['individualCss'])) {
			$individualCss = $variables['individualCss'];
			unset($variables['individualCss']);
		}
		
		$this->compileStylesheet(
			ULTIMATE_DIR.'style/style-'.$style->styleID,
			$files,
			$variables,
			$individualCss,
			new Callback(function($content) use ($style) {
				return "/* stylesheet for '".$style->styleName."', generated on ".gmdate('r')." -- DO NOT EDIT */\n\n" . $content;
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
		// add reset like a boss
		$content = '';
		if (!$this->isACPRequest && !$this->isVisualEditorGrid) $content .= $this->prepareFile(WCF_DIR.'style/bootstrap/reset.less');
		// until style system works completely, we have to use this
		$content .= $this->prepareFile(WCF_DIR.'style/bootstrap/variables.less');
		// apply style variables
		$this->compiler->setVariables($variables);
	
		// add mixins
		$content .= $this->prepareFile(WCF_DIR.'style/bootstrap/mixins.less');
		$content .= $this->prepareFile(ULTIMATE_DIR.'style/bootstrap/ultimateMixins.less');
	
		return $content;
	}
}
