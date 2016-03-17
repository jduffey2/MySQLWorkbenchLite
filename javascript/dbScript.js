//TODO: Figure out in schema_success changing null values into either "" or NULL so they show up in the table
//TODO Figure out why the last row is there in the getSchema/schema_success function


//authenticate - Executes an AJAX call to authenticate a user on the server
//Parameters - NONE
//Return - NONE
function authenticate() {
    var serverIP = $("#serverTB").val();
    var username = $("#userTB").val();
    var password = $("#passTB").val();
    data = {method: 'authenticateUser', server: serverIP, user: username, pass: password};
    
    AJAXCall(data, authenticate_success);
}
//authenticate_success - The function that gets executed when the authenticate
//                       ajax returns successfully.
//                       It calls the getDatabases if the user authenticates
//                       successfully
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
function authenticate_success(data) {
    var value = JSON.parse(data);
    if(value['error'] !== null ) {
            if(value['error'] == 0) {
                    //appropriately authenticated
                    //run getDatabases
                    getDatabases($("#serverTB").val(), $("#userTB").val(), $("#passTB").val());
            }
    }
}



//getDatabases - Executes an AJAX call to get the names of the databases an
//               authenticated user is able to view
//Parameters - NONE
//Return - NONE
function getDatabases(server, user, pass) {
    data = {method: "getDatabases", server: server, user: user, pass: pass}; 
    
    AJAXCall(data, getDatabases_success);
}
//getDatabases_success - The function that gets executed when the getDatabases
//                       ajax returns successfully It puts the returned DB
//                       names into a select box for the user to view
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
function getDatabases_success(data) {
    var value = JSON.parse(data);
    var dbSel = $("#dbSel"); //Get the select box
    dbSel.empty(); //Empty any DBs that were in it previously, if the user changes DBs
    if(value['error'] === 0) {
        $.each(value['dbs'], function(index, value) {
            dbSel.append($("<option>", {
                value: value,
                text: value
            }));
        });
    }
}



//getTables - Executes an AJAX call to get the names of the tables in a specific DB
//Parameters - NONE
//Return - NONE
function getTables() {
	var serverIP = $("#serverTB").val();
	var username = $("#userTB").val();
	var password = $("#passTB").val();
        var db = $("#dbSel").val();
	data = {method: 'getTables', server: serverIP, user: username, pass: password, db: db};

        AJAXCall(data, getTable_success);
}
//getTable_success - The function that gets executed when the getTables ajax
//                   returns successfully It puts the returned table names
//                   into a select box for the user to view
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
function getTable_success(data) {
    var value = JSON.parse(data);
    var tableSel = $("#tableSel"); //get the DOM select element for the table names
    tableSel.empty();
    if(value['error'] === 0) {
        $.each(value['tables'], function(index, value) {
            tableSel.append($("<option>", {
                value: value,
                text: value
            }));
        });
    }    
}



//getSchema - Executes an AJAX call to get the schema of a specific table in a
//            specific DB
//Parameters - NONE
//Return - NONE
function getSchema() {
    var serverIP = $("#serverTB").val();
    var username = $("#userTB").val();
    var password = $("#passTB").val();
    var db = $("#dbSel").val();
    var table = $('#tableSel').val();
    data = {method: 'getSchema', server: serverIP, user: username, pass: password, db: db, table: table};

    AJAXCall(data, schema_success);
}
//schema_success - The function that gets executed when the getSchema ajax
//                 returns successfully It creates a table for the user to view
//                 the schema, right now it is read only
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
function schema_success(data) {
    var value = JSON.parse(data);
    //Build a table of the table schema
    var table = $('#displayTbl');
    table.empty();
    table.append("<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>");
    $.each(value['field'], function(index, val){
        table.append("<tr><td>" + value['field'][index] + "</td><td>" + value['type'][index] + "</td><td>" + value['null'][index] + "</td><td>");
        table.append(value['key'][index] + "</td><td>" + value['default'][index] + "</td><td>" + value['extra'][index] + "</td></tr>");
    });
}


//AJAXCall - Executes the actual AJAX call for all of the functions above
//Parameters: data - the data to pass to the DBAPI as a JS object
//            success_function: the function to execute if ajax returns
//             successfully
//Return - NONE
//NOTE: data must include field: method - which is the method name in the API
//      to get executedsuccess_function must accept one parameter that is the
//      JSON data that gets returned from the AJAX call
function AJAXCall(data, success_function) {
    $.ajax({
            url: 'DBAPI.php',
            data: data,
            type: 'post',
            datatype: 'json',
            success: function(return_value) {
                    success_function(return_value);
            },
            error: function(xhr, message) {
                alert("error");
            }
	});
}