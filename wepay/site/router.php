<?php
/**
 * @package Wepay
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

if ( !class_exists('Wepay') ) {
    JLoader::register( "Wepay", JPATH_ADMINISTRATOR.DS."components".DS."com_wepay".DS."defines.php" );
}

Wepay::load( "WepayHelperRoute", 'helpers.route' );

/**
 * Build the route
 * Is just a wrapper for WepayHelperRoute::build()
 * 
 * @param unknown_type $query
 * @return unknown_type
 */
function WepayBuildRoute(&$query)
{
    return WepayHelperRoute::build($query);
}

/**
 * Parse the url segments
 * Is just a wrapper for WepayHelperRoute::parse()
 * 
 * @param unknown_type $segments
 * @return unknown_type
 */
function WepayParseRoute($segments)
{
    return WepayHelperRoute::parse($segments);
}