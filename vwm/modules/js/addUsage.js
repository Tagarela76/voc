
// OOP VERSION

function AddMixPage() {
	this.pfpManager = new PfpManager();
	this.utils = new Utils();
}

function PfpType(id) {
	this.id = id;
	this.name = 'all';
	this.facility_id = 0;
}

function PfpList(type, pfps) {
	this.type = type;
	this.pfps = pfps;
}

function PfpDetails() {
	this.pfpId;
	this.pfp;
}

function Pfp() {
	this.id;
	this.description;
	this.company_id;
	this.type_id;	
}


function PfpManager() {
	this.defaultPfpType = new PfpType(0);
	this.pfpLists = [];	
	this.currentPfpType;
	this.pfpDetails = [];
	
	this.getPfpsByType = function(pfpType) {
		var that = this;
		var returnPfpList = [];
		
		// check probably list is already loaded
		
		for(key in this.pfpLists) {			
			if(this.pfpLists[key].type.id == pfpType.id) {
				// we found it				
				return this.pfpLists[key].pfps;
			}
		}
		$.ajax({
			url: "?action=loadBriefPfps&category=pfpTypes",
			async: false,
			data: {pfpTypeId: this.currentPfpType.id},
			type: "GET",
			dataType: "json",
			success: function (pfpList) {				
				that.pfpLists.push(pfpList);		
				returnPfpList = pfpList.pfps;				
      		}
		});
		
		return returnPfpList;
	}
	
	
	this.renderPfpList = function() {
		var pfps = this.getPfpsByType(this.currentPfpType);
		
		var html = '';
		for (key in pfps) {
			html += '<tr id="'+page.utils.escapeHTML(pfps[key].id)+'" name="pfp_row">';
			html +=		'<td colspan="4">'+page.utils.escapeHTML(pfps[key].description)+'</td>';
			html += '</tr>';
			html += '<tr id="'+page.utils.escapeHTML(pfps[key].id)+'_details" name="pfp_details" style="display:none;">';
			html +=		'<td colspan="4" style="text-align:center;">';
			html +=			'<img src="images/ajax-loader.gif" class="preloader" />';
			html +=		'</td>';
			html +=	'</tr>';                                   
		}						
		
		// update table
		$('.pfpList tbody').html(html);	
		
		this.enableOnClickListener();
	}
	
	this.openPfpGroup = function(pfpGroupId, linkElement) {
		
		this.currentPfpType = {
			'id':pfpGroupId,
			'name':'',
			'facility_id':0 
		};
		
		this.renderPfpList();
		
		// reset active links
		$('.active_link').removeClass('active_link');
		
		// activate new one
		$(linkElement).addClass('active_link');
	}
	
	//TODO: needs rewrite
	this.enableOnClickListener = function() {
		var that = this;
		$("tr[name='pfp_row']").click(function(e){
			$("tr[name='pfp_details']").hide();
			$("tr[name='pfp_row']").attr('class','');

			$("table[name='pfp_details_products']").remove();
			var id = $(this).attr('id');
			
			if(typeof $.browser.msie != "undefined" && $.browser.msie == true) {
				$("#"+id+"_details").css("display","block");
			} else {
				$("#"+id+"_details").css("display","table-row");
			}

			$("#"+id+"_details .preloader").css("display","block");
			loadPFPDetails(id);
			//	this is not ready yet
			//this.renderPfpList(id);			
		});
	}
	
	
	this.getPpf = function(id) {
		// check probably list is already loaded
		for(key in this.pfpDetails) {			
			if(this.pfpDetails[key].pfpId == id) {
				// we found it				
				return this.pfpDetails[key].pfp;
			}
		}		
		var pfp;
		var that = this;
		
		var urlData = {
			"action" : "getPFPDetailsAjax", 
			"category" : "mix", 
			"departmentID": departmentID, 
			"pfp_id" : id
		};
		$.ajax({
			url:'index.php',
			type: "GET",
			async: false,
			data: urlData,
			dataType: "json",
			success: function (response) {
				that.pfpDetails.push({
					"id":response.id,
					"pfp":response.pfp	
				});
				pfp = response.pfp;  				
  			}					
		});
				
		return pfp;
	}
	
	this.renderPfpDetails = function(id) {
		var pfp = that.getPpf(id);					
		$("#"+pfp.id+"_details .preloader").css("display","none");
  		$("#"+pfp.id).attr('class','pfpListItemSelected');
	}
}

// END OF OOP VERSION


$(document).ready(function() {	

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
  				$("#"+pfp_id+"_details .preloader").css("display","none").after(response);
  				$("#"+pfp_id).attr('class','pfpListItemSelected');
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
		mixObj.setSpentTime($("#spentTime").val());

		mixObj.setAPMethod($("#selectAPMethod option:selected").val());
		mixObj.setEquipment($("#selectEquipment option:selected").val());
		mixObj.setRule($("#rule option:selected").val());
		mixObj.selectUnittypeClass = $("#selectUnittypeClass option:selected").val();
		mixObj.setNotes($("#notes").val());

		mixObj.setIteration($("#repairOrderIteration").val());
		mixObj.setParentID($("#mixParentID").val());
		mixObj.setStepId($("#StepID").val());
		if ($("#repairOrderId").val() != '') {
			mixObj.setRepairOrderId($("#repairOrderId").val());
		}

		return mixObj;
	}



	function addMix() {
		if(mixValidator.isValid() != true ) {
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
          				    	 $("<span name='storageOverflowError' style='color:Red;'>"+res.storageError+"</span>").insertAfter($(this));
          				      }
          				   });
      					} else if (res.products_error != undefined) {
      						if(currentSelectedPFP != null) {
      							$("#PrimaryProductError").css("display","block");
      						}
      					}


      				} else {
      					alert(response);
      				}
      			}

		});

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

		      			$('#product_desc').text(resp['description']);
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
		yes = true;

		// base product should be always on top
		pfp_products = orderPfpProducts(pfp_products);

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
      		data: {"action":"getProductInfo", "productID":productID, "isPrimary":isPrimary},
      		dataType: "html",
      		success: function (r) {

                  tr = $("<tr>").attr({
      				id:"product_row_"+productID
      			});

      			td1 = $("<td>");

      			checkbox = $("<input type='checkbox' value='"+productID+"' CHECKED>");

      			td1.attr({
      				"class":"border_users_r border_users_b border_users_l"
      			});

      			td1.append(checkbox);

      			tr.append(td1);



      			var resp=eval("("+r+")");
                resp.isPrimary = (resp.isPrimary == 1 || resp.isPrimary == "true" || resp.isPrimary == true) ? true : false;
  				var supplier 	= resp['supplier_id'];
  				var productNR 	= resp['product_nr'];
  				var descr 		= resp['name'];

  				tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(supplier)));
				tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(productNR)));
				tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(descr)));

                if(pfp == true) {
                      if(resp.isPrimary != true) {
                          tr.css('background-color',"#D7D7D7");
                      }

                }


				if(editForm == false && 1==2) {

					unittypeDescr = $("#selectUnittype option:selected").text();

					tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(quantity)));
					tr.append($("<td>").attr({"class":"border_users_r border_users_b"}).append($("<span>").text(unittypeDescr)));
					$("#addedProducts").find("tbody").append( tr );
				} else {

					txQ = $("<input>").attr("type","text").attr("id","product_" + productID + "_quantity").val(quantity).numeric();

					if(isPFP == true) {
						if(resp.isPrimary == false) {
							txQ.attr("disabled","disabled");
							txQ.attr("isPrimary","false");
						} else {
							txQ.attr("isPrimary","true");
						}
						txQ.attr("ratio",ratio);
					}

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

					elUnittypeClass = createSelectUnittypeClass("product_selectUnittypeClass_"+productID);

					elUnittypeClass.attr("name","product_selectUnittypeClass_"+productID);

					product = products.getProduct(productID);
					elUnittypeClass.attr('value',product.unittypeClass);

					elUnittypeClass.change( {"productID" : productID} ,function(eventObject) {

						getUnittypes(document.getElementById($(this).attr("name")), departmentId, companyEx);
						setProductUnittype(eventObject.data.productID);
						setProductUnittypeClass(eventObject.data.productID);

						if(currentSelectedPFP != null){
							changeUnittypesInAllProducts(productID);
						}

						calculateVOC();
					});

					td = $("<td>").attr({"class":"border_users_r border_users_b"});
					td.append(elUnittypeClass);

					elUnittypeId = $("<select>");
					id = 'product_selectUnittype_'+productID;


					elUnittypeId.attr('id',id).attr('name',id);
					elUnittypeId.change({"productID" : productID}, function(eventObject){
						setProductUnittype(eventObject.data.productID);

						if(currentSelectedPFP != null){
							changeUnittypesInAllProducts(productID);
						}

						calculateVOC();
					});

					if(isPFP == true && resp.isPrimary != true) {
						elUnittypeClass.css("display",'none');
						elUnittypeId.css("display",'none');
					}

					td.append(elUnittypeId);

					if(isPFP == false) {
						td.append("<div class='error_img error_text'  id='productError_"+productID+"' style='display:none;'>Failed to convert weight unit to volume because product density is underfined! You can set density for this product or use volume units.</span></div>")
					}
					else {
						td.append("<div class='error_img error_text'  id='PrimaryProductError' style='display:none;'>Failed to convert weight unit to volume because products density is underfined! You can set density for this product or use volume units.</span></div>")
					}

					tr.append(td);

                    if(isPFP == true && resp.isPrimary != true) {
                        $("#addedProducts").find("tbody").append( tr );
                    } else {
                        $("#addedProducts").find("tbody").prepend( tr );
                    }


					getUnittypes(document.getElementById(elUnittypeClass.attr('id')), departmentId, companyEx);


				}

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

		primaryProduct = products.getProduct(productID);

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
      				$("#dailyLimitExceeded").html("<b style=\"color: red;\">YES!</b>");
      			} else {
      				$("#dailyLimitExceeded").html("no");
      			}

      			if(resp.departmentLimitExceeded == true) {
      				$("#departmentLimitExceeded").html("<b style=\"color: red;\">YES!</b>");
      			} else {
      				$("#departmentLimitExceeded").html("no");
      			}

      			if(resp.facilityLimitExceeded == true) {
      				$("#facilityLimitExceeded").html("<b style=\"color: red;\">YES!</b>");
      			} else {
      				$("#facilityLimitExceeded").html("no");
      			}

      			if(resp.facilityAnnualLimitExceeded == true) {
      				$("#facilityAnnualLimitExceeded").html("<b style=\"color: red;\">YES!</b>");
      			} else {
      				$("#facilityAnnualLimitExceeded").html("no");
      			}

      			if(resp.departmentAnnualLimitExceeded == true) {
      				$("#departmentAnnualLimitExceeded").html("<b style=\"color: red;\">YES!</b>");
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
	
	
	function is_null(mixed_var){ 
    return ( mixed_var === 'undefined' );
	}
