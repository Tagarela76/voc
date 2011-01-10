	
	$(function()
	{
		/*if(get_cookie('incSearch')=='true')
		{
			$('#AddRemoveSearch').attr('value','Remove Product Search');
			$('#selectProduct').AddIncSearch({
			        maxListSize   : 20,
			        maxMultiMatch : 70
		   		});
		}
		
		$('#AddRemoveSearch').click(function()
		{
			if($(this).attr('value')=='Enable Product Search')
			{				
				$(this).attr('value','Disable Product Search');				
				$('#selectProduct').AddIncSearch({
			        maxListSize   : 20,
			        maxMultiMatch : 70
		   		});
		   		set_cookie ('incSearch', 'true',30);		   		
			}
			else
			{				
				$(this).attr('value','Enable Product Search');
				$('#selectProduct').RemoveIncSearch();
				set_cookie ('incSearch', 'false',30);
			}
		});*/
		
		getProductInfo();
		$('#selectProduct').change(function(el)
		{
			getProductInfo();
		});
	})	
	
	/*//86400000 - 1 day in milliseconds
	function set_cookie(name, value, expires, path, domain, secure) {  
	    //define expires time  
	    var today = new Date();  
	    var expires_date = new Date(today.getTime() + (expires * 86400000));  
	  
	    //set cookie  
	    document.cookie =  
	            name + '=' + escape(value) +  
	            (expires ? ';expires=' + expires_date.toUTCString() : '') +  
	            (path    ? ';path=' + path : '' ) +  
	            (domain  ? ';domain=' + domain : '' ) +  
	            (secure  ? ';secure' : '' );  
	}  
	
	function get_cookie ( cookie_name )
	{
		  var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
		 
		  if ( results )
		    return ( unescape ( results[2] ) );
		  else
		    return null;
	}*/
		
				
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
						$('#product_desc').attr('value',resp['description']);
						$('#coating').attr('value',resp['coatName']);												
      				}
      				$('#product_descPreloader').css('display','none');
					$('#coatingPreloader').css('display','none');										
      			}        		   			   	
			});
		}
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
	
	
		
	function addProduct2List() {			
		
		var productID = $("select#selectProduct option:selected").val();
		var quantity = $("#quantity").val();
		var selectUnittypeClass = $("#selectUnittypeClass").val();
		var selectUnittype = $("#selectUnittype").val();		
		
		var unittypeClassSelectBox = $('#selectUnittypeClass').clone();
		var unittypeSelectBox = $('#selectUnittype').clone();
		
		$('#addProductPreloader').css('display', 'block');						
		$.ajax({
      		url: "modules/ajax/saveMix.php",      		
      		type: "GET",
      		data: { "action":"addProduct", "productID":productID, "quantity":quantity, "unittype":selectUnittype},      			
      		dataType: "json",
      		success: function (r) {
      			$('#addProductPreloader').css('display', 'none');
      			
      			if (r.validStatus.summary == 'true') {
      				//	add product
      				
      				// 	remove product from dropdown
      				$("#selectProduct option[value='"+productID+"']").remove();
      				
      				//	insert one more row
      				$("#addedProducts tbody").append( $("#dock").val() ) ;
      				$("#addedProducts tbody tr:last input[name='product[]']").val(r.product.product_id);
      				$("#addedProducts tbody tr:last div[name='supplier']").val(r.product.supplier);
      				$("#addedProducts tbody tr:last div[name='productNR']").val(r.product.product_nr);
      				$("#addedProducts tbody tr:last div[name='description']").val(r.product.description);
      				$("#addedProducts tbody tr:last input[name='quantityOfAddedProduct']").val(r.product.quantity);
      				
      				//	copy unittype selectbox
      				unittypeClassSelectBox.appendTo("#addedProducts tbody tr:last div[name='unittypeOfAddedProduct']");
      				$("#addedProducts tbody tr:last #selectUnittypeClass").attr('id', 'selectUnittypeClass_'+productID);
      				$("#selectUnittypeClass_"+productID).attr('name', 'selectUnittypeClass_'+productID);
     				$("#selectUnittypeClass_"+productID+" option[value='"+selectUnittypeClass+"']").attr('selected', 'selected');

      				//	copy unittype selectbox
      				unittypeSelectBox.appendTo("#addedProducts tbody tr:last div[name='unittypeOfAddedProduct']");
      				$("#addedProducts tbody tr:last #selectUnittype").attr('id', 'unittype_'+productID);
      				$("#unittype_"+productID).attr('name', 'unittype_'+productID);
      				$("#unittype_"+productID+" option[value='"+selectUnittype+"']").attr('selected', 'selected');
      				
      			} else {
      				//	shit, we have a problems
      				$('#notify').html( generateNotify("There errors on form", "red") );
      				
      				//	up to top
    				$( 'html, body' ).animate( { scrollTop: 0 }, 0 );
    				
    				console.log(r.validStatus);
      			}      			      																	
      		}      			   	
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