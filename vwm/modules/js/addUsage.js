
$(document).ready(function() {

	$("tr[name='pfp_row']").click(function(e){

		//$("#pfpdetails").after($(this));
		$("tr[name='pfp_details']").css("display","none");
		$("tr[name='pfp_row']").attr('class','');


		$("table[name='pfp_details_products']").remove();
		id = $(this).attr('id');
		//alert("table row len: " + $("#"+id+"_details").length);
		if(typeof $.browser.msie != "undefined" && $.browser.msie == true) {
			//alert("IE!!");
			$("#"+id+"_details").css("display","block");
		} else {
			$("#"+id+"_details").css("display","table-row");
		}

		$("#"+id+"_details .preloader").css("display","block");
		loadPFPDetails(id);
		//$(this).simpletip({ content: 'My Simpletip', fixed: false });
		//alert(e.pageX + ":" + e.pageY);
		//$("#pfpdetails").css("left",e.pageX).css("top",e.pageY);
	});

		if(noMWS == true) {
			initNoMWS();
		}else{
			initRecycle();
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

function loadPFPDetails(pfp_id) {
	urlData = {"action" : "getPFPDetailsAjax", "category" : "mix", "departmentID": departmentID, "pfp_id" : pfp_id};
	$.ajax({
		url:'index.php',
		type: "GET",
		async: true,
		data: urlData,
		dataType: "html",
  		success: function (response)
  			{
  				//alert("len:"+$("#"+id+"_details .preloader").length);
  				$("#"+id+"_details .preloader").css("display","none").after(response);
  				$("#"+id).attr('class','pfpListItemSelected');
  			}
	});
}

var mixValidator = new CMixValidator();

function initNoMWS() {

	waste.value = $("#wasteValue").val();
	if($("#selectWasteUnittype").attr('value')) {
	waste.unittype = $("#selectWasteUnittype").attr('value');
	}
	$("#wasteValue").change(function(){
		waste.unittype = $("#selectWasteUnittype").attr('value');
		waste.value = $(this).val();
		calculateVOC();
	});



//RECYCLE
	recycle.value = $("#recycleValue").val();
	if($("#selectRecycleUnittype").attr('value')) {
	recycle.unittype = $("#selectRecycleUnittype").attr('value');
	}
	$("#recycleValue").change(function(){
		recycle.unittype = $("#selectRecycleUnittype").attr('value');
		recycle.value = $(this).val();
		calculateVOC();
	});
calculateVOC();
}

function initRecycle() {
	recycle.value = $("#recycleValue").val();
	if($("#selectRecycleUnittype").attr('value')) {
	recycle.unittype = $("#selectRecycleUnittype").attr('value');
	}
	$("#recycleValue").change(function(){
		recycle.unittype = $("#selectRecycleUnittype").attr('value');
		recycle.value = $(this).val();
		validateRecycle();
		calculateVOC();
	});
//calculateVOC();
}

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

	function validateRecycle() {

		if(recycle.value > 100 && !recycle.unittype) {
			return false;
		} else {
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
		mixObj.setNotes($("#notes").val());

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

		if(!validateRecycle()){

			$("#recycleValidError").css("display","inline");
			return;
		} else {
			$("#recycleValidError").css("display","none");
		}

		mix = getMix();

		if(noMWS != true){
			waste = wasteStreamsCollection.toJson();
			recycle = $.toJSON(recycle);
		} else {

			waste = $.toJSON(waste);
			recycle = $.toJSON(recycle);
		}


		if(editForm == false) {
			urlData = {"action" : "addItemAjax", "category" : "mix", "departmentID": departmentID, "wasteJson" : waste, "recycleJson" : recycle, "products" : products.toJson() , "mix" : mix.toJson()};
		} else {
			urlData = {"action" : "editItemAjax", "category" : "mix", "departmentID": departmentID, "wasteJson" : waste, "recycleJson" : recycle, "products" : products.toJson() , "mix" : mix.toJson(), "id" : mixID};
		}

		$.ajax({
			url:'index.php',
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
      					} else if (res.products_error != undefined) {
      						if(currentSelectedPFP != null) {
      							//productError_453
      							$("#PrimaryProductError").css("display","block");
      						}
      					}


      				} else {
      					alert(response);
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
      		data: {"product_id":product_id},
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
      		url: "index.php",
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
	      				writeUnittype(response,'selectWasteUnittype');
	      				waste.unittype = $("#selectWasteUnittype").attr('value');
						calculateVOC();
	      			}
				});
			}
		} else if (sel.name == 'selectWasteUnittype') {

			if(sysType.length > 0){
				$.ajax({
      			url: "modules/ajax/getUnitTypes.php",
      			type: "GET",
	      		async: false,
	      		data: {"sysType":sysType,"companyID":companyID,"companyEx":companyEx},
	      		dataType: "html",
	      		success: function (response)
	      			{
	      				//writeUnittype(response,'selectWasteUnittype');
	      				waste.unittype = $("#selectWasteUnittype").attr('value');
						calculateVOC();
	      			}
				});
			}
		} else if (sel.name == 'selectRecycleUnittypeClass') {
			$("#selectRecycleUnittype").empty();
			$('#selectRecycleUnittypePreloader').css('display','block');
			if(sysType.length > 0){
				$.ajax({
      			url: "modules/ajax/getUnitTypes.php",
      			type: "GET",
	      		async: false,
	      		data: {"sysType":sysType,"companyID":companyID,"companyEx":companyEx},
	      		dataType: "html",
	      		success: function (response)
	      			{
	      				writeUnittype(response,'selectRecycleUnittype');
	      				recycle.unittype = $("#selectRecycleUnittype").attr('value');
						calculateVOC();
	      			}
				});
			}
		} else if (sel.name == 'selectRecycleUnittype') {

			if(sysType.length > 0){
				$.ajax({
      			url: "modules/ajax/getUnitTypes.php",
      			type: "GET",
	      		async: false,
	      		data: {"sysType":sysType,"companyID":companyID,"companyEx":companyEx},
	      		dataType: "html",
	      		success: function (response)
	      			{
	      				//writeUnittype(response,'selectRecycleUnittype');
	      				recycle.unittype = $("#selectRecycleUnittype").attr('value');
						calculateVOC();
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

			//console.log("WRITE UNITTYPE");
			//console.log("sysType = " + sysType);
			//console.log("companyID = " + companyID);
			//console.log("companyEx = " + companyEx);

			if(sysType.length > 0){
				$.ajax({
      			url: "modules/ajax/getUnitTypes.php",
      			type: "GET",
	      		async: false,
	      		data: {"sysType":sysType,"companyID":companyID,"companyEx":companyEx},
	      		dataType: "html",
	      		success: function (response)
	      			{
	      			//console.log("response = " + response);
	      				writeUnittype(response,"product_selectUnittype_"+productAddedIdx);
	      				//console.log("END OF WRITE UNITTYPE");
	      				productUnittype = products.getProduct(productAddedIdx).selectUnittype;

	      				selector = "#product_selectUnittype_"+productAddedIdx;

						$(selector).val(productUnittype).attr("selected",true);
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

	var currentSelectedPFP = null;
	var currentSelectedPFP_descr = null;

	function addPFPProducts(pfp_products,pfp_id,pfp_description) {
		//alert($.toJSON(pfp_products));
		//alert(products.Count());
		yes = true;

		// base product should be always on top
		pfp_products = orderPfpProducts(pfp_products);
		//console.log(pfp_products);
		if(currentSelectedPFP != null) {
			yes = confirm("Pre-formulated-products is already loaded from \""+currentSelectedPFP_descr+"\". Do you want clear products list and load products from pre-formulated-products \"" + pfp_description+"\"?");
			if(yes == true) {
				clearProductsList();
			}
		}
		else if(products.Count() > 0) {
			yes = confirm("Products are already exists. Do you want clear products list and load products from pre-formulated-products \""+pfp_description+"\"?");
			if(yes == true){
				clearProductsList();
			}
		}

		if(yes == true) {

			var selectUnittypeClass = $("#selectUnittypeClass").val();
			var selectUnittype = $("#selectUnittype").val();

			currentSelectedPFP = pfp_id;
			currentSelectedPFP_descr = pfp_description;

			for(i=0; i<pfp_products.length; i++) {
				addProduct(pfp_products[i].productID, 0, selectUnittype, selectUnittypeClass, true, pfp_products[i].isPrimary, pfp_products[i].ratio, pfp_products[i].isRange);
			}
		}

	}

	/**
	 * Base product should be always on top
	 */
	function orderPfpProducts(pfp_products) {
		var orderedProducts = [];
		var nonPrimaryIndex = 1;
		for (var i=0; i<pfp_products.length; i++) {
			if(pfp_products[i].isPrimary) {
				orderedProducts[0] = pfp_products[i];
			} else {
				orderedProducts[nonPrimaryIndex] = pfp_products[i];
				nonPrimaryIndex++;
			}
		}

		return orderedProducts;
	}

	function clearProductsList() {
		//for (i=0; i<products.Count(); i++) {
		while(products.Count() > 0){
			id = products.products[0].productID;

			$("#product_row_" + id).remove();
			products.removeProduct(id);
		}
		calculateVOC();
	}

	function addProduct(productID, quantity, unittypeId, unittypeClass,pfp, isPrimary, ratio, isRange) {


		isPFP = typeof(pfp) != 'undefined' ? true : false;


		if(isPFP == true) {

			products.addPFPProduct(productID, quantity, unittypeId, unittypeClass,ratio,isPrimary,isRange);
		} else {

			products.addProduct(productID, quantity, unittypeId, unittypeClass);
		}

		$('#addProductPreloader').css('display', 'block');
		$("#addProductsContainer").css('display','block');

		$.ajax({
      		url: "modules/ajax/saveMix.php",
      		type: "GET",
      		async: true,
      		data: {"action":"getProductInfo", "productID":productID},
      		dataType: "html",
      		success: function (r) {
      			//$('#addProductPreloader').css('display', 'none');

      			tr = $("<tr>").attr({
      				id:"product_row_"+productID
      			});

      			if(pfp == true) {
      				if(isPrimary != true) {
      					tr.css('background-color',"#D7D7D7");
      				}

      			}

      			td1 = $("<td>");



      			/*checkbox = $("<input>").attr({
      				type:"checkbox",
      				value:"" + productID
      			}
      			);

      			checkbox.attr('checked', false); */

      			checkbox = $("<input type='checkbox' value='"+productID+"' CHECKED>");


      			//dCh = checkbox.get();
      			//dCh.checked = true;
      			//checkbox = $(dCh);

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


				if(editForm == false && 1==2) {

					unittypeDescr = $("#selectUnittype option:selected").text();

					tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(quantity)));
					tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(unittypeDescr)));
					$("#addedProducts").find("tbody").append( tr );
				} else {

					txQ = $("<input>").attr("type","text").attr("id","product_" + productID + "_quantity").val(quantity).numeric();

					if(isPFP == true) {
						if(isPrimary == false) {
							txQ.attr("disabled","disabled");
							txQ.attr("isPrimary","false");
						} else {
							txQ.attr("isPrimary","true");
						}
						txQ.attr("ratio",ratio);
					}
					//txQ..attr("onchange","setProductQuantity("+productID+")")
					txQ.change( {"productID" : productID} ,function(eventObject) {
						setProductQuantity(eventObject.data.productID);
						if(currentSelectedPFP != null){
							calculateQuantityInPFPProducts(eventObject.data.productID);
						}
						calculateVOC();
					});

					tdQuantity = $("<td>").attr({"class":"border_users_r border_users_b"});
					tdQuantity.append(txQ);
					if(isPFP == true) {
						if (isRange) {
							isRangeCaption = " % from primary";
						} else {
							isRangeCaption = "";
						}
							ratioSpan = $("<span>ratio: <b>"+ratio+"</b>"+isRangeCaption+"</span>");
						tdQuantity.append(ratioSpan);

					}

					tr.append(tdQuantity);
					//tr.append($("<td>").attr({class:"border_users_r border_users_b"}).append($("<span>").text(unittypeClass)));

					//elUnittypeClass = $("#selectUnittypeClass").clone(true);

					//elUnittypeClass = $("<select>");

					elUnittypeClass = createSelectUnittypeClass("product_selectUnittypeClass_"+productID);

					//elUnittypeClass.append(selectOptions2UnitTypeClasses());

					elUnittypeClass.attr("name","product_selectUnittypeClass_"+productID);

					product = products.getProduct(productID);
					elUnittypeClass.attr('value',product.unittypeClass);



					//elUnittypeClass.attr("onchange","getUnittypes(this, "+companyId+", "+companyEx+"); setProductUnittype("+productID+"); setProductUnittypeClass("+productID+");");

					elUnittypeClass.change( {"productID" : productID} ,function(eventObject) {

						//console.log($(this).get());
						//alert($(this).attr("name"));
						getUnittypes(document.getElementById($(this).attr("name")), companyId, companyEx);
						setProductUnittype(eventObject.data.productID);
						setProductUnittypeClass(eventObject.data.productID);

						if(currentSelectedPFP != null){
							changeUnittypesInAllProducts(productID);
						}

						calculateVOC();
					});

					td = $("<td>").attr({"class":"border_users_r border_users_b"});
					td.append(elUnittypeClass);



					//elUnittypeId = $("#selectUnittype").clone(true);
					elUnittypeId = $("<select>");
					id = 'product_selectUnittype_'+productID;


					elUnittypeId.attr('id',id).attr('name',id);
					//elUnittypeId.attr("onchange","setProductUnittype("+productID+")");
					elUnittypeId.change({"productID" : productID}, function(eventObject){
						setProductUnittype(eventObject.data.productID);

						if(currentSelectedPFP != null){
							changeUnittypesInAllProducts(productID);
						}

						calculateVOC();
					});

					if(isPFP == true && isPrimary != true) {
						elUnittypeClass.css("display",'none');
						elUnittypeId.css("display",'none');
					}
					//$(id + " option[value="+unittypeId+"]").attr("SELECTED",true).attr('ololo','trololo');


					td.append(elUnittypeId);
					//<div class="error_img"  id="mixDescriptionErrorAlreadyInUse" style="display:none;"><span class="error_text" >Entered name is already in use!</span></div>

					if(isPFP == false) {
						td.append("<div class='error_img error_text'  id='productError_"+productID+"' style='display:none;'>Failed to convert weight unit to volume because product density is underfined! You can set density for this product or use volume units.</span></div>")
					}
					else {
						td.append("<div class='error_img error_text'  id='PrimaryProductError' style='display:none;'>Failed to convert weight unit to volume because products density is underfined! You can set density for this product or use volume units.</span></div>")
					}

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

				calculateVOC();

      		}
		});
	}

	function changeUnittypesInAllProducts(productID) {

		primaryProduct = products.getProduct(productID);

		for(i=0; i<products.Count(); i++) {
			if(products.products[i].productID != productID) {

				products.products[i].selectUnittype = primaryProduct.selectUnittype;
				products.products[i].unittypeClass = primaryProduct.unittypeClass;
			}
		}
	}

	function calculateQuantityInPFPProducts(productID) {
		//alert(products.toJson());
		primaryProduct = products.getProduct(productID);
		//alert($.toJSON(primaryProduct));

		if(primaryProduct.ratio > 0) {
			delitel = primaryProduct.ratio;
		} else {
			delitel = 1;
		}

		quantity = products.getProduct(productID).quantity;

		for(i=0; i<products.Count(); i++) {
			if(products.products[i].productID != productID) {
				if (products.products[i].isRange) {
					pr_ratio = products.products[i].ratio*primaryProduct.ratio/100;
				} else {
					pr_ratio = products.products[i].ratio;
				}
				q_tmp = (pr_ratio / delitel) * quantity;
				pr_id = products.products[i].productID;
				q_tmp = q_tmp.toFixed(2);
				//alert("apply quantity "+q_tmp+" to productID #"+pr_id);
				products.products[i].quantity = q_tmp;
				$("#product_"+pr_id+"_quantity").attr("value",q_tmp);
			}
		}
	}

	function WasteStreams4CalcVoc(waste) {
		if (waste !== '[]'){
			var quantity = 0;
			var allweight = [];
			var allvolume = [1,4,8,9,13,14,15,16,17,18,24,25,26,27,28,30,31,32];
			var typeIDarr = [];var allquan = [];var alltype = [];
			obj2 = jQuery.parseJSON(waste);
			str = object2String(obj2);
			arr = string2Array(str);



			n = 0;
			i = 0;
			while (arr[n]) {
				if (arr[n].pollutions !== undefined) {
					m = 0;
					while (arr[n].pollutions[m]) {
						if (arr[n].pollutions[m].quantity){
							quantity += parseFloat(arr[n].pollutions[m].quantity);
						}
						typeIDarr.push(arr[n].pollutions[m].unittypeId);
						allquan[i] = parseFloat(arr[n].pollutions[m].quantity);
						alltype[i] = arr[n].pollutions[m].unittypeId;
						m ++;i = i + m;
					}

					//alert(arr[n].pollutions);
				}else{
					if (arr[n].quantity){
						quantity += parseFloat(arr[n].quantity);
					}
					typeIDarr.push(arr[n].unittypeId);

						allquan[i] = parseFloat(arr[n].quantity);
						alltype[i] = arr[n].unittypeId;
						i ++;
				}

				n ++;

			}
		FlaginVolume = 0;


		for (var i = 0; i < alltype.length; i++) {
			for (var j = 0; j < allvolume.length; j++) {
				if (alltype[i] == allvolume[j] ){
					FlaginVolume ++;
				}
			}
		}

		if (FlaginVolume != alltype.length && FlaginVolume != 0){

			return ;
		}


		if (FlaginVolume == 0){
			ut = 2;
			convertWaste = WasteConverter(allquan,alltype,ut);
		}else{
			ut = 1;
			convertWaste = WasteConverter(allquan,alltype,ut);
		}

		//console.log('result:'+convertWaste);
		//console.log(allquan.length);
		//console.log(alltype.length);
		var wasteJSON = {"value": convertWaste, "unittype": ut};
		return wasteJSON;

		}else{
			return;
		}

}

	function WasteConverter(allquan,alltype,ut) {
		var convertWastes=0;

		if (ut == 1){
			for (var i = 0; i < alltype.length; i++) {
				coeff = chooseVolumeCoefficient(parseFloat(alltype[i]));

				convertWastes = convertWastes + allquan[i]*coeff;
			}
		}else{
			for (var i = 0; i < alltype.length; i++) {
				coeff = chooseWeightCoefficient(parseFloat(alltype[i]));

				convertWastes = convertWastes + allquan[i]*coeff;
			}
		}

	return convertWastes;
}

	function chooseWeightCoefficient(tipid) {

		switch (tipid) {
	/*	case 2:
			coef = 1;
			return coef;
			break*/
		case 3:
			coef = 2.206999;
			return coef;
			break

		case 5:
			coef = 2206.999205;
			return coef;
			break
		case 7:
			coef = 0.0625;
			return coef;
			break
		case 10:
			coef = 0.000002207;
			return coef;
			break

		case 11:
			coef = 0.002207;
			return coef;
			break
		case 12:
			coef = 0.000143;
			return coef;
			break
		case 20:
			coef = 100;
			return coef;
			break

		case 22:
			coef = 0.003906;
			return coef;
			break
		case 23:
			coef = 0.2205;
			return coef;
			break
		case 33:
			coef = 112;
			return coef;
			break

		default:

			return 1;

		}


}

	function chooseVolumeCoefficient(tipid) {

		switch (tipid) {
	/*	case 1:
			coef = 1;
			return coef;
			break*/
		case 4:
			coef = 0.264172052;
			return coef;
			break

		case 8:
			coef = 1.200949926;
			return coef;
			break
		case 9:
			coef = 0.000264172;
			return coef;
			break
		case 13:
			coef = 1.164;
			return coef;
			break

		case 14:
			coef = 0.007505937;
			return coef;
			break
		case 15:
			coef = 0.0078125;
			break
		case 16:
			coef = 0.125;
			return coef;
			break

		case 17:
			coef = 0.25;
			return coef;
			break
		case 18:
			coef = 42;
			return coef;
			break
		case 24:
			coef = 0.00264172;
			return coef;
			break

		case 25:
			coef = 0.02641721;
			return coef;
			break
		case 26:
			coef = 2.641721;
			return coef;
			break
		case 27:
			coef = 26.4172;
			return coef;
			break

		case 28:
			coef = 264.1721;
			return coef;
			break
		case 30:
			coef = 9.309177;
			return coef;
			break
		case 31:
			coef = 0.000264;
			return coef;
			break

		case 32:
			coef = 9.607619;
			return coef;
			break

		default:

			return 1;

		}


}


	function calculateVOC() {
		mix = getMix();

		if(noMWS != true){
			waste = wasteStreamsCollection.toJson();

			//	waste streams to normal view for auoto calc voc
			answer = WasteStreams4CalcVoc(waste);

			if (answer == '') {
				return;
			}else{
				waste = answer;

			}
		}


		$.ajax({
      		url: "index.php",
      		type: "GET",
      		async: true,
      		data: {"action" : "calculateVOCAjax", "category" : "mix", "departmentID": departmentID, "products" : products.toJson() , "mix" : mix.toJson() , "wasteJson" : waste, "recycleJson" : recycle},
      		dataType: "html",
      		success: function (r) {

      			var resp=eval("("+r+")");



      			$("#VOC").html(resp.currentUsage);



      			if(resp.dailyLimitExcess == true) {
      				$("#dailyLimitExceeded").html("<b>YES!</b>");
      			} else {
      				$("#dailyLimitExceeded").html("no");
      			}

      			if(resp.departmentLimitExceeded == true) {
      				$("#departmentLimitExceeded").html("<b>YES!</b>");
      			} else {
      				$("#departmentLimitExceeded").html("no");
      			}

      			if(resp.facilityLimitExceeded == true) {
      				$("#facilityLimitExceeded").html("<b>YES!</b>");
      			} else {
      				$("#facilityLimitExceeded").html("no");
      			}

      			if(resp.facilityAnnualLimitExceeded == true) {
      				$("#facilityAnnualLimitExceeded").html("<b>YES!</b>");
      			} else {
      				$("#facilityAnnualLimitExceeded").html("no");
      			}

      			if(resp.departmentAnnualLimitExceeded == true) {
      				$("#departmentAnnualLimitExceeded").html("<b>YES!</b>");
      			} else {
      				$("#departmentAnnualLimitExceeded").html("no");
      			}
      		}
		});
	}

	function string2Array(string) {
		eval("var result = " + string);
		return result;
	}
	function object2String(obj) {
		var val, output = "";
		if (obj) {
			output += "{";
			for (var i in obj) {
				val = obj[i];
				switch (typeof val) {
					case ("object"):
						if (val[0]) {
							output += i + ":" + array2String(val) + ",";
						} else {
							output += i + ":" + object2String(val) + ",";
						}
						break;
					case ("string"):
						output += i + ":'" + escape(val) + "',";
						break;
					default:
						output += i + ":" + val + ",";
				}
			}
			output = output.substring(0, output.length-1) + "}";
		}
		return output;
	}

	function array2String(array) {
    var output = "";
    if (array) {
        output += "[";
        for (var i in array) {
            val = array[i];
            switch (typeof val) {
                case ("object"):
                    if (val[0]) {
                        output += array2String(val) + ",";
                    } else {
                        output += object2String(val) + ",";
                    }
                    break;
                case ("string"):
                    output += "'" + escape(val) + "',";
                    break;
                default:
                    output += val + ",";
            }
        }
        output = output.substring(0, output.length-1) + "]";
    }
    return output;
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

		if(currentSelectedPFP != null) {
			yes = confirm("Pre-formulated-products is already loaded from \""+currentSelectedPFP_descr+"\". Do you want clear products list and add single product?");
			if(yes == true) {
				clearProductsList();
				currentSelectedPFP = null;
				currentSelectedPFP_descr = null;
			}
			else {
				return;
			}
		}

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


		if(typeof $.browser.msie != "undefined" && $.browser.msie == true) {

			nextEl = selectedOption.next("option");//.attr('selected', 'selected');
			nextEl.attr('selected', 'selected');

		}



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
				$("#selectProduct option[value='"+id+"']").removeAttr('disabled');
			}

		});

		for ( keyVar in rowsToRemove ) {
			id = rowsToRemove[keyVar];
			$("#product_row_" + id).remove();
			products.removeProduct(id);
		}
		calculateVOC();
		//alert(products.toJson());
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