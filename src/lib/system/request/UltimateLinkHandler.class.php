<?php
/**
 * Contains the UltimateLinkHandler class.
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
 * @subpackage	system.request
 * @category	Ultimate CMS
 */
namespace ultimate\system\request;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\application\ApplicationHandler;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\request\RequestHandler;
use wcf\system\request\RouteHandler;
use wcf\util\StringUtil;

/**
 * Modifies the LinkHandler to fit the Ultimate CMS needs.
 * 
 * @author		Jim Martens
 * @copyright	2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.request
 * @category	Ultimate CMS
 */
class UltimateLinkHandler extends LinkHandler {
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.request.LinkHandler.html#getLink
	 * @internal	Removed area in which landing page is called. Enables possibility to create links without controllers.
	 */
	public function getLink($controller = null, array $parameters = array(), $url = '') {
		$abbreviation = 'wcf';
		$anchor = '';
		$isACP = $originIsACP = RequestHandler::getInstance()->isACPRequest();
		$isRaw = false;
		$appendSession = true;
		
		if (isset($parameters['application'])) {
			$abbreviation = $parameters['application'];
			unset($parameters['application']);
		}
		if (isset($parameters['isRaw'])) {
			$isRaw = $parameters['isRaw'];
			unset($parameters['isRaw']);
		}
		if (isset($parameters['appendSession'])) {
			$appendSession = $parameters['appendSession'];
			unset($parameters['appendSession']);
		}
		if (isset($parameters['isACP'])) {
			$isACP = (bool) $parameters['isACP'];
			unset($parameters['isACP']);
				
			// drop session id if link leads to ACP from frontend or vice versa
			if ($originIsACP != $isACP) {
				$appendSession = false;
			}
		}
		if (isset($parameters['forceFrontend'])) {
			if ($parameters['forceFrontend'] && $isACP) {
				$isACP = false;
				$appendSession = false;
			}
			unset($parameters['forceFrontend']);
		}
		
		// remove anchor before parsing
		if (($pos = strpos($url, '#')) !== false) {
			$anchor = substr($url, $pos);
			$url = substr($url, 0, $pos);
		}
		
		$routeURL = 'index.php/';
		foreach ($parameters as $key => $parameter) {
			$routeURL .= $parameter.'/';
		}
		
		if (!$isRaw && !empty($url)) {
			$routeURL .= (strpos($routeURL, '?') === false) ? '?' : '&';
		}
		
		// encode certain characters
		if (!empty($url)) {
			$url = StringUtil::replace(array('[', ']'), array('%5B', '%5D'), $url);
		}
		
		$url = $routeURL . $url;
		
		// append session id
		if ($appendSession) {
			$url .= (strpos($url, '?') === false) ? SID_ARG_1ST : SID_ARG_2ND_NOT_ENCODED;
		}
		
		// handle applications
		if (!PACKAGE_ID) {
			$url = RouteHandler::getHost() . RouteHandler::getPath(array('acp')) . ($isACP ? 'acp/' : '') . $url;
		}
		else {
			// try to resolve abbreviation
			$application = null;
			if ($abbreviation != 'wcf') {
				$application = ApplicationHandler::getInstance()->getApplication($abbreviation);
			}
				
			// fallback to primary application if abbreviation is 'wcf' or unknown
			if ($application === null) {
				$application = ApplicationHandler::getInstance()->getPrimaryApplication();
			}
				
			$url = $application->getPageURL() . ($isACP ? 'acp/' : '') . $url;
		}
		
		// append previously removed anchor
		$url .= $anchor;
		
		return $url;
	}
}
