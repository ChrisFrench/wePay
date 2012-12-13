<?php 

$redirect_uri = '';

$options = array();
$options['state'] = '';
$options['username'] = '';
$options['email'] = '';
?>

<div>
	
	<a class="btn " href="<?php echo WepayLib::getAuthorizationUri(WepayLib::$all_scopes,$redirect_uri  ); ?>"> AUTHENTICATE WITH WEPAY</a>
</div>
