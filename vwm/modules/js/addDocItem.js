    function fileSelected(id) {
        document.getElementById(id).disabled = 0;
        document.getElementById('error_path').style.display = 'none';
        document.getElementById('err').style.display = 'none';
    }
    
    function showAddItemType(type) {
    	if (type == 'file') {
    		document.getElementById('div_input').style.display = 'block';
    		document.getElementById('docDescription').style.display = 'block';
    		document.getElementById('upload').disabled = 1;

    	} else {
    		document.getElementById("div_input").innerHTML = document.getElementById("div_input").innerHTML;
    		document.getElementById('div_input').style.display = 'none';
    		document.getElementById('docDescription').style.display = 'none'; 
    		document.getElementById('upload').disabled = 0;  
	
    	}
    }

    function showEditItemType(type) {
    	if (type == 'file') {
    		document.getElementById('div_input').style.display = 'block';
    		document.getElementById('docDescription').style.display = 'block';
    		document.getElementById('div_select').style.display = 'none';

    	} else {
    		document.getElementById('div_input').style.display = 'none';
    		document.getElementById('docDescription').style.display = 'none'; 
    		document.getElementById('div_select').style.display = 'block'; 
	
    	}
    	document.getElementById("table").innerHTML = document.getElementById("table").innerHTML;
    	//document.getElementById("select").innerHTML = document.getElementById("select").innerHTML;
    	document.getElementById("docName").value = "";
    	document.getElementById("docDescription").value = "";
    }
    
    function setInfo(d_id,d_type,d_name,d_descr,d_parent_id) {
		document.getElementById('docName').value = d_name;
		document.getElementById('docDescription').value = d_descr;
		id = "r_"+d_parent_id;
		document.getElementById(id).checked = true;
    }
