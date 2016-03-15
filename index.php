<html>
	<head>
		<title>DB Table Creation</title>
		<link rel="stylesheet" type="text/css" href="css/dbStyle.css">
		<script type="text/javascript" src="javascript/dbScript.js"></script>
		<script type="text/javascript" src="javascript/jquery-2.2.1.min.js"></script>
	</head>
	<body>
        <?php
            require_once 'DBAPI.php';
        ?>
	<div id="authenticateDiv">
            <?php
                $hide_area = "";
                //If the form was submitted by the authentication submit button
                if(isset($_POST['authenticate'])) {
                    //try authenticating the user
                    
                    $auth = authenticateUser($_POST['serverTB'], $_POST['userTB'],$_POST['passTB']);
                    //no error generated means the connection was successful and the user authenticated
                    //store credentials in session variable
                    //hide authentication boxes
                    if($auth['error'] == 0) {
                        $_SESSION['server'] = $_POST['serverTB'];
                        $_SESSION['username'] = $_POST['userTB'];
                        $_SESSION['password'] = $_POST['passTB'];
                        $_SESSION['authenticated'] = TRUE;
                        $hide_area = "class='hidden'";                     
                    }
                }
            ?>
            <div id="formDiv" <?php echo $hide_area;?> >
                <form action="" method="post">
                    Server:<input type="text" name="serverTB" value="127.0.0.1">
                    Username:<input type="text" name="userTB" value="root">
                    Password:<input type="password" name="passTB">
                    <input type="submit" value="Submit" name="authenticate">
                </form>
            </div>
	</div>

	<div id="tablenamesDiv">
            <?php
                if(isset($_SESSION['authenticated'])) {
                    if($_SESSION['authenticated'] == TRUE) {
                        $dbs = getDatabases($_SESSION['server'], $_SESSION['username'], $_SESSION['password']);
                        echo implode(", ", $dbs['dbs']);
                    }
                }
            ?>
		<ul>
			<label for='tablesList'>Databases</label>
				<ul>
					<!--<php
						if($result->num_rows > 0) {
							while($row = $result->fetch_array()) {
								echo "<li class='tableEntry' onclick='showContents(`" . $row[0] . "`)'>" . $row[0] . "</li>";
							}
						}
					?>-->
				</ul>
		</ul>
	</div>
	<div id="mainContentDiv">
	</div>
	</body>
</html>