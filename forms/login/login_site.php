<?php
	
	ob_start();	
	$error = 0;
	if (isset($_POST['TRYlogin']))
	{
	$pass = $_POST['pass'];
	
		if ($pass=='SOLVEIT')
		{
			echo "Logowanie poprawne";
			$_SESSION['MAIN_LOGIN'] = 10;
			header("Location: ".BDIR);
		}
		else {
			$error=1;
		}
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>LOGIN</title>
		<meta content="index,follow" name="robots">
		<meta content="Aquarius" name="description">
		<meta content="" name="keywords">
		<link media="screen" href="<?php echo BDIR; ?>styles/login.css" type="text/css" rel="stylesheet">
		
	<style type="text/css">
	body {
    background: #f1f2ed;
    color: #000;
    font-family: Helvetica,Arial,sans-serif;
	
}

.site {
	background: white;
    margin: 1% auto;
	width: 350px;
	border: silver solid 1px;    
	overflow: hidden;
}

.top{
	margin: 30px auto;
	margin-bottom:35px;
	color: black;
	font-weight: bold;
	font-size: 20px;
	text-align: center;
}

.form{
	margin:10px 10% 10px 10%;
	font-size: 14px;
}

#login-button, #login-button:hover{
	background-color: #1a6e7c; 
	color: white;
	margin-top: 30px;
	margin-right: 28%;
	margin-bottom: 20px;
	width: 150px;
	height: 40px;
	font-size:14px;
	float: right;
	cursor: pointer;
	border: 1px solid #EEEEEE;
}

#login-button:hover{
	background: #2a7e8c;
	border: 1px solid #CCCCCC;

}

#username, #userpass{
	width: 100%;
	margin: auto;
	padding: 5px;
	height: 35px;
	font-size: 16px;
}

.error{
	color:red;
	font-size:10px;
	margin: auto;
	text-align: center;
}
	
	</style>	

	</head>
	
	<body>
	    <div class="site">
			<div class="top">
				<img src="<?php echo BDIR; ?>images/info.jpg" width="35%"/>
			</div>
			<form method="post" action="">
				<?php if ($error) { ?>
					<div class="error">Bad password</div>
				<?php } ?>
				<div class="form">Password</div>
				<div class="form"><input id="userpass" type="password" value="" name="pass"></div>
				<button id="login-button" name='TRYlogin' type="submit">Log in</button>
			
			</form>
			
			
	   
		</div>

	</body>
			
</html>
