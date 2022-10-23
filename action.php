<?php
session_start();
include 'config.php';

$update = false; // So it shows Add button for default instead of Update
$id = "";
$name = "";
$email = "";
$phone = "";
$photo = "";

// ADD RECORD FUNCTION
if (isset($_POST['add'])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];

	$photo = $_FILES['image']['name'];
	$upload = "uploads/" . $photo;

	$query = "INSERT INTO crud(name,email,phone,photo)VALUES(?,?,?,?)";
	$stmt = $conn->prepare($query); // stmt creates an object to execute the same query multiple times
	$stmt->bind_param("ssss", $name, $email, $phone, $upload); // Specify data type, in this case 4 are string, so it makes the insert only if it's a string (to prevent SQL Injection)
	$stmt->execute();
	move_uploaded_file($_FILES['image']['tmp_name'], $upload);

	header('location:index.php');
	$_SESSION['response'] = "Successfully Inserted to the database!";
	$_SESSION['res_type'] = "success";
}


// DELETE FUNCTION
if (isset($_GET['delete'])) {
	$id = $_GET['delete'];

	$sql = "SELECT photo FROM crud WHERE id=?";
	$stmt2 = $conn->prepare($sql);
	$stmt2->bind_param("i", $id);
	$stmt2->execute();
	$result2 = $stmt2->get_result();
	$row = $result2->fetch_assoc();

	$imagepath = $row['photo'];
	unlink($imagepath);

	$query = "DELETE FROM crud WHERE id=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("i", $id);
	$stmt->execute();

	header('location:index.php');
	$_SESSION['response'] = "Successfully Deleted!";
	$_SESSION['res_type'] = "danger";
}


// EDIT FUNCTION
if (isset($_GET['edit'])) {
	$id = $_GET['edit'];

	$query = "SELECT * FROM crud WHERE id=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();

	$id = $row['id'];
	$name = $row['name'];
	$email = $row['email'];
	$phone = $row['phone'];
	$photo = $row['photo'];

	$update = true;
}
if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$oldimage = $_POST['oldimage'];

	if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != "")) {
		$newimage = "uploads/" . $_FILES['image']['name'];
		unlink($oldimage);
		move_uploaded_file($_FILES['image']['tmp_name'], $newimage);
	} else {
		$newimage = $oldimage;
	}
	$query = "UPDATE crud SET name=?,email=?,phone=?,photo=? WHERE id=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("ssssi", $name, $email, $phone, $newimage, $id);
	$stmt->execute();

	$_SESSION['response'] = "Updated Successfully!";
	$_SESSION['res_type'] = "primary";
	header('location:index.php');
}


// SHOW DETAILS FUNCTION
if (isset($_GET['details'])) {
	$id = $_GET['details'];
	$query = "SELECT * FROM crud WHERE id=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();

	$vid = $row['id'];
	$vname = $row['name'];
	$vemail = $row['email'];
	$vphone = $row['phone'];
	$vphoto = $row['photo'];
}
