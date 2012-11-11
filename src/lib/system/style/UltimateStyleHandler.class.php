<?php
/**
 * Contains the UltimateStyleHandler class.
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
use wcf\system\request\RequestHandler;
use wcf\system\style\StyleHandler;
use wcf\system\WCF;

/**
 * Extends the WCF-StyleHandler to address Ultimate's needs.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.style
 * @category	Ultimate CMS
 */
class UltimateStyleHandler extends StyleHandler {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.style.StyleHandler.html#getStylesheet
	 */
	public function getStylesheet() {
		if (RequestHandler::getInstance()->isACPRequest()) {
			// ACP
			$filename = 'acp/style/style.css';
			if (!file_exists(ULTIMATE_DIR.$filename)) {
				UltimateStyleCompiler::getInstance()->compileACP();
			}
			return '<link rel="stylesheet" type="text/css" href="'.WCF::getPath('ultimate').$filename.'" />';
		}
		else {
			// frontend
			$filename = 'style/style-'.$this->getStyle()->styleID.'.css';
			if (!file_exists(ULTIMATE_DIR.$filename)) {
				UltimateStyleCompiler::getInstance()->compile($this->getStyle()->getDecoratedObject());
			}
			return '<link rel="stylesheet" type="text/css" href="'.WCF::getPath('ultimate').$filename.'" />';
		}
		
	}
	
	/**
	 * Returns the HTML tag to include the visualEditor stylesheet.
	 * 
	 * @return	string
	 */
	public function getVisualEditorStylesheet() {
		$filename = 'style/visualEditor-'.$this->getStyle()->styleID.'.css';
		if (!file_exists(ULTIMATE_DIR.$filename)) {
			UltimateStyleCompiler::getInstance()->compileVisualEditor($this->getStyle()->getDecoratedObject());
		}
		return '<link rel="stylesheet" type="text/css" href="'.WCF::getPath('ultimate').$filename.'" />';
	}
	
	/**
	 * Returns the HTML tag to include the visualEditor stylesheet.
	 *
	 * @return	string
	 */
	public function getVisualEditorGridStylesheet() {
		$filename = 'style/visualEditorGrid-'.$this->getStyle()->styleID.'.css';
		if (!file_exists(ULTIMATE_DIR.$filename)) {
			UltimateStyleCompiler::getInstance()->compileVisualEditorGrid($this->getStyle()->getDecoratedObject());
		}
		return '<link rel="stylesheet" type="text/css" href="'.WCF::getPath('ultimate').$filename.'" />';
	}
}
