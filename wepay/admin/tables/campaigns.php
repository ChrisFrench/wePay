<?php
/**
 * @version	1.5
 * @package	Wepay
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );



class WepayTableCampaigns extends DSCTable 
{
	/**
	 * Constructs the object
	 * @param $db
	 * @return unknown_type
	 */
	function WepayTableCampaigns ( &$db ) 
	{
		$tbl_key 	= 'campaign_id';
		$tbl_suffix = 'campaigns';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'wepay';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	/**
	 * Checks the integrity of the object before a save 
	 * @return unknown_type
	 */
	function check()
	{		
		$db			= $this->getDBO();
		$nullDate	= $db->getNullDate();
		if (empty($this->created_date) || $this->created_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_date = $date->toMysql();
		}
		if (empty($this->modified_date) || $this->modified_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->modified_date = $date->toMysql();
		}
		
		if(empty($this->content_cat_id)) {
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_categories/tables' );
		
		$table = JTable::getInstance('Category','CategoriesTable');
		$data = array();
		$data['id'] = 0;
		$data['parent_id'] = '15';
		$data['title'] = $this->campaign_name;
		$data['extension'] = 'com_content';
		$data['published'] = '1';
		$data['level'] = '2';
		$data['access'] = '1';
		$data['metadesc'] = '';
		$data['metakey'] = '';
		$data['created_user_id'] = $this->user_id;
		$data['language'] = '*';
		
		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}
		$table->parent_id = 15;
		$table->level = 2;
		// Bind th

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

	
		/*if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}*/

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}

		// Rebuild the path for the category:
		if (!$table->rebuildPath($table->id))
		{
			$this->setError($table->getError());
			return false;
		}
		$table->parent_id = 15;
		$table->level = 2;
		$table->store();
		
		$this->content_cat_id = $table->id;
		}
		
		$this->filterHTML( 'campaign_name' );
		if (empty($this->campaign_name))
		{
			$this->setError( JText::_('COM_TIENDA_NAME_REQUIRED') );
			return false;
		}
        jimport( 'joomla.filter.output' );
        if (empty($this->campaign_alias)) 
        {
            $this->campaign_alias = $this->campaign_name;
        }
        $this->campaign_alias = JFilterOutput::stringURLSafe($this->campaign_alias);
		
		return true;
	}
	
	function createContentCategory() {
		
	}
	
	/**
	 * Stores the object
	 * @param object
	 * @return boolean
	 */
	function store($updateNulls=false) 
	{
		$date = JFactory::getDate();
		$this->modified_date = $date->toMysql();
		$store = parent::store($updateNulls);		
		return $store;		
	}
	
	
}
