<?php
/**
* @package		Wepay
* @copyright	Copyright (C) 2012 DT Design Inc. All rights reserved.
* @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.dioscouri.com
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class WepaySelect extends DSCSelect
{
	
	/**
	* Generates Type list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function wepayType( $selected, $name = 'filter_type', $attribs = array('class' => 'inputbox'), $idtag = null, $allowAny = false, $title = 'COM_WEPAY_TYPE' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}
	
		$list[] = JHTML::_('select.option',  'GOODS', JText::_('COM_WEPAY_GOODS') );
		$list[] = JHTML::_('select.option',  'SERVICE', JText::_('COM_WEPAY_SERVICE') );
		$list[] = JHTML::_('select.option',  'PERSONAL', JText::_('COM_WEPAY_PERSONAL') );
		$list[] = JHTML::_('select.option',  'EVENT', JText::_('COM_WEPAY_EVENT') );
		$list[] = JHTML::_('select.option',  'DONATION', JText::_('COM_WEPAY_DONATION') );
		

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}

	/**
	* Generates Type list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function feeOptions( $selected, $name = 'filter_feeoptions', $attribs = array('class' => ''), $idtag = null, $allowAny = false, $title = 'COM_WEPAY_TYPE' )
	{
	    $list = array();
		
		
		$list[] = JHTML::_('select.option',  '0', JText::_('COM_WEPAY_APPFEE_BUILTIN') );
		$list[] = JHTML::_('select.option',  '1', JText::_('COM_WEPAY_APPFEE_USERPAYS') );
		

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}
	
	
	
} 
?>