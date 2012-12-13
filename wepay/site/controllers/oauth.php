<?php
/**
 * @package	Wepay
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class WepayControllerOauth extends WepayController {

	var $error = NULL;
	var $code = NULL;
	var $info = NULL;
	var $user = NULL;
	var $accountCreate = NULL;

	function __construct() {
		parent::__construct();
		$this -> set('suffix', 'oauth');

		$this -> error = JRequest::getVar('error');
		$this -> code = JRequest::getVar('code');
		$redirect_uri = JRoute::_('index.php?option=com_wepay&view=oauth&Itemid=' . Wepay::getInstance() -> get('oauth_itemid', '153'), true, -1);

		$this -> user = JFactory::getUser();

		if (!empty($this -> error)) {
			// user did not grant permissions
		} elseif (empty($this -> code)) {
			// set $scope and $redirect_uri before doing this
			// this will send the user to WePay to authenticate

			$options = array();
			$options['state'] = '';
			$options['user_name,'] = $this -> user -> name;
			$options['user_email'] = $this -> user -> email;

			$uri = WePayLib::getAuthorizationUri(WepayLib::$all_scopes, $redirect_uri, $options);
			$this -> setRedirect($uri);

			//$app = JApplication::getInstance();
			//$app->close();
		} else {
			$this -> info = WePayLib::getToken($this -> code, $redirect_uri);
			if ($this -> info) {
				$wepay = new WePayLib($this -> info -> access_token);

				// create an account for a user
				$this -> accountCreate = $wepay -> request('account/create/', array('name' => $this -> user -> username, 'description' => Wepay::getInstance() -> get('app_description', 'Funding Application')));

			} else {
				// Unable to obtain access token
			}
		}

	}

	function display($cachable = false, $urlparams = false) {
		if ($this -> info -> access_token && $this -> accountCreate -> account_id) {
			// do the user store
			//TODO for some reason DSCTable was failing
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_wepay/tables');
			$table = JTable::getInstance('Accounts', 'WepayTable');
			$table -> load();
			$table -> user_id = $this -> user -> id;
			$table -> wepay_userid = $this -> info -> user_id;
			$table -> wepay_account_id = $this -> accountCreate -> account_id;
			$table -> wepay_account_uri = $this -> accountCreate -> account_uri;
			$table -> wepay_access_token = $this -> info -> access_token;
			$table -> wepay_token_type = $this -> info -> token_type;
			if($this -> info -> expires_in) {
					$table -> wepay_expires_in = $this -> info -> expires_in;
			}
			$table -> oauth_code = $this -> code;
			/*What you would like to name the account. (Note: This appears on people's credit card statements, so we suggest using the name of the person or business accepting payments)*/
			/* I am using the Username, we could probably add a field to user sign up if needed. */
			$table -> wepay_name = $this -> user -> username;
			//$table->wepay_description = '';
			$table -> store();
		}

		parent::display();

	}

}
