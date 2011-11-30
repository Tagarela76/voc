//86400000 - 1 day in milliseconds
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
		}
		
		function addSupplierToSup() {
			var checkBoxes = document.getElementById('supplierListPP').getElementsByTagName('input');
			
			//	clear old data from parent
			var typesClassString = document.getElementById('supplierList');
			typesClassString.innerHTML = "";			
			
			var hiddenTypesClasses = document.getElementById('hiddenSuppliers');
			if (hiddenTypesClasses.hasChildNodes()) {
    			while ( hiddenTypesClasses.childNodes.length > 0 ) {
        			hiddenTypesClasses.removeChild(hiddenTypesClasses.firstChild);       
    			} 
			}
			
			for (i = 0; i < checkBoxes.length; i++) {
				if (checkBoxes[i].type == 'checkbox' && checkBoxes[i].checked == true) {					
					var index = checkBoxes[i].value;										
					typesClassString.innerHTML += document.getElementById('category_'+index).innerHTML + "; ";
					var hiddenTypesClassID =  document.createElement("input");
					hiddenTypesClassID.type = "hidden";
					hiddenTypesClassID.name = 'supplier_'+i;
					hiddenTypesClassID.value = checkBoxes[i].value;
					hiddenTypesClasses.appendChild(hiddenTypesClassID);
				}
			}
			
			//	hide popup
			$("#supplierPopup").dialog('close');	
		}
				
		$(function() {
			$("#supplierPopup").dialog({
					width: 800,
					height:500,
					autoOpen: false,
					resizable: true,
					dragable: true,			
					modal: true,
					buttons: {				
						'Cancel': function() {					
							$(this).dialog('close');
						},
						'Select': function() {
							addSupplierToSup();
						}
					}	
				});
		});