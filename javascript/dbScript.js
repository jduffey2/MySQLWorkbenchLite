function showContents(table) {
	//alert("show contents of " + table);
	data = {action: 'table_content', table: table};
	makeAJAX(data);
}


//Format of data:
// {
//	   action: 'function_name'
// }
function makeAJAX(data) {
	var results;
	$.ajax({
		url: 'DBAPI.php',
		data: data,
		type: 'post',
		datatype: 'json',
		success: function(return_value) {
			var value = JSON.parse(return_value);
			if(value['display_data'] !== null ) {
				//the query returned results to display
				//they should be displayed here
			}
		}
	});
}


function authenticate() {
	var serverIP = document.getElementById("serverTB").value;
	var username = document.getElementById("userTB").value;
	var password = document.getElementById("passTB").value;
	data = {action: 'authenticate', server: serverIP, user: username, pass: password};

	$.ajax({
		url: 'DBAPI.php',
		data: data,
		type: 'post',
		datatype: 'json',
		success: function(return_value) {
			var value = JSON.parse(return_value);
			if(value['error'] !== null ) {
				if(value['error'] == 0) {
					
				}
			}
		}
	});
}