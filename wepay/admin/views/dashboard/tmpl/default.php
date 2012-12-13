<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_wepay/js/'); ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>

<form action="<?php echo JRoute::_( @$form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<table style="width: 100%;">
	<tr>
		<td style="width: 70%; max-width: 70%; vertical-align: top; padding: 0px 5px 0px 5px;">

			<?php
			$modules = JModuleHelper::getModules("wepay_dashboard_main");
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$attribs 	= array();
			$attribs['style'] = 'xhtml';
			foreach ( @$modules as $mod )
			{
				echo $renderer->render($mod, $attribs);
			}
			?>
		</td>
		<td style="vertical-align: top; width: 30%; min-width: 30%; padding: 0px 5px 0px 5px;">

			<?php
			$modules = JModuleHelper::getModules("wepay_dashboard_right");
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$attribs 	= array();
			$attribs['style'] = 'xhtml';
			foreach ( @$modules as $mod )
			{
				$mod_params = new JParameter( $mod->params );
				if ($mod_params->get('hide_title', '1')) { $mod->showtitle = '0'; }
				echo $renderer->render($mod, $attribs);
			}
			?>
		</td>
	</tr>
	
	<tr><td colspan="2">
		<h1>
<a name="wepay-php-sdk" class="anchor" href="#wepay-php-sdk"><span class="mini-icon mini-icon-link"></span></a>WePay PHP SDK</h1>

<p>WePay's API allows you to easily add payments into your application.</p>

<p>For full documentation, see <a href="https://www.wepay.com/developer">WePay's developer documentation</a></p>

<h2>
<a name="usage" class="anchor" href="#usage"><span class="mini-icon mini-icon-link"></span></a>Usage</h2>


<h3>
<a name="configuration" class="anchor" href="#configuration"><span class="mini-icon mini-icon-link"></span></a>Configuration</h3>

<p>For all requests, you must initialize the SDK with your Client ID and Client Secret, into either Staging or Production mode. All API calls made against WePay's staging environment mirror production in functionality, but do not actually move money. This allows you to develop your application and test the checkout experience from the perspective of your users without spending any money on payments.  Our <a href="https://www.wepay.com/developer">full documentation</a> contains additional information on test account numbers you can use in addition to "magic" amounts you can use to trigger payment failures and reversals (helpful for testing IPNs).</p>

<p><strong>Note:</strong> Staging and Production are two completely independent environments and share NO data with each other. This means that in order to use staging, you must register at <a href="https://stage.wepay.com/developer">stage.wepay.com</a> and get a set of API keys for your Staging application, and must do the same on Production when you are ready to go live. API keys and access tokens granted on stage <em>can not</em> be used on Production, and vice-versa.</p>

<pre><code>&lt;?php
require 'library/wepay.php';
WePay::useProduction('YOUR CLIENT ID', 'YOUR CLIENT SECRET'); // To initialize staging, use WePay::useStaging('ID','SECRET'); instead.
</code></pre>

<h3>
<a name="authentication" class="anchor" href="#authentication"><span class="mini-icon mini-icon-link"></span></a>Authentication</h3>

<p>To obtain an access token for your user, you must redirect the user to WePay for authentication. WePay uses OAuth2 for authorization, which is detailed <a href="https://www.wepay.com/developer/reference/oauth2">in our documentation</a>. To generate the URI to which you must redirect your user, the SDK contains <code>WePay::getAuthorizationUri($scope, $redirect_uri)</code>. <code>$scope</code> should be an array of scope strings detailed in the documentation. To request full access (most useful for testing, since users may be weary of granting permission to your application if it wants to do too much), you pay pass in <code>WePay::$all_scopes</code>. <code>$redirect_uri</code> must be a fully qualified URI where we will send the user after permission is granted (or not granted), and the domain must match your application settings.</p>

<p>If the user grants permission, he or she will be redirected to your <code>$redirect_uri</code> with <code>code=XXXX</code> appended to the query string. If permission is not granted, we will instead put <code>error=XXXX</code> in the query string. If <code>code</code> is present, the following will exchange it for an access token. Note that codes are only valid for several minutes, so you should do this immediately after the user is redirected back to your website or application.</p>

<pre><code>&lt;?php
if (!empty($_GET['error'])) {
    // user did not grant permissions
}
elseif (empty($_GET['code'])) {
    // set $scope and $redirect_uri before doing this
    // this will send the user to WePay to authenticate
    $uri = WePay::getAuthorizationUri($scope, $redirect_uri);
    header("Location: $uri");
    exit;
}
else {
    $info = WePay::getToken($_GET['code'], $redirect_uri);
    if ($info) {
        // YOUR ACCESS TOKEN IS HERE
        $access_token = $info-&gt;access_token;
    }
    else {
        // Unable to obtain access token
    }
}
</code></pre>

<p>Full details on the access token response are <a href="https://www.wepay.com/developer/reference/oauth2#token">here</a>.</p>

<p><strong>Note:</strong> If you only need access for yourself (e.g., for a personal storefront), the application settings page automatically creates an access token for you. Simply copy and paste it into your code rather than manually going through the authentication flow.</p>

<h3>
<a name="making-api-calls" class="anchor" href="#making-api-calls"><span class="mini-icon mini-icon-link"></span></a>Making API Calls</h3>

<p>With the <code>$access_token</code> from above, get a new SDK object:</p>

<pre><code>&lt;?php
$wepay = new WePay($access_token);
</code></pre>

<p>Then you can make a simple API call. This will list the user's accounts available to your application:</p>

<pre><code>// (continued from above)
try {
    $accounts = $wepay-&gt;request('account/find');
    foreach ($accounts as $account) {
        echo "&lt;a href=\"$account-&gt;account_uri\"&gt;$account-&gt;name&lt;/a&gt;: $account-&gt;description &lt;br /&gt;";
    }
}
catch (WePayException $e) {
    // Something went wrong - normally you would log
    // this and give your user a more informative message
    echo $e-&gt;getMessage();
}
</code></pre>

<p>And that's it!  For more detail on what API calls are available, their parameters and responses, and what permissions they require, please see <a href="https://www.wepay.com/developer/reference">our documentation</a>. For some more detailed examples, look in the <code>demoapp</code> directory and check the README. Dropping the entire directory in a web-accessible location and adding your API keys should allow you to be up and running in just a few seconds.</p>

<h3>
<a name="ssl-certificate" class="anchor" href="#ssl-certificate"><span class="mini-icon mini-icon-link"></span></a>SSL Certificate</h3>

<p>If making an API call causes the following problem:</p>

<pre><code>Uncaught exception 'Exception' with message 'cURL error while making API call to WePay: SSL certificate problem, verify that the CA cert is OK. Details: error:14090086:SSL routines:SSL3_GET_SERVER_CERTIFICATE:certificate verify failed'
</code></pre>

<p>You can read the solution here: <a href="https://support.wepay.com/entries/21095813-problem-with-ssl-certificate-verification">https://support.wepay.com/entries/21095813-problem-with-ssl-certificate-verification</a></p>
		
	</td></tr>
	</table>
	
	

	<?php echo $this->form['validate']; ?>
</form>