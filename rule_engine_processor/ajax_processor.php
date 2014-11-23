<?php 
$requestType = $_REQUEST['request_type'];
require_once ("../rule_engine_processor/sql_connect.inc"); 
require_once ('../rule_engine_processor/ajax_processor.class.php');
switch ($requestType) {
	case 'listProductsShow':
	//To list all the available products
	echo ajax_processor::listProductsShow();
	break;
	
	case 'saveRule':
	//To save the newly created rules
	echo ajax_processor::saveRule();
	break;
	
	case 'viewRule':
	//To list all the saved rules
	echo ajax_processor::viewRule();
	break;
	
	case 'processProductCart':
	//To process the selected products based on the saved rules and return the prices and offers
	echo ajax_processor::processProductCart();
	break;
		
	default:
		return 'Invalid request. Cannot be processed.';
	break;
}
?>