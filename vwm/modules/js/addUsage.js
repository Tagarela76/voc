
$(document).ready(function() {
		
		if(noMWS == true) {
			initNoMWS();
		}
		
		getProductInfo();
		$('#selectProduct').change(function(el)
		{
			getProductInfo();
			
			var unitType=$("#selectUnittype").attr('value');
			var productID = $("#selectProduct").attr('value');
			
			checkUnittypeWeightWarning(unitType, productID, $("#errorProductWeight"));
			
			checkUnittypeWeightWarning(unitType,productID,$("#errorProductWeight"));
		});
		
		
		$('#mixDescription').change(function() {
			
			
			
			val = $('#mixDescription').val();
			
			if(val != "") {
				isMixDescriptionUnique(val);
			} else {
				mixValidator.mixValid = false;
			}
		});
		
		$("#quantity").change(function(){
			if( $(this).val() != "" ) {
				$("#errorAddProduct").css("display","none");
			}
		});
		
		/*AAAAAAAAAAAAAAAA*/
});

var mixValidator = new CMixValidator();

function initNoMWS() {
	
	waste.value = $("#wasteValue").val();
	$("#wasteValue").change(function(){
		
		waste.value = $(this).val();
	});
	
	waste.unittype = $("#selectWasteUnittype").attr('value');
}

	/*$(function()
	{
		
	});*/
	
	function IsNumeric(input)
	{
	   return (input - 0) == input && input.length > 0;
	}
	
	function generateLink () {
		
		if(editForm == false) {
			url = '/vwm/?action=addItemAjax&category=mix&departmentID='+departmentID;
		} else {
			url = '/vwm/?action=editItemAjax&category=mix';
			url += "&id=" + mixID;
		}
		
		
		url += "&mix=" + getMix().toJson();
		
		if(products != undefined) {
			
			url += '&products=' + products.toJson();
		}
		
		
		if(undefined != window['wasteStreamsCollection']) {
			waste = wasteStreamsCollection.toJson();
			url += '&wasteJson=' + waste;
		} else {
			url += '&wasteJson=' + $.toJSON(waste);
		}
		
		url += "&debug=" + "true";
		
		
		
		
		$("#addMix").attr('href',url);
		$("#addMix").css('display','inline');
	}
	
	function validateWaste() {
		
		if(window['isMWS'] != undefined && window['isMWS'] == true) {
			
			result = wasteStreamsCollection.isQuantityFilled();
			return result;
		} else {
			
			/*TODO доделать если модуль выключен*/
			return true;
		}
	}
	
	function getMix() {
		
		mixObj = new CMix();
		
		mixObj.setDescription($("#mixDescription").val());
		mixObj.setExcemptRule($("#exemptRule").val());
		mixObj.setMixDate($("#calendar1").val());
		mixObj.setAPMethod($("#selectAPMethod option:selected").val());
		mixObj.setEquipment($("#selectEquipment option:selected").val());
		mixObj.setRule($("#rule option:selected").val());
		mixObj.selectUnittypeClass = $("#selectUnittypeClass option:selected").val();
		return mixObj;
	}
	
	
	
	function addMix() {
		
		
		
		if(mixValidator.isValid() != true ) {
			
			//alert("Mix invalid!");
			return;
		} else if (products.Count() == 0) {
			alert("Product count is empty!");
			return;
		}
		
		$('span[name="storageOverflowError"]').remove();
		
		if(!validateWaste()){
			$("#wasteValidError font").text("Fill quantity!");
			$("#wasteValidError").css("display","inline");
			return;
		} else {
			$("#wasteValidError").css("display","none");
		}
		
		mix = getMix();
		
		if(noMWS != true){
			waste = wasteStreamsCollection.toJson();
		} else {
			waste = $.toJSON(waste);
			
		}
		
		if(editForm == false) {
			urlData = {"action" : "addItemAjax", "category" : "mix", "departmentID": departmentID, "wasteJson" : waste, "products" : products.toJson() , "mix" : mix.toJson()};
		} else {
			urlData = {"action" : "editItemAjax", "category" : "mix", "departmentID": departmentID, "wasteJson" : waste, "products" : products.toJson() , "mix" : mix.toJson(), "id" : mixID};
		}
		
		//alert("Everything is ok!");
		//return;
		
		$.ajax({
			url:'/vwm/index.php',
			type: "GET",
			async: true,
			data: urlData,
			dataType: "html",
      		success: function (response) 
      			{         		
      				
      				
      				if(response == 'DONE') {
      					//res = confirm("Mix updated successfully! Do you want browse mixes?");
      					if( true) {
      						document.location = "?action=browseCategory&category=department&id="+departmentID+"&bookmark=mix";
      					}
      				}
      				else if(response!='false') {
      					var res = eval('(' + response + ')');
      					
      					
      					if(res.storageError != undefined) {
      						
      						storagesSelects = $('select[name^="selectStorage_"]');
          					
          					jQuery.each(storagesSelects, function() {
          				      val = $("option:selected", this).val();
          				      
          				      if(res.storageOverflow[val] != undefined) {
          				    	  
          				    	  //$(this).append("<span style='color:Red;'>"+res.storageError+"</span>");
          				    	 $("<span name='storageOverflowError' style='color:Red;'>"+res.storageError+"</span>").insertAfter($(this));
          				      }
          				   });
      					} else {
      						//alert('storages are ok');
      					}
      					
      					
      				} else {
      					//alert('silent...');
      				}
      			}
			
		});
		
		//$("#addMix").attr('href','/vwm/?action=addItemAjax&category=mix&departmentID='+departmentID+'&wasteJson='+waste);
		//$("#addMix").css('display','inline');
	}

	
	function isMixDescriptionUnique(value) {
		//var departmentID
		$.ajax({
      		url: "modules/ajax/saveMix.php",      		
      		type: "GET",
      		async: false,
      		data: {"action":"isMixDescrUnique", "descr":value, "depID":departmentID},      			
      		dataType: "html",
      		success: function (response) 
      			{         				
      				if(response!='false')
      				{      	
      					var respObj = eval('(' + response + ')');
      					if(respObj.isUnique == false) {
      						
							$("#mixDescriptionErrorAlreadyInUse").css('display','block');
							mixValidator.mixValid = false;
						} else {
							
							$("#mixDescriptionErrorAlreadyInUse").css('display','none');
							mixValidator.mixValid = true;
						}
      				}										
      			}        		   			   	
			});
	}
	
	function getProductInfo() {
		var product_id=$('#selectProduct').attr('value');		
		if(product_id.length>0){
			$('#product_descPreloader').css('display','block');
			$('#coatingPreloader').css('display','block');			
			$.ajax({
      		url: "modules/ajax/getProductInfoInMixes.php",      		
      		type: "GET",
      		async: false,
      		data: { "product_id":product_id},      			
      		dataType: "html",
      		success: function (response) 
      			{         				
      				if(response!='false')
      				{      					
		      			resp=eval("("+response+")");  					
						//$('#product_desc').attr('value',resp['description']);
		      			$('#product_desc').text(resp['description']);
						//$('#coating').attr('value',resp['coatName']);	
		      			$('#coating').text(resp['coatName']);
						var currentSelectedProductSupportDensity = resp['supportWeight'];
						
      				}
      				
      				$('#product_descPreloader').css('display','none');
					$('#coatingPreloader').css('display','none');										
      			}        		   			   	
			});
		}
	}
	var unittypeWarning = false;
	function checkUnittypeWeightWarning(unitType, productID, jqElError) {
		
		//Check is unit type is weight
		$.ajax({
      		url: "/vwm/index.php",      		
      		type: "GET",
      		async: false,
      		data: {"action":"validateProductAjax", "category" : "mix" , "unittypeID" : unitType, "productID" : productID},      			
      		dataType: "html",
      		success: function (response) 
      			{         				
      				if(response != 'false')
      				{      					
		      			resp = eval("("+response+")");  	
		      			
		      			if(resp.summary == "false") {
		      				unittypeWarning = true;
		      				jqElError.css('display','block');
		      			} else {
		      				
		      				jqElError.css('display','none');
		      				unittypeWarning = false;
		      			}
      				}
      			}        		   			   	
			});
	}
		
	function getUnittypes(sel, companyID, companyEx) {
		var sysType=$(sel).attr('value');
		
		var productAddedIdx;
		if (sel.name.substring(0,20) == 'selectUnittypeClass_') {
			productAddedIdx = sel.name.substring(20);		
			$("#unittype_"+productAddedIdx).empty();
			$('#unittype_'+productAddedIdx+'Preloader').css('display','block');
			
			if(sysType.length > 0){			
				$.ajax({
      			url: "modules/ajax/getUnitTypes.php",      		
      			type: "GET",
	      		async: false,
	      		data: {"sysType":sysType,"companyID":companyID,"companyEx":companyEx},      			
	      		dataType: "html",
	      		success: function (response) 
	      			{   
	      				writeUnittype(response,'unittype_'+productAddedIdx)									
	      			}        		   			   	
				});
				
				
			}					 
		} else if (sel.name == 'selectWasteUnittypeClass') {				
			$("#selectWasteUnittype").empty();
			$('#selectWasteUnittypePreloader').css('display','block');				
			if(sysType.length > 0){				
				$.ajax({
      			url: "modules/ajax/getUnitTypes.php",      		
      			type: "GET",
	      		async: false,
	      		data: {"sysType":sysType,"companyID":companyID,"companyEx":companyEx},      			
	      		dataType: "html",
	      		success: function (response) 
	      			{   
	      				writeUnittype(response,'selectWasteUnittype')
	      				waste.unittype = $("#selectWasteUnittype").attr('value');
	      			}        		   			   	
				});				
			}
		} else if (sel.name == 'selectUnittypeClass') {				
			$("#selectUnittype").empty();
			$('#selectUnittypePreloader').css('display','block');		
				
			if(sysType.length > 0){				
				$.ajax({
      			url: "modules/ajax/getUnitTypes.php",      		
      			type: "GET",
	      		async: false,
	      		data: {"sysType":sysType,"companyID":companyID,"companyEx":companyEx},      			
	      		dataType: "html",
	      		success: function (response) 
	      			{   
	      				writeUnittype(response,'selectUnittype')									
	      			}        		   			   	
				});			
			}
		}
		else if(sel.name.substring(0,28) == "product_selectUnittypeClass_") {
			
			
			productAddedIdx = sel.name.substring(28);		
			$("#product_selectUnittype_"+productAddedIdx).empty();
			
			if(sysType.length > 0){			
				$.ajax({
      			url: "modules/ajax/getUnitTypes.php",      		
      			type: "GET",
	      		async: false,
	      		data: {"sysType":sysType,"companyID":companyID,"companyEx":companyEx},      			
	      		dataType: "html",
	      		success: function (response) 
	      			{
	      				
	      				writeUnittype(response,"product_selectUnittype_"+productAddedIdx);									
	      			}        		   			   	
				});
				
				
			}	
		}
		
	}	
	
	function fillSelectUnittypes(unittypeClass, selectUnittypeId) {
		
		$("#"+selectUnittypeId+"").empty();
		
		$.ajax({
  			url: "modules/ajax/getUnitTypes.php",      		
  			type: "GET",
      		async: false,
      		data: {"sysType":unittypeClass,"companyID":companyID,"companyEx":companyEx},      			
      		dataType: "html",
      		success: function (response) 
      			{
      				
      				writeUnittype(response,selectUnittypeId);									
      			}        		   			   	
			});
	}
			
	function writeUnittype(response,elementID) {
		
		if (response!='false')
		{
			var resp=eval("("+response+")");			
			for (var key in resp)
			{			
				
				$('#'+elementID).append(
					"<option value='"+resp[key]['unittype_id']+"'>"+resp[key]['name']+"</option>");					
			}
		}
		
		$('#'+elementID+"Preloader").css('display','none');			
	}	
	
	
	
	var selectedProducts = new Array();
	
	var products = new CProductCollectionObj();
	
	function addProduct(productID, quantity, unittypeId, unittypeClass) {
		
		products.addProduct(productID, quantity, unittypeId, unittypeClass);
		//alert('add product');
		$('#addProductPreloader').css('display', 'block');
		$("#addProductsContainer").css('display','block');
		
		$.ajax({
      		url: "modules/ajax/saveMix.php",      		
      		type: "GET",
      		async: true,
      		data: { "action":"getProductInfo", "productID":productID},      			
      		dataType: "html",
      		success: function (r) {
      			//$('#addProductPreloader').css('display', 'none');
      			
      			tr = $("<tr>").attr({
      				id:"product_row_"+productID
      			});
      			
      			td1 = $("<td>");
      			
      			
      			
      			checkbox = $("<input>").attr({
      				type:"checkbox",
      				value:"" + productID,
      				checked:"checked"
      			}
      			);
      			
      			td1.attr({
      				"class":"border_users_r border_users_b border_users_l"
      			});
      			
      			td1.append(checkbox);
      			
      			tr.append(td1);
      			
      			
      			
      			var resp=eval("("+r+")");
      			
  				var supplier 	= resp['supplier_id'];
  				var productNR 	= resp['product_nr'];
  				var descr 		= resp['name'];
  				
  				tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(supplier)));
				tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(productNR)));
				tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(descr)));
				
				
				if(editForm == false) {
					
					unittypeDescr = $("#selectUnittype option:selected").text();
					
					tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(quantity)));
					tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(unittypeDescr)));
					$("#addedProducts").find("tbody").append( tr );
				} else {
					
					txQ = $("<input>").attr("type","text").attr("id","product_" + productID + "_quantity").val(quantity).numeric();
					
					//txQ..attr("onchange","setProductQuantity("+productID+")")
					txQ.change( { "productID" : productID} ,function(eventObject) {
						setProductQuantity(eventObject.data.productID);
					});
					
					tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append(txQ));
					//tr.append($("<td>").attr({class:"border_users_r border_users_b"}).append($("<span>").text(unittypeClass)));
					
					//elUnittypeClass = $("#selectUnittypeClass").clone(true);
					
					//elUnittypeClass = $("<select>");
					
					elUnittypeClass = createSelectUnittypeClass("product_selectUnittypeClass_"+productID);
					
					//elUnittypeClass.append(selectOptions2UnitTypeClasses());
					
					elUnittypeClass.attr("name","product_selectUnittypeClass_"+productID);
					
					product = products.getProduct(productID);
					elUnittypeClass.attr('value',product.unittypeClass);
					
					
					
					//elUnittypeClass.attr("onchange","getUnittypes(this, "+companyId+", "+companyEx+"); setProductUnittype("+productID+"); setProductUnittypeClass("+productID+");");
					
					elUnittypeClass.change( { "productID" : productID} ,function(eventObject) {
						
						//console.log($(this).get());
						//alert($(this).attr("name"));
						getUnittypes(document.getElementById($(this).attr("name")), companyId, companyEx); 
						setProductUnittype(eventObject.data.productID);
						setProductUnittypeClass(eventObject.data.productID);
						
					});
					
					td = $("<td>").attr({"class":"border_users_r border_users_b"});
					td.append(elUnittypeClass);
	
					
					
					//elUnittypeId = $("#selectUnittype").clone(true);
					elUnittypeId = $("<select>");
					id = 'product_selectUnittype_'+productID;
					
					
					elUnittypeId.attr('id',id).attr('name',id);
					elUnittypeId.attr("onchange","setProductUnittype("+productID+")");
					

					td.append(elUnittypeId);
					//<div class="error_img"  id="mixDescriptionErrorAlreadyInUse" style="display:none;"><span class="error_text" >Entered name is already in use!</span></div>
					
					td.append("<div class='error_img error_text'  id='productError_"+productID+"' style='display:none;'>Failed to convert weight unit to volume because product density is underfined! You can set density for this product or use volume units.</span></div>")
					
					tr.append(td);
					
					$("#addedProducts").find("tbody").append( tr );
					
					getUnittypes(document.getElementById(elUnittypeClass.attr('id')), companyId, companyEx);
				}
				
				
				//var obj = {quantity:quantity,unittypeID:selectUnittype};
				//selectedProducts["product_"+productID.toString()] = obj;
				//alert(selectedProducts.length);//[productID.toString()].quantity);
				//alert(selectedProducts["product_"+productID.toString()].unittypeID);
				//arr = [productID, quantity, selectUnittype];
				//selectedProducts.push(arr);
				
				
				//alert(products.toJson());
				
				//var encoded = $.toJSON(selectedProducts); 
				
				
				
      		}
		});
	}
	
	function setProductUnittype(productID) {
		p = products.getProduct(productID);
		
		p.selectUnittype = $("#product_selectUnittype_"+productID).val();
		
		checkUnittypeWeightWarning(p.selectUnittype, p.productID , $("#productError_"+productID));
	}
	
	function setProductQuantity(productID) {
		products.getProduct(productID).quantity = $("#product_"+productID+"_quantity").val();
	}
	
	function setProductUnittypeClass(productID) {
		products.getProduct(productID).unittypeClass = $("#product_selectUnittypeClass_"+productID).attr("value");
	}
	
	function addProduct2List() {			
		
		var productID = $("select#selectProduct option:selected").val();
		var quantity = $("#quantity").val();
		var selectUnittypeClass = $("#selectUnittypeClass").val();
		var selectUnittype = $("#selectUnittype").val();	

		var unittypeText = $("#selectUnittype option:selected").text();

		var unitType=$("#selectUnittype").attr('value');
		var productID = $("#selectProduct").attr('value');
		
		checkUnittypeWeightWarning(unitType, productID, $("#errorProductWeight"));
		
		if(unittypeWarning == true) {
			return;
		}
		else if(quantity != "" && quantity > 0) {
			$("#quantity").val("0.0");
		} else {
			$("#errorAddProduct .error_text").text("Type quantity!");
			$("#errorAddProduct").css('display','inline');
			$("#quantity").focus();
			$("#quantity").select();
			return;
		}
		
		//var unittypeClassSelectBox = $('#selectUnittypeClass').clone();
		//var unittypeSelectBox = $('#selectUnittype').clone();
		
		//alert(productID+", "+quantity+", "+selectUnittypeClass+", "+selectUnittype);
		
			
		
		//$("#selectProduct option[value='"+productID+"']").remove();
		selectedOption = $("#selectProduct option[value='"+productID+"']");

		selectedOption.attr({disabled:"disabled"}).removeAttr('selected');
		nextEl = selectedOption.next("option");//.attr('selected', 'selected');
		
		

		getProductInfo();
			
		addProduct(productID, quantity, selectUnittype, selectUnittypeClass);
	}	
	
	function clearSelectedProducts() {
		
		checkboxes = $("#addProductsContainer").find("input[type='checkbox']");
		var rowsToRemove = new Array();
		
		checkboxes.each(function(i){
			
			id = this.value;
			if(this.checked) {
				//$("#product_row_"+id).remove();
				rowsToRemove.push(id);
				$("#selectProduct option[value='"+id+"']").removeAttr('disabled')
			}
			
		});
		
		for ( keyVar in rowsToRemove ) {
			id = rowsToRemove[keyVar];
			$("#product_row_" + id).remove();
			products.removeProduct(id);
		}
		alert(products.toJson());
		//checkboxes.attr({checked:"checked"});
	}
	
	function selectAllProducts(select) {
		
		checkboxes = $("#addProductsContainer").find("input[type='checkbox']");
		checkboxes.each(function(i){
			this.checked = select;
		});
	}
	
	
	function generateNotify(text, color) {
		var colorPrefix;
		var colorPrefixTail;
		
		//	generate prefix by color
		switch (color) {
			case 'red':
				colorPrefix = 'o';	//	orange
				colorPrefixTail = 'orange';
				break;
			case 'green':
				colorPrefix = 'gr';	//	green
				colorPrefixTail = 'green';
				break;
			default:
				colorPrefix = 'r';	//	blue
				colorPrefixTail = 'blue';
		}
			
		//	create table
		var table = document.createElement('TABLE');
		table.align = 'center';
		table.cellPadding = '0';
		table.cellSpacing = '0';
		table.className = 'pop_up';
		var tbody = document.createElement('TBODY');	//	TBODY is needed for IE
			
		//	create first row
		var row1 = document.createElement('TR');
		var data1 = document.createElement('TD');
		var divOut = document.createElement('DIV');
		divOut.className = 'bl_'+colorPrefix;
		var divMiddle = document.createElement('DIV');
		divMiddle.className = 'br_'+colorPrefix;
		var divIn = document.createElement('DIV');
		divIn.className = 'tl_'+colorPrefix;
		var divText = document.createElement('DIV');
		divText.className = 'tr_'+colorPrefix;
			
		//	create seond row
		var row2 = document.createElement('TR');
		var data2 = document.createElement('TD');
		data2.className = 'tail_'+colorPrefixTail;
		
		//	build model
		divText.appendChild(document.createTextNode(text));
		divIn.appendChild(divText);
		divMiddle.appendChild(divIn);
		divOut.appendChild(divMiddle);
		data1.appendChild(divOut);
		row1.appendChild(data1);
		row2.appendChild(data2);
			
		tbody.appendChild(row1);
		tbody.appendChild(row2);
		
		table.appendChild(tbody);		
			
		return table;
	} 		