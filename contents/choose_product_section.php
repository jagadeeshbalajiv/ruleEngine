<div class='productTop'>
	<div class='labelText'>Products : </div>
	<div class='inputField'>
		<span class="textbox" style="width: 298px; height: 20px;">
			<span class="textbox-addon textbox-addon-right" style="right: 0px;">
				<a tabindex="-1" icon-index="0" class="textbox-icon icon-search textbox-icon-disabled" href="javascript:void(0)" style="width: 18px; height: 20px;"></a>
			</span>
			<input type="text" autocomplete="off" class="textbox-text validatebox-text productSearchBox" id='searchProductForCart' name='searchProductForCart' placeholder="Search.. (min 3 characters)">
		</span>
		<input type='hidden' id='selectedProductIds' name='selectedProductIds' />
		<div id='hiddenDivForProducts'></div>
	</div>
	<div style="width: 298px; height: 20px;float: left;padding-top: 10px; padding-left:4px; padding-right:4px;width: auto;">(Use search terms like Puma, Nike, Adidas, Lee cooper and Woodland)</div>
	<input type="button" name='clearSelectedProducts' id='clearSelectedProducts' value='Clear Cart' class='buttonsPrimary'/>
</div>
<div class='productBottom'>
	<div class='cont_prim'>
		<div class="labelText" style='width:100%;'> Selected Products :</div>
		<div class="selectedProductsListContainer"></div>
	</div>
	<div class='cont_sec'>
		<div class="labelText" style='width:-moz-available;width:-webkit-fill-available;'> Cart Details :</div>
		<div class="cartDetails"></div>
	</div>
</div>