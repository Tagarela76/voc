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
		
		function addTypesClasses() {
			var checkBoxes = document.getElementById('typesClassList').getElementsByTagName('input');
			
			//	clear old data from parent
			var typesClassString = document.getElementById('typesClassString');
			typesClassString.innerHTML = "";			
			
			var hiddenTypesClasses = document.getElementById('hiddenTypesClasses');
			if (hiddenTypesClasses.hasChildNodes()) {
    			while ( hiddenTypesClasses.childNodes.length > 0 ) {
        			hiddenTypesClasses.removeChild(hiddenTypesClasses.firstChild);       
    			} 
			}
			
			for (i = 0; i < checkBoxes.length; i++) {
				if (checkBoxes[i].type == 'checkbox' && checkBoxes[i].checked == true) {
					console.log(checkBoxes[i]);
					var index = checkBoxes[i].value - 1;
					//typesClassString.innerHTML += document.getElementById('typesClassName_'+index).innerHTML + "; ";
					var hiddenTypesClassID =  document.createElement("input");
					hiddenTypesClassID.type = "hidden";
					hiddenTypesClassID.name = 'typesClass_'+i;
					hiddenTypesClassID.value = checkBoxes[i].value;
					hiddenTypesClasses.appendChild(hiddenTypesClassID);
				}
			}
			
			//	hide popup
			$("#industryTypesPopup").dialog('close');	
		}
				
		$(function() {
			$("#industryTypesPopup").dialog({
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
							addTypesClasses();
						}
					}	
				});
		});