	function showDir(id) {
		visibility = document.getElementById(id).style.display;
		if (visibility != "block") {
			document.getElementById(id).style.display = "block";
		} else {   
			document.getElementById(id).style.display = "none";
		}
	}
