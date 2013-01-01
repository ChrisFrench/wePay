<br>
<div class="alert alert-info fade in span12">
	<form action="<?php echo JRoute::_('index.php?option=com_wepay&view=oauth&Itemid=' . Wepay::getInstance() -> get('oauth_itemid', '153'), true, -1); ?>">
	<div class="span12"><h2>
Inorder to create projects, and receive funds you need to register with wepay.com		
		</h2>
	<div class="span5">
		
		<h4>
<h2>What is WePay?</h2>
</h4>
<p>
WePay is a secure payment processor. Take a minute to register and start accepting payments online instantly.
</p>
</div>
	<div class="span5">
		<br>
		<button class="btn btn-primary btn-large" value="submit" type="submit">Connect My iCrowdFund account to wepay.com</button>
	</div>
	</form>
</div>
</div>


<div class="span12 clearfix">
	<h2>How does it connect?</h2>
	When you click the image above you will be directed to wepay.com as you see in the image below,  please fill out wepay  
	information and when your account is created you will be redirected back to iCrowdFund, to create your project. 
	<strong>You only have to follow through this process one time, and it only takes a minute</strong>
<img src="<?php echo DSC::getUrl('images', 'com_wepay');?>wepayOauth.png">
		</div>