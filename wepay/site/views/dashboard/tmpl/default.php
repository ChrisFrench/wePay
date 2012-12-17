<?php

// order info
        DSCTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_tienda/tables' );
        $order = DSCTable::getInstance('Orders', 'TiendaTable');
        $order->load('84');
		$orderitems = $order->getItems();
var_dump($orderitems);
foreach($orderitems as $oi) {
echo $oi->vendor_id;
}
echo $orderitems[0]['vendor_id'];

$account = WepayHelperWepay::getUser();


if (empty($account -> access_token)) {
	$this -> setLayout('default_form');
	echo $this -> loadTemplate();
} else {
	$this -> setLayout('default_account');
	echo $this -> loadTemplate();
}
?>