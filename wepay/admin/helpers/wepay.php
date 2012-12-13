<?php
/**
 * @version 1.5
 * @package Wepay
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');


if (!class_exists('Wepay')) {
	JLoader::register("Wepay", JPATH_ADMINISTRATOR . "/components/com_wepay/defines.php");
}



class WepayHelperWepay extends JObject {
	
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 * @since	1.6
	 */
	public static function addSubmenu($vName = 'wepay')
	{
		JHtmlSidebar::addEntry(
			JText::_('Dashboard'),
			'index.php?option=com_wepay&view=dashboard',
			$vName == 'favorites'
		);
		JHtmlSidebar::addEntry(
			JText::_('Accounts'),
			'index.php?option=com_wepay&view=accounts',
			$vName == 'favorites'
		);
		JHtmlSidebar::addEntry(
			JText::_('Config'),
			'index.php?option=com_wepay&view=config',
			$vName == 'favorites'
		);
		
	}
	
	
	
	
}

?>