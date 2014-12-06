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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.style
 * @category	Ultimate CMS
 */
namespace ultimate\system\style;
use wcf\system\style\StyleHandler;
use wcf\system\WCF;

/**
 * Extends the WCF StyleHandler to allow non-WCF stylesheets for ACP.
 * 
 * Use it only for the ACP.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.style
 * @category	Ultimate CMS
 */
class UltimateStyleHandler extends StyleHandler {
	/**
	 * Returns the HTML tag to include current stylesheet (only for ACP).
	 *
	 * @param   boolean $isACP
	 * @return	string empty string if you call it outside ACP
	 */
	public function getStylesheet($isACP = false) {
		if ($isACP) {
			// ACP
			$filename = 'acp/style/style-noWCF'.(WCF::getLanguage()->get('wcf.global.pageDirection') == 'rtl' ? '-rtl' : '').'.css';
			if (!file_exists(WCF_DIR.$filename)) {
				UltimateStyleCompiler::getInstance()->compileACP();
			}
			return '<link rel="stylesheet" type="text/css" href="'.WCF::getPath().$filename.'?m='.filemtime(WCF_DIR.$filename).'" />';
		}
	
		return '';
	}
}
