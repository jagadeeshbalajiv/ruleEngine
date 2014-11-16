<?php 
$requestType = $_REQUEST['request_type'];
require_once ("../rule_engine_processor/sql_connect.inc"); 
require_once ('../rule_engine_processor/ajax_processor.class.php');
switch ($requestType) {
	case 'listProductsShow':
	echo ajax_processor::listProductsShow();
	break;
	
	case 'saveRule':
	echo ajax_processor::saveRule();
	break;
	
	default:
		return 'Invalid request. Cannot be processed.';
	break;
}
?>