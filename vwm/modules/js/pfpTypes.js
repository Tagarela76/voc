
function assignPFP2Type(assign) {
    
    var checkboxes = $("#pfpContainer").find("input[type='checkbox']");
    var rowsToUse = new Array();
    checkboxes.each(function(i){
        id = this.value;
        if(this.checked) {
            rowsToUse.push(id);
        }
    });
   
    var pfptypeID=$('#pfptypeID').attr('value');
    var facilityID=$('#facilityID').attr('value');
    
	var urlData = {"action" : assign, "category" : "pfpTypes", "id": pfptypeID, "facilityID" : facilityID, "pfpIDs" : rowsToUse};
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