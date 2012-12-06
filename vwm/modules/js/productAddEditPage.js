function ManageProductLibraryTypes(){
	this.divId = 'manageProductLibraryTypesContainer';
	this.isLoaded = false; 
    
	this.openDialog = function() {
		$('#'+this.divId).dialog('open');
		if(!this.isLoaded) {
			this.loadContent();
		}
		return false;
	}
    
	this.loadContent = function() {
		var that = this;
		$.ajax({
			url: "?action=loadProductLibraryTypes&category=product",
			data: {
				productId: page.productId
			},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				
				// do not perform any AJAX queries from now
				that.isLoaded = true;
			}
		});
	}
	this.iniDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId != this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 500,
			height: 500,
			autoOpen: false,
			resizable: true,
			dragable: true,
			modal: true,
			buttons: {
				'Cancel': function() {
					$(this).dialog('close');
					that.isLoaded = false;					
				},
				'Save': function() {
					that.save();
				}
			}
		});
	}
    
	this.save = function() {
		var countLibraryTypes = $('#countLibraryTypes').val();
		var productLibraryTypesIds =new Array();
		for(var i = 0; i<countLibraryTypes; i++){
			if ($('#checkBox_'+i).is(":checked")){
				productLibraryTypesIds.push($('#checkBox_'+i).val()); 
			}
		}
		$.ajax({
			url: "?action=saveProductLibraryTypes&category=product",
			data: {
				productId: page.productId, 
				productlibraryTipesIds: productLibraryTypesIds
			},
			type: "Post",
			success: function (response) {
				$('#productLibraryTypes').html(response);
			}
		});
        
		$("#"+this.divId).dialog('close');
	}
    
}

function ManageIndustryTypes() {
	this.divId = 'manageIndustryTypesContainer';
	this.isLoaded = false;

	this.iniDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId != this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 800,
			height: 500,
			autoOpen: false,
			resizable: true,
			dragable: true,
			modal: true,
			buttons: {
				'Cancel': function() {
					$(this).dialog('close');
					that.isLoaded = false;					
				},
				'Save': function() {
					that.save();
				}
			}
		});
	}
	
	this.openDialog = function() {
		$('#'+this.divId).dialog('open');
		if(!this.isLoaded) {
			this.loadContent();
		}
		return false;
	}

	this.loadContent = function() {
		var that = this;
		$.ajax({
			url: "?action=loadIndustryTypes&category=product",
			data: {
				productId: page.productId
			},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				that.isLoaded = true;
			}
		});
	}

	this.save = function() {
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
				var index = checkBoxes[i].value;
				typesClassString.innerHTML += document.getElementById('category_'+index).innerHTML + "; ";
				var hiddenTypesClassID =  document.createElement("input");
				hiddenTypesClassID.type = "hidden";
				hiddenTypesClassID.name = 'typesClass_'+i;
				hiddenTypesClassID.value = checkBoxes[i].value;
				hiddenTypesClasses.appendChild(hiddenTypesClassID);
			}
		}

		//	hide popup
		$("#"+this.divId).dialog('close');
	}
}

function ProductAddEditPage() {
	this.manageIndustryTypes = new ManageIndustryTypes();
	this.manageProductLibraryTypes = new ManageProductLibraryTypes();
	this.productId = false;
}

var page;

$(function() {
	page = new ProductAddEditPage();
	page.manageIndustryTypes.iniDialog();
	page.manageProductLibraryTypes.iniDialog();
});