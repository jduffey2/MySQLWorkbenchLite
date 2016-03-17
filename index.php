<!--
    TODO:   Spice up the CSS - it sucks right nowaday
    TODO:   Figure out in schema_success changing null values into either "" or NULL so they show up in the table
    TODO:   Figure out why the last row is there in the getSchema/schema_success function
    TODO:   Add functionality of CUD for Database operations
    TODO:   Add functionality of CRUD for a table in a DB
    TODO:   Add Swag
-->
<html>
	<head>
		<title>DB Table Creation</title>
		<link rel="stylesheet" type="text/css" href="css/dbStyle.css">
		<script type="text/javascript" src="javascript/dbScript.js"></script>
		<script type="text/javascript" src="javascript/jquery-2.2.1.min.js"></script>
	</head>
	<body>
	<div id="authenticateDiv">
            
                Server:<input type="text" id='serverTB' name="serverTB" value="127.0.0.1">
                Username:<input type="text" id='userTB' name="userTB" value="root">
                Password:<input type="password" id='passTB' name="passTB">
                <input type="button" value="Log in" name="authenticate" onclick="authenticate()">
	</div>

	<div id="tablenamesDiv">
            Databases: &nbsp;
            <!-- Add onblur event incase there is only one to choose from, because then onchange cannot fire-->
            <select id='dbSel' name='dbSel' onchange="getTables()" onblur="getTables()">
                <option value="">Please Log in</option>
            </select>
                <br>
            Tables: &nbsp;
            <!-- Add onblur event incase there is only one to choose from, because then onchange cannot fire-->
            <select id='tableSel' name='tableSel' onchange="getSchema()" onblur="getSchema()">
                <option value="">Select a Database</option>
            </select>
	</div>
	<div id="mainContentDiv">
            <table id="displayTbl" name='displayTbl'></table>
	</div>
	</body>
</html>