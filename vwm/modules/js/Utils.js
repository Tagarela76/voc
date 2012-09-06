/*
 * Utils class is used to all over the system and let's you to do common tasks
 */
function Utils() {
	
	this.escapeHTML = function(html) {
		if(!html) {
			return "";
		}
		return html.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
	}
}