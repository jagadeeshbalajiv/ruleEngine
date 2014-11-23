<form id='ruleDataForm' name='ruleDataForm'>
	<div class='RuleHeadingPrimary'>Create Rules :</div>
	
	<div class="productTop">
		<div class="labelText">Rule Name : </div>
		<div class="inputField">
			<input type="text" placeholder="Rule name" name="ruleName" id="ruleName" class="textbox-text validatebox-text" style='width:200px;' autocomplete="off">
		</div>
		<input type="button" class="buttonsPrimary" style="float:right;margin-right:15px;" value="Clear" id="clearRules" name="clearRules">
		<input type="button" class="buttonsPrimary" style="float:right;margin-right:15px;" value="Save Rule" id="saveRule" name="saveRule">
	</div>
	
	<div class='RuleHeadingSecondary'>Condition/Exclusion : 
		<select id='ruleConditionOption' name='ruleConditionOption' onchange='ruleOptionsChange("ruleConditionOption","ruleCondition");'>
			<option value=''>Select Condition</option>
			<option value='{total >} "" '>Cart total &gt; </option>
			<option value='{total <} "" '>Cart total &lt; </option>
			<option value='{category in} "" '>Category id</option>
		</select>
		<select id='ruleExclusionOption' name='ruleExclusionOption' onchange='ruleOptionsChange("ruleExclusionOption","ruleCondition");'>
			<option value=''>Select Exclusion</option>
			<option value='{category not in} "" '>Category id</option>
		</select>
		<select id='ruleConditionOperand' name='ruleConditionOperand' onchange='ruleOptionsChange("ruleConditionOperand","ruleCondition");'>
			<option value=''>Select Operator</option>
			<option value='&& '>AND</option>
			<option value='|| '>OR</option>
		</select>
	</div>
	<textarea id='ruleCondition' name='ruleCondition' class='ruleInput'></textarea>
	<div class='RuleHeadingSecondary'>Action :
		<select id='ruleActionOption' name='ruleActionOption' onchange='ruleOptionsChange("ruleActionOption","ruleAction");'>
			<option value=''>Select Action</option>
			<option value='{free shipping} '>Free Shipping</option>
			<option value='{%Discount} "" '>% Discount</option>
			<option value='{flat discount} "" '>Flat Discount</option>
			<option value='{free product} "" '>Free Product</option>
		</select>
		<select id='ruleActionOperand' name='ruleActionOperand' onchange='ruleOptionsChange("ruleActionOperand","ruleAction");'>
			<option value=''>Select Operator</option>
			<option value='&& '>AND</option>
		</select>
	</div>
	<textarea id='ruleAction' name='ruleAction' class='ruleInput'></textarea>
	
</form>