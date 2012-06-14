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

		function addChemicalClasses() {
			var checkBoxes = document.getElementById('chemClassList').getElementsByTagName('input');

			//	clear old data from parent
			var chemicalClassString = document.getElementById('chemicalClassString');
			chemicalClassString.innerHTML = "";

			var hiddenChemicalClasses = document.getElementById('hiddenChemicalClasses');
			if (hiddenChemicalClasses.hasChildNodes()) {
    			while ( hiddenChemicalClasses.childNodes.length > 0 ) {
        			hiddenChemicalClasses.removeChild(hiddenChemicalClasses.firstChild);
    			}
			}


			for (i = 0; i < checkBoxes.length; i++) {
				if (checkBoxes[i].type == 'checkbox' && checkBoxes[i].checked == true) {
					var index = checkBoxes[i].value -1 ;
					chemicalClassString.innerHTML += document.getElementById('chemicalClassName_'+index).innerHTML + "; ";
					var hiddenChemicalClassID =  document.createElement("input");
					hiddenChemicalClassID.type = "hidden";
					hiddenChemicalClassID.name = 'chemicalClass_'+index;
					hiddenChemicalClassID.value = checkBoxes[i].value;
					hiddenChemicalClasses.appendChild(hiddenChemicalClassID);
				}
			}

			//	hide popup
			$("#hazardousPopup").dialog('close');
		}

		function addRuleSelector(index) {
			var checkBox = document.getElementById('checkBox_'+index);
			var rules = document.getElementById("rules_"+index);

			if ( checkBox.checked == true) {
				var ruleCount = document.getElementById('rulesCount_'+index);
				if (ruleCount == null) {
					rules.innerHTML = "";
					var ruleCountInput = document.createElement("input");
					ruleCountInput.type = "hidden";
					ruleCountInput.name = 'rulesCount_'+index;
					ruleCountInput.id = ruleCountInput.name;
					ruleCountInput.value = 1;
					rules.appendChild(ruleCountInput);
					var buttonDeleteLast = document.createElement("input");
					buttonDeleteLast.type = "button";
					buttonDeleteLast.setAttribute('onClick',"deleteLastRuleSelector("+index+");");
					buttonDeleteLast.value = "-";
					rules.appendChild(buttonDeleteLast);
					var buttonAddMore = document.createElement("input");
					buttonAddMore.type = "button";
					buttonAddMore.setAttribute('onClick',"addRuleSelector("+index+");");
					buttonAddMore.value = "+";
					rules.appendChild(buttonAddMore);
					var ruleIndex = 0;
				} else {
					ruleIndex = ruleCount.value;
					ruleCount.value++;
				}
				var defaultRule = document.getElementById("chemicalRule");
				var newSelect = document.createElement("select");
				newSelect.name = "chemicalRule_"+index+"_"+ruleIndex;
				newSelect.id = newSelect.name;
				for (i = 0; i < defaultRule.options.length; i++) {
					newSelect.options[newSelect.options.length] = new Option(defaultRule.options[i].text,defaultRule.options[i].value,false,false);
				}
				rules.appendChild(newSelect);
			} else {
				rules.innerHTML = "";
			}
		}

		function deleteLastRuleSelector(index) {
			var ruleCount = document.getElementById('rulesCount_'+index).value;
			if (ruleCount > 1) {
				var toRemove = document.getElementById("chemicalRule_"+index+"_"+(ruleCount-1));
				var parentDiv = document.getElementById("rules_"+index);
				parentDiv.removeChild(toRemove);
				document.getElementById('rulesCount_'+index).value--;
			}
		}

		$(function() {
			$("#hazardousPopup").dialog({
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
							addChemicalClasses();
						}
					}
				});
		});