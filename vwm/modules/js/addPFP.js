$(function() {
	
	
	$("#addProduct").click(function(){
		
		productID = $("#selectProduct").val();
		ratio = $("#ratio").val();
		
		selectedOption = $("#selectProduct option[value='"+productID+"']");
		selectedOption.attr({disabled:"disabled"}).removeAttr('selected');
		if(typeof $.browser.msie != "undefined" && $.browser.msie == true) {
			nextEl = selectedOption.next("option");//.attr('selected', 'selected');
			nextEl.attr('selected', 'selected');
		}
		
		addProductToList(productID,ratio);
	});
	
	$("#ratio").numeric();
	
	$("#pfp_description").change(function(el){
		
		val = $(el.target).val();
		
		if(edit == true && val == pfp_descr) {
			
			$("#descr_error").css("display",'none');
			return;
		}
		
		departmentID = $("#department_id").val();
		
		if(val != ""){
			if(edit == false || (edit == true && pfp_descr != val)) {
				isUniqueDescription(departmentID,val);
				if(isDescrUnique == false) {
					valid = false;
					errors.push("Description is not unique!");
					$("#descr_error").css("display",'inline');
					$("#descr_error span").text("Description is not unique!");
				}
			} else if (edit == true && pfp_descr == val) {
				$("#descr_error").css("display",'none');
			}
		} else {
			$("#descr_error").css("display",'inline');
			$("#descr_error span").text("Description cannot be empty!");
		}
		
		
		/*if(val != "") {
			isUniqueDescription(departmentID, val);
			
			if(isDescrUnique == false){
				$("#descr_error").css("display","inline");
				$("#descr_error span").text("Description is not unique!");
			} else {
				$("#descr_error").css("display","none");
			}
			
		} else {
			$("#descr_error").css("display",'inline');
			$("#descr_error span").text("Description cannot be empty!");
		}*/
		
		
		
	});
	
	$("#save").click(function(){
		validResult = isValid();
		
		if(validResult != false) {
			addPFP();
		}
	});
	
});
var isDescrUnique = true;
var errors = [];

function addProduct(productID,ratio) {
	selectedOption = $("#selectProduct option[value='"+productID+"']");
	selectedOption.attr({disabled:"disabled"}).removeAttr('selected');
	if(typeof $.browser.msie != "undefined" && $.browser.msie == true) {
		nextEl = selectedOption.next("option");//.attr('selected', 'selected');
		nextEl.attr('selected', 'selected');
	}
	
	addProductToList(productID,ratio);
}

function addPFP() {
	document.getElementById("addPFPForm").submit();
}

function isValid() {
	errors = [];
	valid = true;
	
	
	
	descr = $("#pfp_description").val();
	
	if(descr == "") {
		
		valid = false;
		errors.push("Description cannot be empty!");
		$("#descr_error").css("display",'inline');
		$("#descr_error span").text("Description cannot be empty!");
	}
	departmentID = $("#department_id").val();
	if(edit == false || (edit == true && pfp_descr != descr)) {
		isUniqueDescription(departmentID,descr);
		if(isDescrUnique == false) {
			valid = false;
			errors.push("Description is not unique!");
			$("#descr_error").css("display",'inline');
			$("#descr_error span").text("Description is not unique!");
		}
	} else if (edit == true && pfp_descr == descr) {
		$("#descr_error").css("display",'none');
	}
	
	
	productCount = parseInt($("#productCount").val());
	
	if(productCount < 2) {
		valid = false;
		errors.push("Error! Products count less than 2!");
		//products_error
		
		$("#products_error").css("display",'inline').css("float",'right');
		$("#products_error span").text("Error! Products count less than 2!");
	}
	
	if($("input[name='pfp_primary']").filter(":checked").length == 0) {
		valid = false;
		alert("Select primary product!");
	}
	/*if($("input[name='pfp_primary'] :checked").length) {
		
	}*/
	//return false;
	return valid;
}


function addProductToList(productID,ratio) {
	
	//?action=getPFPProductInfo&category=mix&id=64&json=true
	$.ajax({
  		url: "modules/ajax/saveMix.php",      		
  		type: "GET",
  		async: true,
  		data: { "action":"getProductInfo", "productID":productID},      			
  		dataType: "html",
  		success: function (r) {
  			
  			productCount = $("#productCount").val();
  			
  			if(productCount >= 1) {
  				$("#products_error").css("display",'none');
  			}
  			
  			tr = $("<tr>").attr({
  				id:"product_row_"+productCount
  			});
  			td1 = $("<td>");
  			
  			
  			checkbox = $("<input type='checkbox' value='"+productCount+"' CHECKED>");
  			
  			
  			td1.attr({
  				"class":"border_users_r border_users_b border_users_l"
  			});
  			
  			td1.append(checkbox);
  			
  			tr.append(td1);
  			
  			
  			
  			var resp=eval("("+r+")");
  			
			var supplier 	= resp['supplier_id'];
			var productNR 	= resp['product_nr'];
			var descr 		= resp['name'];
			
			tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<input type='radio' name='pfp_primary' value='"+productID+"'>")));
			tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(supplier)));
			tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(productNR)));
			tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(descr)));
			
			//edit == false
			if(1 == 2) {
				tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(ratio)));
				tr.append($("<input type='hidden'>").attr("name","product_"+productCount+"_ratio").attr('id',"product_"+productCount+"_ratio").attr("value",ratio));
			} else {
				tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<input type='text'>").attr("name","product_"+productCount+"_ratio").attr('id',"product_"+productCount+"_ratio").attr("value",ratio)));
			}
			
			
			
			
			tr.append($("<input type='hidden'>").attr("name","product_"+productCount+"_id").attr('id',"product_"+productCount+"_id").attr("value",productID));
			
			
			productCount++;
			$("#productCount").val(productCount);
			
			$("#addedProducts").find("tbody").append( tr );
  		}
	});
}

function clearSelectedProducts() {
	
	checkboxes = $("#addProductsContainer").find("input[type='checkbox']");
	var rowsToRemove = new Array();
	
	checkboxes.each(function(i){
		
		id = this.value;
		if(this.checked) {
			//$("#product_row_"+id).remove();
			rowsToRemove.push(id);
			productID = $("#product_"+id+"_id").val();
			$("#selectProduct option[value='"+productID+"']").removeAttr('disabled')
		}
		
	});
	
	rowsToRemove.reverse();
	
	
	productCount = $("#productCount").val();

	for ( keyVar in rowsToRemove ) {
		num = rowsToRemove[keyVar];

		$("#product_row_" + num).remove();
		
		
		
		if(productCount > 0) {
			//alert("remove row " + num);
			moveProductsRow(num, productCount);
		}
		
		productCount--;
		
		
		//product_row_2
		target_id = "#product_row_"+target;
		need_id = "product_row_"+need;
		$(target_id).find("input[type='checkbox']").val(need);
		$(target_id).attr('id',need_id);
	}
	if(productCount == -1) productCount = 0;
	$("#productCount").val(productCount);
}

function isUniqueDescription(departmentID, description) {
	
	$.ajax({
  		url: "index.php?&category=mix",      		
  		type: "GET",
  		async: false,
  		data: { "action":"isPFPUnique", "departmentID" : departmentID, "ajax":true, "descr" : description},      			
  		dataType: "html",
  		success: function (r) {
  			if(r == "TRUE") {
  				isDescrUnique = true;
  				return true;
  			} else {
  				isDescrUnique = false;
  			}
  		}
	});
}

function moveProductsRow(removedProductRowNum, productCount) {
	
	removedProductRowNum = parseInt(removedProductRowNum);
	productCount = parseInt(productCount);
	
	//alert('removedProductRowNum: ' + removedProductRowNum + " productCount:" + productCount);
	
	for( i=removedProductRowNum; i <= productCount; i++) {
		
		target = 1 + parseInt(i);
		need = i;
		
		//alert(" move " + target + " to " + need);
		
		target_id = "#product_"+target+"_id";
		need_id = "product_"+need+"_id";
		$(target_id).attr('id',need_id).attr('name',need_id);
		
		//product_2_ratio
		target_id = "#product_"+target+"_ratio";
		need_id = "product_"+need+"_ratio";
		$(target_id).attr('id',need_id).attr('name',need_id);
		

		
	}
}

function removeProductFromList(productNumberInList) {
	
}

function selectAllProducts(select) {
	
	checkboxes = $("#addProductsContainer").find("input[type='checkbox']");
	checkboxes.each(function(i){
		this.checked = select;
	});
}