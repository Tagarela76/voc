/** Mix Validator */

function CMixValidator () {
	
	this.mixValid = false;
	this.wasteVaLid = true;
	this.productVaLid = true;
	
	this.isValid = function() {
		
		mixDescr = $("#mixDescription").val();
		
		
		
		if(editForm) {
			
			
			
			if(mixDescr != "") {
				//mixDescription is defined in addUsageNew.tpl when form is edit
				if(mixDescription != mixDescr) {
					isMixDescriptionUnique(mixDescr);
				} else {
					this.mixValid = true;
				}
				
				
				return this.mixValid && this.wasteVaLid && this.productVaLid;
			}
			else {
				$("#mixDescriptionError").css("display","block");
				return false;
			}
			
		} else {
			
			
			
			if(mixDescr == "") {
				$("#mixDescriptionError").css("display","block");
				this.mixValid = false;
			}
			else {
				isMixDescriptionUnique(mixDescr);
			}
			
		}
		
		/*Check DATE*/
		if($("#calendar1").val() == "") {
			this.mixValid = false;
			$("#creationTimeError").css("display","block");
		}
		else if(! validateUSDate($("#calendar1").val()) ) {
			this.mixValid = false;
			$("#creationTimeError").css("display","block");
		} else {
			$("#creationTimeError").css("display","none");
		}
		if(this.mixValid) {
			$("#mixDescriptionError").css("display","none");
		}
		
		return this.mixValid && this.wasteVaLid && this.productVaLid;
		
	}
}

/*
 * Founded at http://rgagnon.com/jsdetails/js-0063.html
 * */
function validateUSDate( strValue ) {
	/************************************************
	DESCRIPTION: Validates that a string contains only
	    valid dates with 2 digit month, 2 digit day,
	    4 digit year. Date separator can be ., -, or /.
	    Uses combination of regular expressions and
	    string parsing to validate date.
	    Ex. mm/dd/yyyy or mm-dd-yyyy or mm.dd.yyyy

	PARAMETERS:
	   strValue - String to be tested for validity

	RETURNS:
	   True if valid, otherwise false.

	REMARKS:
	   Avoids some of the limitations of the Date.parse()
	   method such as the date separator character.
	*************************************************/
	  var objRegExp = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/
	 
	  //check to see if in correct format
	  if(!objRegExp.test(strValue))
	    return false; //doesn't match pattern, bad date
	  else{
	    var strSeparator = strValue.substring(2,3) 
	    var arrayDate = strValue.split(strSeparator); 
	    //create a lookup for months not equal to Feb.
	    var arrayLookup = { '01' : 31,'03' : 31, 
	                        '04' : 30,'05' : 31,
	                        '06' : 30,'07' : 31,
	                        '08' : 31,'09' : 30,
	                        '10' : 31,'11' : 30,'12' : 31}
	    var intDay = parseInt(arrayDate[1],10); 

	    //check if month value and day value agree
	    if(arrayLookup[arrayDate[0]] != null) {
	      if(intDay <= arrayLookup[arrayDate[0]] && intDay != 0)
	        return true; //found in lookup table, good date
	    }
	    
	    //check for February (bugfix 20050322)
	    //bugfix  for parseInt kevin
	    //bugfix  biss year  O.Jp Voutat
	    var intMonth = parseInt(arrayDate[0],10);
	    if (intMonth == 2) { 
	       var intYear = parseInt(arrayDate[2]);
	       if (intDay > 0 && intDay < 29) {
	           return true;
	       }
	       else if (intDay == 29) {
	         if ((intYear % 4 == 0) && (intYear % 100 != 0) || 
	             (intYear % 400 == 0)) {
	              // year div by 4 and ((not div by 100) or div by 400) ->ok
	             return true;
	         }   
	       }
	    }
	  }  
	  return false; //any other values, bad date
	}