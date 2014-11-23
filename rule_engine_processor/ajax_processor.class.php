<?php

class ajax_processor{
	
	public static $categoryListArrayForRules = '';
	public static $productPriceForRules = '';
	
	public static $conditionListArray = array('{total >}','{total <}','{category in}','{category not in}');
	public static $actionListArray = array('{free shipping}','{%Discount}','{flat discount}','{free product}');
	public static $finalProductCartArray = array(0=>0, 1=>0, 2=>0, 3=>'');

	//List products available
	public static function listProductsShow(){
		$searchTerm = $_REQUEST['searchTerm'];
		$selectedProductIds = trim($_REQUEST['selectedProductIds']);
		$excludedProductIds = '';
		if($selectedProductIds != ''){
			$excludedProductIds = ' AND product_id NOT IN('.$selectedProductIds.') ';
		}
		$sqlFetchProducts = 'SELECT * FROM table_product WHERE product_name LIKE "%'.$searchTerm.'%" '.$excludedProductIds;
		$result = selectResult($sqlFetchProducts);
		
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
	
	//To save the submitted rule data
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
	
	//To list all the saved rules
	public static function viewRule(){
		$sqlStatement = 'SELECT * FROM table_rule WHERE is_rule_active ="1" ';
		$result = selectResult($sqlStatement);
		$returnString = "<table style='width:100%;' border='0' class='searchProductList'>";
		$productString = "<tr><td width='100%' class='productEvenRow'>No rules exists. Please create a new rule.</td></tr>";
		if ($result->num_rows > 0) {
			$rowCount=1;
			$productString = "<tr><td width='30%' class='RuleHeadingSecondary'>Rule Name</td><td width='70%' class='RuleHeadingSecondary'>Rule Data</td></tr>";
		 	while($row = $result->fetch_assoc()) {
		 		$productRowClass = 'productOddRow';
		 		if($rowCount%2 == 0){
		 			$productRowClass = 'productEvenRow';
		 		}
		 		$rowCount++;
		        $productString .= "<tr><td class='$productRowClass'><span class='productDetailsLabel'>".$row["rule_name"]."</span></td>";
		        $productString .= "<td class='$productRowClass'>".$row["rule_data"];
		        $productString .= "</td></tr>";
		    }
		    
		}
		$returnString .= $productString."</table>";
	    return $returnString;
	}
	
	//To process the selected products based on the saved rules and return the prices and offers
	public static function processProductCart(){
		
		$selectedProductIds = trim($_REQUEST['selectedProductIds']);
		
		//To calculate the total price for the selected products
		$sqlStatementProductList = 'SELECT * FROM table_product WHERE product_id  IN('.$selectedProductIds.')';
		$resultProductList = selectResult($sqlStatementProductList);
		$totalProductPrice = 0;
		while($row = $resultProductList->fetch_assoc()) {
			$productPriceForCart = (($row["product_price"] > $row["product_special_price"]) && $row["product_special_price"] != 0) ? $row["product_special_price"] : $row["product_price"];
			$totalProductPrice += $productPriceForCart;
		}
		
		//To select the categoryids for the selected products
		$sqlStatementCategoryList = "SELECT GROUP_CONCAT(DISTINCT (f_category_id), '') as category_list FROM `table_product_category` WHERE f_product_id IN($selectedProductIds)";
		$resultCategoryList = selectResult($sqlStatementCategoryList);
		$categoryList = '';
		$categoryListArray = array();
		while($row = $resultCategoryList->fetch_assoc()) {
			$categoryList = $row["category_list"];
			$categoryListArray = explode(',',$categoryList);
		}
		
		//Assign the category ids and total product price for using in other functions
		ajax_processor::$categoryListArrayForRules = $categoryListArray;
		ajax_processor::$productPriceForRules = $totalProductPrice;
		
		//Call the function to process the available rules with the selected products and return the final product details
		return ajax_processor::productCartDetails();
		
	}
	
	//Returns the final product details based on the rules
	public static function productCartDetails(){
		
		//Returns the rules that are matching for the selected products
		$rulesResultArray = ajax_processor::processRulesForProducts();
		
		if(count($rulesResultArray) > 0){

			foreach ($rulesResultArray as $k => $v) {

				$actionForEachRule = explode('&&',$v);
				foreach ($actionForEachRule as $actionValue) {
						
					//Process each of the rule condition with the allowed conditions array 
				  	foreach (ajax_processor::$actionListArray as $actionListKey => $actionListValue) {
				  		
					  	if (strpos($actionValue, $actionListValue) !== FALSE) {
					  		//Sets the free delivery flag for the product list
					        if($actionListKey == 0){
					        	ajax_processor::$finalProductCartArray[$actionListKey] = 1;
					        }
					  		
					        //Sets the maximum discount percentage allowed for the product list
					        if($actionListKey == 1){
					        	$discountVal = trim(trim(trim(trim($actionValue),$actionListValue)),'"');
					        	if($discountVal > ajax_processor::$finalProductCartArray[$actionListKey]){
					        		ajax_processor::$finalProductCartArray[$actionListKey] = $discountVal;
					        	}
					        }
					  		
					        //Sets the total flat discount allowed for the product list
					        if($actionListKey == 2){
					        	$deductibleVal = trim(trim(trim(trim($actionValue),$actionListValue)),'"');
					        	if($deductibleVal > 0){
					        		ajax_processor::$finalProductCartArray[$actionListKey] = ajax_processor::$finalProductCartArray[$actionListKey]+$deductibleVal;
					        	}
					        }
					  		
					        //Sets the list of free products for the selected product list
					        if($actionListKey == 3){
					        	$offerProduct = trim(trim(trim(trim($actionValue),$actionListValue)),'"');
					        	if($offerProduct != ''){
					        		if(trim(ajax_processor::$finalProductCartArray[$actionListKey]) != ''){
					        			$offerProduct = ','.$offerProduct;
					        		}
					        		ajax_processor::$finalProductCartArray[$actionListKey] = ajax_processor::$finalProductCartArray[$actionListKey].$offerProduct;
					        	}
					        }
					        
					        break;
					    }
				  	}
				}
			}
		}
		
		//Returns the consolidated product cart based on the offers generated by processing the rules
		return ajax_processor::productCartOfferDetails();
	}
	
	//Returns the consolidated product cart based on the offers generated by processing the rules
	public static function productCartOfferDetails(){
		$freeShipping = '';
		$flatDiscountEligible = 0;
		$discountEligible = 0;
		$additionalProducts = '';
		
		foreach (ajax_processor::$finalProductCartArray as $key => $value) {
			
	        if($key == 0 && $value == 1){
	        	$freeShipping = '<span class="highlightedText"> * The selected products are eligible for free product delivery</span>';
	        }
	  		
	        if($key == 1 && $value > 0 ){
	        	//Calculate the eligible % discount to be reduced from the total price 
	        	$discountEligible = ((ajax_processor::$productPriceForRules/100) * $value );
	        }
	  		
	        if($key == 2 && $value > 0 ){
	        	//Calculate the eligible % discount to be reduced from the total price 
	        	$flatDiscountEligible = $value ;
	        }
	  		
	        if($key == 3 && $value != ''){
	        	//Returns the free products available for the selected products list
	        	$additionalProducts = ajax_processor::fetchProductsBasedOnIds($value);
	        }
		}
		
		$returnString = "<table class='selectedProductList' style='margin:35px auto 0; width:98%;'><tr><td width='40%'>";
		$returnString .= "Total Price </td><td> : </td><td>".number_format(ajax_processor::$productPriceForRules,2)."</td></tr>";
		
		if($flatDiscountEligible > 0 || $discountEligible > 0){
			$totalDiscount = $flatDiscountEligible + $discountEligible;
			$amountPayable = ajax_processor::$productPriceForRules-$totalDiscount;
			$returnString .= "<tr><td> Actual Amount to be Paid </td><td> : </td><td>".number_format($amountPayable,2)."<br> (Discount : ".number_format($totalDiscount,2).")</td></tr>";
		}
		
		if($additionalProducts!= ''){
			$returnString .= "<tr><td valign='top'> Eligible free products </td><td>: </td><td>".$additionalProducts."</td></tr>";
		}
		
		if($freeShipping != ''){
			$returnString .= "<tr><td colspan='3' >".$freeShipping."</td></tr>";
		}
		
		$returnString .= "</table>";
		
		return $returnString;
	}
	
	//Returns the eligible actions to be applied for the products based on rules
	public static function processRulesForProducts(){
		$sqlRulesList = "SELECT rule_id, rule_name, rule_data FROM table_rule WHERE is_rule_active = '1' ";
		$resultProductList = selectResult($sqlRulesList);

		//$individualRulesResponseArray holds the processed rule information for the selected product list.
		$individualRulesResponseArray = array();
		
		//All the results are consolidated and the complete result is returned
		while($row = $resultProductList->fetch_assoc()) {
			$processedRuleResult = ajax_processor::processCurrentRuleForProductCart($row["rule_data"]);
			if($processedRuleResult[0] == 1){
				$individualRulesResponseArray[$row['rule_id']] = $processedRuleResult[1];
			}
		}
		
		return $individualRulesResponseArray;
		
	}

	//Process each rule with the selected product cart
	public static function processCurrentRuleForProductCart($ruleData = ''){
		$isRuleSatisfied = 0;
		$processResultArray = array($isRuleSatisfied,'');
		if(trim($ruleData) == ''){
			return $processResultArray;
		}
		
		$parsedRuleData = array();
		
		//Seperates condition and the action part of the rule
		list($parsedRuleCondition,$parsedRuleAction) = explode('==>',$ruleData);
		$parsedRuleData['condition']['and'] = explode('&&',$parsedRuleCondition);
		
		//Seperates the AND and OR conditions specified in the rule for validation
		foreach ($parsedRuleData['condition']['and'] as $keyAnd=>$valueAnd) {
			foreach (explode('||',$valueAnd) as $keyOr=>$valueOr) {
				if($keyOr == 0){
					$parsedRuleData['condition']['and'][$keyAnd] = $valueOr;
				} else {
					$parsedRuleData['condition']['or'][] = $valueOr;
				} 
			}
		}
		
		//Checks if any one of the OR condition is satisfied and sets the true flag
		$isAnyOrConditionSatisfied = 0;
		if(count($parsedRuleData['condition']['or']) > 0){
			foreach ($parsedRuleData['condition']['or'] as $key=>$value) {
				if($isAnyOrConditionSatisfied == 0){
					$isAnyOrConditionSatisfied = ajax_processor::conditionValidator($value);
				} else {
					continue;
				}
			}
		}

		//Checks if all the AND condition is satisfied and sets the true flag
		$isAnyAndConditionSatisfied = 1;
		if((count($parsedRuleData['condition']['and']) > 0) && $isAnyOrConditionSatisfied == 0 ){
			foreach ($parsedRuleData['condition']['and'] as $key=>$value) {
				if($isAnyAndConditionSatisfied == 1){
					$isAnyAndConditionSatisfied = ajax_processor::conditionValidator($value);
				} else {
					continue;
				}
			}
		}
		
		if($isAnyOrConditionSatisfied || $isAnyAndConditionSatisfied){
			$isRuleSatisfied = 1;
		}
		$processResultArray = array($isRuleSatisfied,$parsedRuleAction);
		return $processResultArray;;
	}
	
	//Validates the rule conditions with the selected product list
	public static function conditionValidator($conditionData = ''){
		if(trim($conditionData) == ''){
			return 0;
		}
		$keyVal = 0;
		$conditionValue = '';
		foreach (ajax_processor::$conditionListArray as $key => $value){
			$conditionValueArray = explode($value,$conditionData);
			if(count($conditionValueArray) > 1){
				$keyVal = $key;
				$conditionValue = $conditionValueArray[1];
				$conditionValue = trim($conditionValue);
				$conditionValue = trim($conditionValue,'"');
			}
		}
		if( $keyVal < 2 ){
			//For validating totals and comparing the prices
			return ajax_processor::totalValidator($keyVal,$conditionValue);
		} else {
			//For validating products based on the category details
			return ajax_processor::categoryValidator($keyVal,$conditionValue);
		}
	}
	
	//Validates the conditions based on the total
	public static function totalValidator($keyVal,$conditionValue){
		$returnVal = 0; 
		if($keyVal == 0){
			if(ajax_processor::$productPriceForRules > $conditionValue){
				$returnVal = 1;
			} 
		} else {
			if(ajax_processor::$productPriceForRules < $conditionValue){
				$returnVal = 1;
			} 
		}
		return $returnVal;
	}
	
	//Validates the conditions based on the category
	public static function categoryValidator($keyVal,$conditionValue){
		$returnVal = 0;
		if(trim($conditionValue) == ''){
			return $returnVal;
		}
		$ruleCategoryList = explode(',', $conditionValue);
		
		if($keyVal == 2){
			foreach (ajax_processor::$categoryListArrayForRules as $categoryKey => $categoryValue) {
				if(in_array($categoryValue, $ruleCategoryList)){
					//Checks if any included category id is present, then return true
					$returnVal = 1;
				}
			}
		} else {
			foreach (ajax_processor::$categoryListArrayForRules as $categoryKey => $categoryValue) {
				//Checks if any excluded category id is present
				if(in_array($categoryValue, $ruleCategoryList)){
					$returnVal = 1;
				}
			}
			
			if($returnVal == 0){
			//Checks if no excluded category id is present, then return true
				$returnVal = 1;
			} else {
			//Checks if any excluded category id is present, then return false
				$returnVal = 0;
			}
		}
		
		return $returnVal;
	}
	
	//Returns the product names based on the product ids
	public static function fetchProductsBasedOnIds($selectedProductIds){
		if($selectedProductIds == ''){
			return false;
		}
		
		$sqlFetchProductName = "SELECT product_name FROM table_product WHERE product_id IN ($selectedProductIds) ";
		$resultProductName = selectResult($sqlFetchProductName);
		$productList = "<ul>";
		while($row = $resultProductName->fetch_assoc()) {
			$productList .= "<li>".$row['product_name']."</li>";
		}
		$productList .= "</ul>";
		
		return $productList;
		
	}
}
?>