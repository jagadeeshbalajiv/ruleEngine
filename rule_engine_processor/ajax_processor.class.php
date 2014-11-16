<?php

class ajax_processor{
	public static function listProductsShow(){
		$searchTerm = $_REQUEST['searchTerm'];
		$selectedProductIds = trim($_REQUEST['selectedProductIds']);
		$excludedProductIds = '';
		if($selectedProductIds != ''){
			$excludedProductIds = ' AND product_id NOT IN('.$selectedProductIds.') ';
		}
		$sqlStatement = 'SELECT * FROM table_product WHERE product_name LIKE "%'.$searchTerm.'%" '.$excludedProductIds;
		$result = selectResult($sqlStatement);
		$returnString = "<table style='width:100%;' border='0' class='searchProductList'>";
		$productString = "<tr><td width='100%' class='productEvenRow'>No matching products found..</td></tr>";
		if ($result->num_rows > 0) {
			$rowCount=1;
			$productString = '';
		 	while($row = $result->fetch_assoc()) {
		 		$productRowClass = 'productOddRow';
		 		if($rowCount%2 == 0){
		 			$productRowClass = 'productEvenRow';
		 		}
		 		$rowCount++;
		 		$productPriceForCart = (($row["product_price"] > $row["product_special_price"]) && $row["product_special_price"] != 0) ? $row["product_special_price"] : $row["product_price"];
		        $onClickString = $row["product_id"]."~~".$row["product_name"]."~~".$row["product_price"]."~~".$row["product_special_price"].'~~'.$productPriceForCart;
		        $productString .= "<tr onclick=\"javascript:selectProduct('$onClickString')\"><td width='100%' class='$productRowClass'><span class='productDetailsLabel'>".$row["product_name"]."</span>";
		        $productString .= "<br><span class='productDetailsLabel'>Price : </span>".$row["product_price"];
		        if($row["product_special_price"] != 0 || $row["product_special_price"] != null){
		        $productString .= "<br><span class='productDetailsLabel'>Special Price : </span>".$row["product_special_price"];
		        }
		        $productString .= "</td></tr>";
		    }
		    
		}
		$returnString .= $productString."</table>";
	    return $returnString;
	} 
	
	public static function saveRule(){
		$ruleName = $_REQUEST['ruleName'];
		$ruleCondition = $_REQUEST['ruleCondition'];
		$ruleExclusion = $_REQUEST['ruleExclusion'];
		$ruleAction = $_REQUEST['ruleAction'];
		
		$ruleData = '';
		$ruleData = $ruleCondition;
		
		if($ruleExclusion != ''){
			if($ruleData != ''){
				$ruleData .= ' && ';
			}
			$ruleData .= $ruleExclusion;
		}
		
		if($ruleAction != ''){
			if($ruleData != ''){
				$ruleData .= ' ==> ';
			}
			$ruleData .= $ruleAction;
		}
		
		$sqlStatement = "INSERT INTO table_rule (rule_name,rule_data) values('$ruleName','$ruleData')";
		$result = insertRecords($sqlStatement);
		return true;
		
	}
	
}
?>