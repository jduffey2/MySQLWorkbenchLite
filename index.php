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
                //User hit log out button
                if(isset($_POST['logout'])) {
                    unset($_COOKIE['server']);
                    unset($_COOKIE['username']);
                    unset($_COOKIE['password']);
                    setcookie('authenticated', FALSE, time()+86400);
                }
            
                $hide_area = "";
                //If the form was submitted by the authentication submit button
                if(isset($_POST['authenticate'])) {
                    //try authenticating the user
                    
                    $auth = authenticateUser($_POST['serverTB'], $_POST['userTB'],$_POST['passTB']);
                    //no error generated means the connection was successful and the user authenticated
                    //store credentials in session variable
                    //hide authentication boxes
                    if($auth['error'] == 0) {
                        setcookie('server',$_POST['serverTB'],time() + 86400);
                        setcookie('username',$_POST['userTB'],time() + 86400);
                        setcookie('password',$_POST['passTB'],time() + 86400);
                        setcookie('authenticated',TRUE,time() + 86400);
//                        $_SESSION['username'] = $_POST['userTB'];
//                        $_SESSION['password'] = $_POST['passTB'];
//                        $_SESSION['authenticated'] = TRUE;
                        $hide_area = "class='hidden'";                     
                    }
                }
            ?>
            
            <form action="" method="post" <?php echo $hide_area;?>>
                Server:<input type="text" name="serverTB" value="">
                Username:<input type="text" name="userTB" value="">
                Password:<input type="password" name="passTB">
                <input type="submit" value="Submit" name="authenticate">
            </form>
            
            <form action="" method="post">    
                <input type="submit" name="logout" value="Log Out">
            </form>
	</div>

	<div id="tablenamesDiv">
            <?php
                //The user is authenticated, get dbs that the user can view
                if((isset($_COOKIE['authenticated']) && !isset($_POST['logout'])) || $auth['error'] == 0) {
                    if($_COOKIE['authenticated'] == 1) {
                        $dbs = getDatabases($_COOKIE['server'], $_COOKIE['username'], $_COOKIE['password']);
                    }
                }
                
                //The user has selected a db and wishes to view the tables
                if(isset($_POST['dbSelSubmit']) && isset($_COOKIE['server'])) {
                    $tables = getTables($_COOKIE['server'], $_COOKIE['username'], $_COOKIE['password'], $_POST['dbSel']);
                }
            ?>
            Databases: &nbsp;
            <form action="" method="post">
                <select name='dbSel'>
                    <?php
                        if(isset($dbs['dbs'])) {
                            $list = $dbs['dbs'];
                            for($i = 0; $i < sizeof($list); $i++) {
                                echo "<option value='$list[$i]'>" . $list[$i] . "</option>";
                            }
                        }
                    ?>

                </select>
                <br>
                <input type='submit' name='dbSelSubmit' value='Change DB'>
            </form>
            
            Tables: &nbsp;
            <form action="" method="post">
                <select name='tableSel'>
                    <?php
                        if(isset($tables['tables'])) {
                            $list = $tables['tables'];
                            for($i = 0; $i < sizeof($list); $i++) {
                                echo "<option value='$list[$i]'>" . $list[$i] . "</option>";
                            }
                        }
                    ?>

                </select>
                <br>
                <input type='submit' name='tableSelSubmit' value='Change Table'>
            </form>
	</div>
	<div id="mainContentDiv">
	</div>
	</body>
</html>