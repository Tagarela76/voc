$(function() {
	
	$("#country").change(function(){
		id = $(this).val();
		if (id == usaID) {
			$("#state_select_type").val("select");
			$("#txState").css("display","none");
			$("#selState").css("display","inline");
		} else {
			$("#state_select_type").val("text");
			$("#txState").css("display","inline");
			$("#selState").css("display","none");
		}
	});
});