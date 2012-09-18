<?php
namespace ultimate\system\menu\custom;
use wcf\system\menu\page\DefaultPageMenuItemProvider;

/**
 * Provides menu items for a custom menu.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.menu.custom
 * @category	Ultimate CMS
 */
class DefaultCustomMenuItemProvider extends DefaultPageMenuItemProvider {
	/**
	 * @see	wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = '\wcf\data\menu\item\MenuItem';
}
