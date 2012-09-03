
function assignPFP2Type() {
    
    var pfpID=$('#selectPfp').attr('value');
    var pfptypeID=$('#pfptypeID').attr('value');
    var facilityID=$('#facilityID').attr('value');
    
	var urlData = {"action" : "assign", "category" : "pfpTypes", "id": pfptypeID, "facilityID" : facilityID, "pfpID" : pfpID};
	$.ajax({
		url:'index.php',
		type: "GET",
		async: true,
		data: urlData,
		dataType: "html",
  		success: function (response) {
            window.location=response;
        }
	});
}

function unAssignPFP2Type() {
    
    var checkboxes = $("#pfpContainer").find("input[type='checkbox']");
    var rowsToUnAssign = new Array();
    checkboxes.each(function(i){
        id = this.value;
        if(this.checked) {
            rowsToUnAssign.push(id);
        }
    });
      
    var pfptypeID=$('#pfptypeID').attr('value');
    var facilityID=$('#facilityID').attr('value');
    
	var urlData = {"action" : "unassign", "category" : "pfpTypes", "id": pfptypeID, "facilityID" : facilityID, "pfpIDs" : rowsToUnAssign};
	$.ajax({
		url:'index.php',
		type: "GET",
		async: true,
		data: urlData,
		dataType: "html",
  		success: function (response) {
            window.location=response;
        }
	});
}