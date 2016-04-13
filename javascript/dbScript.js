////TODO: Figure out in schema_success changing null values into either "" or NULL so they show up in the table
//TODO: Figure out why the last row is there in the getSchema/schema_success function
//TODO: tableize function breaks if the table has no data in it
/*
 * Name: dbScript.js
 * Author: Jason Duffey
 *         Ohio Northern University
 * Date: 3/2016
 * 
 * Uses DBAPI to access arbitrary databases, used in this context to create
 * a 'MySQL Workbench Lite' type of implementation
 */
var CurtableData = {};

function authenticate() {
//authenticate - Executes an AJAX call to authenticate a user on the server
//Parameters - NONE
//Return - NONE
    var serverIP = $("#serverTB").val();
    var username = $("#userTB").val();
    var password = $("#passTB").val();
    data = {method: 'authenticateUser', server: serverIP, user: username, pass: password};
    
    AJAXCall(data, authenticate_success);
}
function authenticate_success(data) {
//authenticate_success - The function that gets executed when the authenticate
//                       ajax returns successfully.
//                       It calls the getDatabases if the user authenticates
//                       successfully
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
    var value = JSON.parse(data);
    if(value['error'] !== null ) {
            if(value['error'] == 0) {
                    //appropriately authenticated
                    //run getDatabases
                    getDatabases($("#serverTB").val(), $("#userTB").val(), $("#passTB").val());
            }
    }
}

function getDatabases(server, user, pass) {
//getDatabases - Executes an AJAX call to get the names of the databases an
//               authenticated user is able to view
//Parameters - NONE
//Return - NONE
    data = {method: "getDatabases", server: server, user: user, pass: pass}; 
    
    AJAXCall(data, getDatabases_success);
}
function getDatabases_success(data) {
//getDatabases_success - The function that gets executed when the getDatabases
//                       ajax returns successfully It puts the returned DB
//                       names into a select box for the user to view
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
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

function getTables() {
//getTables - Executes an AJAX call to get the names of the tables in a specific DB
//Parameters - NONE
//Return - NONE
    var serverIP = $("#serverTB").val();
    var username = $("#userTB").val();
    var password = $("#passTB").val();
    var db = $("#dbSel").val();
    data = {method: 'getTables', server: serverIP, user: username, pass: password, db: db};

    AJAXCall(data, getTable_success);
}
function getTable_success(data) {
//getTable_success - The function that gets executed when the getTables ajax
//                   returns successfully It puts the returned table names
//                   into a select box for the user to view
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
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

function getSchema() {
//getSchema - Executes an AJAX call to get the schema of a specific table in a
//            specific DB
//Parameters - NONE
//Return - NONE
    var serverIP = $("#serverTB").val();
    var username = $("#userTB").val();
    var password = $("#passTB").val();
    var db = $("#dbSel").val();
    var table = $('#tableSel').val();
    data = {method: 'getSchema', server: serverIP, user: username, pass: password, db: db, table: table};

    AJAXCall(data, schema_success);
    
    getTableData();
}
function schema_success(data) {
//schema_success - The function that gets executed when the getSchema ajax
//                 returns successfully It creates a table for the user to view
//                 the schema, right now it is read only
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
    var value = JSON.parse(data);
    //Build a table of the table schema
    var table = $('#displayTbl');
    table.empty();
    table.append("<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>");
    $.each(value['field'], function(index, val){
        table.append("<tr><td>" + value['field'][index] + "</td><td>" + value['type'][index] + "</td><td>" + value['null'][index] + "</td><td>");
        table.append(value['key'][index] + "</td><td>" + value['default'][index] + "</td><td>" + value['extra'][index] + "</td></tr>");
    });
    
    //clear out any current table data
    $("#contentTbl").html("");
    
    CurtableData['primary'] = value['primary'];
    CurtableData['keyColumn'] = value['keyColumn'];
    CurtableData['referencedTable'] = value['referencedtable'];
    CurtableData['referencedColumn'] = value['referencedColumn'];
}

function getTableData() {
//getTableData - Executes an AJAX call to get the SELECT * of a specific table
//               in a specific DB
//Parameters - NONE
//Return - NONE
    var serverIP = $("#serverTB").val();
    var username = $("#userTB").val();
    var password = $("#passTB").val();
    var db = $("#dbSel").val();
    var table = $('#tableSel').val();
    data = {method: 'selectAll', server: serverIP, user: username, pass: password, db: db, table: table};

    AJAXCall(data, tableData_success);
}
function tableData_success(data) {
//tableData_success - gets executed when the getTableData ajax returns successfully
//                    It creates a table for the user to view the data in the 
//                    selected table
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
    var value = JSON.parse(data);
    $("#contentTbl").html("");
    var table = tableize(value);
    $("#contentTbl").html(table);
    $("#controlDiv").html('<input type="button" id="insertBtn" value="Insert Row" onclick="insertRow()" />');
    
    CurtableData['table'] = $('#tableSel').val();
}

function AJAXCall(data, success_function) {
//AJAXCall - Executes the actual AJAX call for all of the functions above
//Parameters: data - the data to pass to the DBAPI as a JS object
//            success_function: the function to execute if ajax returns
//             successfully
//Return - NONE
//NOTE: data must include field: method - which is the method name in the API
//      to get executedsuccess_function must accept one parameter that is the
//      JSON data that gets returned from the AJAX call
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

function tableize(tableData) {
//tablize - generates the html for a table with the passed in JS Object
//Parameters - tableData: the data as a JS Object that is to be put into the table
//NOTE: the Object requires a 'columns' field which is an array with the name of
//      the columns, as well as fields that are arrays and key are the same name
//      as the data in the columns array
//EXAMPLE: tableData = Object {columns: ['Name','ID','Field3'], Name:Array[2], ID:Array[2], Field3:Array[2]}
//Return: a string that is the HTML for the table
    
    //open the table and header row
    var table = "<table><tr>";
    var columns = [];
    
    //add the header row
    for(var i = 0; i < tableData.columns.length; i++) {
        table += "<th>" + tableData.columns[i] + "</th>";
        columns.push(tableData.columns[i]);
    }
    table += "<th>Delete</th>";
    CurtableData['columns'] = columns;
    //close the header row
    table += "</tr>"
    
    //iterate through the elements in the remaining columns fields
    //gets the length of the array titled the same as the first column in condition
    if(typeof(tableData[tableData.columns[0]]) !== 'undefined') {
        for(var j = 0; j < tableData[tableData.columns[0]].length; j++) {
            table += "<tr>";
            for(var i = 0; i < tableData.columns.length; i++) {
                table += "<td>" + tableData[tableData.columns[i]][j] + "</td>";
            }
            table += "<td><input type='button' value='Delete' onclick='delete()' /></td>";
            table += "</tr>";
        }
    }
    
    //iterate through adding an edit box to insert a new row, either a textbox
    //or a select box if there is a foreign key
    table += "<tr>";
    for(var i = 0; i < tableData.columns.length; i++) {
        if($.inArray(tableData.columns[i], CurtableData['keyColumn']) == -1) {
            table += "<td><input type='textbox' id='" + tableData.columns[i] + "' /></td>";
        }
        else {
            table += "<td><select id='" + tableData.columns[i] + "'></select></td>";
            getSelData(tableData.columns[i]);
        }
    }
    table += "<td></td>";
    
    
    //close the table
    table += "</table>";
    
    return table;
}

function insertRow() {
//insertRow - Executes an AJAX call to insert a row into a specific table in a
//            specific DB
//Parameters - NONE
//Return - NONE
    var serverIP = $("#serverTB").val();
    var username = $("#userTB").val();
    var password = $("#passTB").val();
    var db = $("#dbSel").val();
    
    var rowData = {};
    
    //get all of the inputed data
    for(var i = 0; i < CurtableData['columns'].length; i++) {
        var selector = "#" + CurtableData['columns'][i];
        rowData[CurtableData['columns'][i]] = $(selector).val();
    }
    
    data = {method:"insert", server:serverIP, user:username, pass:password, db:db, tableData:CurtableData, data: rowData};
    AJAXCall(data, insert_success);
}
function insert_success(data) {
//schema_success - The function that gets executed when the insertRow ajax
//                 returns successfully It updates the table of values for the table
//Parameters - data: the data returned from the AJAX call in JSON
//Return - NONE
    getTableData();
}

function getSelData(column) {
//getSelData - cross references the referenced table and columns and 
//             executes an ajax call to grab the appropriate values from the DB
//Parameters - column - the column name to find the referenced column to get the 
//             data from
//Return - NONE
    var serverIP = $("#serverTB").val();
    var username = $("#userTB").val();
    var password = $("#passTB").val();
    var db = $("#dbSel").val();
    var select = {};
    
    //gets the referenced table and column
    var index = $.inArray(column, CurtableData['keyColumn']);
    select['table'] = CurtableData['referencedTable'][index];
    select['where'] = 1;
    select['columns'] = [];
    select['columns'].push(CurtableData['referencedColumn'][index]);
    data = {method: 'selectRows', server: serverIP, user: username, pass: password, db: db, selectData:select};
    
    //executes the AJAX call and builds the <option> elements upon success
    $.ajax({
            url: 'DBAPI.php',
            data: data,
            type: 'post',
            datatype: 'json',
            success: function(return_value) {
                   var value = JSON.parse(return_value);
                   var selElement = "#" + column;
                   var ElemSel = $(selElement); //get the DOM select element for the table names
                   ElemSel.empty();
                   if(value['error'] === 0) {
                       $.each(value[CurtableData['referencedColumn'][index]], function(ind, value) {
                           ElemSel.append($("<option>", {
                               value: value,
                               text: value
                           }));
                       });
                   }    
            },
            error: function(xhr, message) {
                alert("error");
            }
    });
}