$(document).ready(function(){
	//Rendering components with jQuery EasyUI plugin - Begins
	$('#selectMainSection').tabs({
	    border:false,
	    height: 450
	});
	//Rendering components with jQuery EasyUI plugin - Ends
	
	$('#searchProductForCart').focus().keyup(function(e){
		e.preventDefault();
		var searchTerm = $.trim($('#searchProductForCart').val());
		if(searchTerm.length >= 3){
			loadProductList(searchTerm);
		} else {
			$('#hiddenDivForProducts').hide();
		}
	});
	
	$('#searchProductForCart').blur(function(e){
		e.preventDefault();
		setTimeout(function(){
			$('#hiddenDivForProducts').hide().html('');
			$('#searchProductForCart').val('');
		}, 1000);
		
	});
	
	$('#clearSelectedProducts').click(function(e){
		e.preventDefault();
		$('#searchProductForCart, #selectedProductIds').val('');
		$('.selectedProductsListContainer').html('');
	});
	
	$('#saveRule').click(function(){
		if($.trim($('#ruleName').val()) != ''){
			submitRuleData();
		} else{
			alert('Please enter a valid name for the rule');
		}
		
	});
	
	//To calculate width for selected products and rule engine results section
	var wrapperWidthRendered = $('.wrapper_prim').width();
	var containerPrimarywidth = (wrapperWidthRendered*65)/100;
	var containerSecondarywidth = (wrapperWidthRendered*34.5)/100;
	$('.cont_prim').css('width',containerPrimarywidth);
	$('.cont_sec').css('width',containerSecondarywidth);
});

function loadProductList(searchTerm){
	$.ajax({
		  url: "../rule_engine_processor/ajax_processor.php",
		  method: "POST",
		  data:{
				request_type : 'listProductsShow',
				searchTerm : searchTerm,
				selectedProductIds : $.trim($('#selectedProductIds').val())
			}
	}).done(function( data ) {
		$('#hiddenDivForProducts').show().html(data);
		});
}

function selectProduct(productDetails){
	var productDetailsArray = productDetails.split("~~");
	var selectedProductIds = $.trim($('#selectedProductIds').val());
	if(selectedProductIds != ''){
		selectedProductIds = selectedProductIds+',';
	}
	selectedProductIds = selectedProductIds+productDetailsArray[0];
	$('#selectedProductIds').val(selectedProductIds);
	var selectedProductsList = $('.selectedProductsListContainer').html()+"<div class='selectedProductList'>"+productDetailsArray[1]+"</div>";
	$('.selectedProductsListContainer').html(selectedProductsList);
}

function ruleOptionsChange(selectedOptionId, targetInputId){
	insertStringNextToCursor(targetInputId,$('#'+selectedOptionId+' option:selected').val());
	$('#'+selectedOptionId).val('');
}

function insertStringNextToCursor(elementId,text) {
    var txtarea = document.getElementById(elementId);
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? 
        "ff" : (document.selection ? "ie" : false ) );
    if (br == "ie") { 
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart ('character', -txtarea.value.length);
        strPos = range.text.length;
    }
    else if (br == "ff") strPos = txtarea.selectionStart;

    var front = (txtarea.value).substring(0,strPos);  
    var back = (txtarea.value).substring(strPos,txtarea.value.length); 
    txtarea.value=front+text+back;
    strPos = strPos + text.length;
    if (br == "ie") { 
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart ('character', -txtarea.value.length);
        range.moveStart ('character', strPos);
        range.moveEnd ('character', 0);
        range.select();
    }
    else if (br == "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
}

function submitRuleData(){
	$.ajax( {
	      type: "POST",
	      url: "../rule_engine_processor/ajax_processor.php?request_type=saveRule",
	      data: $('#ruleDataForm').serialize(),
	      success: function( response ) {
			$('#ruleDataForm').trigger("reset");
	      }
	    } );
}