<?php
//register.php

include('database_connection.php');
include('class/class.phpmailer.php');
if(isset($_SESSION['user_id']))
{
	header("location:index.php");
}

$message = '';

if(isset($_POST["register"]))
{
	$query = "SELECT * FROM register_user WHERE user_email = :user_email";
	$statement = $connect->prepare($query);
	$statement->execute(
		array(
			':user_email'	=>	$_POST['user_email']
		)
	);
	$no_of_row = $statement->rowCount();
	if($no_of_row > 0)
	{
		$message = '<label class="text-danger">Email Already Exits</label>';
	}
	else
	{
		$message = '<label class="text-danger">Try again 1</label>';
		$user_password = rand(100000,999999);
		$user_encrypted_password = password_hash($user_password, PASSWORD_DEFAULT);
		$user_activation_code = md5(rand());
		$insert_query = "INSERT INTO register_user(user_name, user_email, user_password, user_activation_code, user_email_status) 
		VALUES (:user_name, :user_email, :user_password, :user_activation_code, :user_email_status)";
		$statement = $connect->prepare($insert_query);
		$statement->execute(
			array(
				':user_name'			=>	$_POST['user_name'],
				':user_email'			=>	$_POST['user_email'],
				':user_password'		=>	$user_encrypted_password,
				':user_activation_code'	=>	$user_activation_code,
				':user_email_status'	=>	'not verified'
			)
		);
		$result = $statement->fetchAll();
		//$message = '<label class="text-danger">Try again 2</label>';
		if(isset($result))
		{
			$base_url = "http://localhost/email-address-verification-script-using-php-demo/";  //change this baseurl value as per your file path
			$mail_body = "
			<p>Hi ".$_POST['user_name'].",</p>
			<p>Thanks for Registration. Your password is ".$user_password.", This password will work only after your email verification.</p>
			<p>Please Open this link to verified your email address - ".$base_url."email_verification.php?activation_code=".$user_activation_code."
			<p>Best Regards,<br />BirdNest</p>
			";
			//$message = '<label class="text-danger">Try again 3</label>';
			require 'class/PHPMailerAutoload.php';
			$mail = new PHPMailer();
			$mail->IsSMTP();				
			$mail->Host = 'smtp.gmail.com';	 
			$mail->Port = 587;		
			$mail->SMTPAuth = true;	
			$mail->SMTPSecure = 'tls';
			
			$mail->Username = 'property.birdnest@gmail.com';	
			$mail->Password = 'Murarepur';
			
			$mail->setFrom = ('property.birdnest@gmail.com');		
			$mail->FromName = 'BirdNest';					
			$mail->addAddress($_POST['user_email'], $_POST['user_name']);				
			$mail->WordWrap = 50;							
			$mail->IsHTML(true);							
			$mail->Subject = 'Email Verification';			
			$mail->Body = $mail_body;	
			if($mail->send()){								
			$message = '<label class="text-success">Register Done, Please check your mail.</label>';
			}
			//$message = '<label class="text-danger">Try again 5</label>';
	
	
						/*
									require 'phpmailer/PHPMailerAutoload.php';
									$mail = new PHPMailer;

									$mail->isSMTP();
									$mail->Host='smtp.gmail.com';
									$mail->Port=587;
									$mail->SMTPAuth = true;
									$mail->SMTPSecure='tls';

									$mail->Username='saratajsultan@gmail.com';// akhan thaka mail jabe
									$mail->Password='i want walpad';

									$mail->setFrom('saratajsultan@gmail.com','BirdNest');
									$mail->addAddress('sarataj35-1619@diu.edu.bd');
									$mail->addReplyTo('saratajsultan@gmail.com');
									$mail->isHTML(true);
									$mail->Subject='PHP Miler Subject';
									$mail->Body='<h1 align=center>Subscribe My </h1> <br> <h4 align=center>-----------</h4>';

									if(!$mail->send()){
										echo"could not send";
										
									}
									else{
										echo"Success";
									}
						
						*/
	
	
	
	
	
	
	
		}
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>PHP Register Login Script with Email Verification</title>		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container" style="width:100%; max-width:600px">
			<h2 align="center">PHP Register Login Script with Email Verification</h2>
			<br />
			<div class="panel panel-default">
				<div class="panel-heading"><h4>Register</h4></div>
				<div class="panel-body">
					<form method="post" id="register_form">
						<?php echo $message; ?>
						<div class="form-group">
							<label>User Name</label>
							<input type="text" name="user_name" class="form-control" pattern="[a-zA-Z ]+" required />
						</div>
						<div class="form-group">
							<label>User Email</label>
							<input type="email" name="user_email" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="register" id="register" value="Register" class="btn btn-info" />
						</div>
					</form>
					<p align="right"><a href="login.php">Login</a></p>
				</div>
			</div>
		</div>
	</body>
</html>