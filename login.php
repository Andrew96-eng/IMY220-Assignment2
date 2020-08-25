<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false

	$userID = 0;
	$sql = "SELECT * FROM tbusers WHERE email = '$email'";

	$result = $mysqli->query($sql);

	if($result && $result->num_rows > 0)
	{
		while($row = mysqli_fetch_assoc($result))
		{
			$userID = $row["user_id"];
		}
	}
	else
	{
		echo 	"<div class='alert alert-danger mt-3' role='alert'>
				Could not log you in
				</div>";
	}

	if(isset($_POST["submit"]))
	{
		$totalFiles = count($_FILES["picToUpload"]["name"]);

		for( $i=0 ; $i < $totalFiles ; $i++ ) 
		{

			$picToUpload = basename($_FILES["picToUpload"]["name"][$i]);
			$imagePath = $_SERVER['DOCUMENT_ROOT'] . "/imy_220/Assignment_2/images/";

			// User folder
			if(!is_dir($imagePath . $userID . "/"))
			{
				mkdir($imagePath . $userID . "/");
			}
			$imagePath = $imagePath . $userID . "/";

			$uploadOk = 0;
			if(file_exists($imagePath . $picToUpload))
			{
				echo '<div class="alert alert-danger mt-3" role="alert">
						That file already exists.
					</div>';
			}
			else
			{
				$check = getimagesize($_FILES["picToUpload"]["tmp_name"][$i]);
				if($check !== false) 
				{
					
					$uploadOk = 1;
				} 
				else 
				{
					echo "File is not an image.";
					$uploadOk = 0;
				}
			}

			if($uploadOk == 1)
			{
				
				if($_FILES["picToUpload"]["size"][$i] < 1000000)
				{
					
					$imageFileType = strtolower(pathinfo($imagePath . $picToUpload ,PATHINFO_EXTENSION));

					$imagePath = $imagePath . $picToUpload;
					
					if($imageFileType != "jpg" && $imageFileType != "jpeg") 
					{
						// incorrect file type
						echo '<div class="alert alert-danger mt-3" role="alert">
								Incorrect File type.
							</div>';
					}
					else
					{
						move_uploaded_file($_FILES["picToUpload"]["tmp_name"][$i], $imagePath);

						$insertSql = "INSERT INTO tbgallery (user_id, filename) VALUES ($userID, '$picToUpload')";
				
						$response = $mysqli->query($insertSql);
						if($response)
						{
							//success
						}
						else
						{
							echo '<div class="alert alert-danger mt-3" role="alert">
								Failed to insert into gallery table.
							</div>';
						}
					}
				}
				else
				{
					$fileUploadedPos = $i + 1;
					//to large
					echo '<div class="alert alert-danger mt-3" role="alert">
								File ' . $fileUploadedPos . ' too large.
							</div>';
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Andrew Wilson">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='' method='POST' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple='multiple' /><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
									<input type='hidden' id='loginEmail' name='loginEmail' value=$email>
									<input type='hidden' id='loginPass' name='loginPass' value=$pass>
								</div>
							  </form>";
							  
					$imageSQL = "SELECT * FROM tbgallery WHERE user_id = $userID";
					$res2 = $mysqli->query($imageSQL);
					echo "<div class='row'><h2>Image Gallery</h2></div><div class='row imageGallery'>";
					if($res2 && $res2->num_rows > 0)
					{
						while($imageRow = mysqli_fetch_assoc($res2))
						{
							$imageGalleryPath ="images/". $userID . "/" . $imageRow['filename'];
							echo "<div class='col-3' style='background-image: url($imageGalleryPath)'></div>";
						}
					}
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>