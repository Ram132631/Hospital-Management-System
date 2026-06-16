<?php
session_start();
$conn = new mysqli("localhost","root","","hospital_db");
if($conn->connect_error) die("Connection failed");

$conn->query("CREATE TABLE IF NOT EXISTS patients(
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
age INT,
gender VARCHAR(20),
phone VARCHAR(20),
disease VARCHAR(255)
)");

$conn->query("CREATE TABLE IF NOT EXISTS doctors(
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
specialization VARCHAR(100),
phone VARCHAR(20)
)");

$conn->query("CREATE TABLE IF NOT EXISTS appointments(
id INT AUTO_INCREMENT PRIMARY KEY,
patient_name VARCHAR(100),
doctor_name VARCHAR(100),
appointment_date DATE,
status VARCHAR(50)
)");

if(isset($_POST['admin_login'])){
    if($_POST['username']=="admin" && $_POST['password']=="admin123"){
        $_SESSION['admin']=true;
    }
}

if(isset($_GET['logout'])){
    session_destroy();
    header("Location:index.php");
    exit;
}

if(isset($_POST['add_patient'])){
    $stmt=$conn->prepare("INSERT INTO patients(name,age,gender,phone,disease) VALUES(?,?,?,?,?)");
    $stmt->bind_param("sisss",$_POST['name'],$_POST['age'],$_POST['gender'],$_POST['phone'],$_POST['disease']);
    $stmt->execute();
}

if(isset($_POST['add_doctor'])){
    $stmt=$conn->prepare("INSERT INTO doctors(name,specialization,phone) VALUES(?,?,?)");
    $stmt->bind_param("sss",$_POST['dname'],$_POST['specialization'],$_POST['dphone']);
    $stmt->execute();
}

if(isset($_POST['book'])){
    $stmt=$conn->prepare("INSERT INTO appointments(patient_name,doctor_name,appointment_date,status) VALUES(?,?,?,'Pending')");
    $stmt->bind_param("sss",$_POST['patient'],$_POST['doctor'],$_POST['date']);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Hospital Management System</title>
<style>
body{font-family:Arial;background:#eef2f7;margin:0}
.container{width:90%;max-width:1100px;margin:auto}
.card{background:#fff;padding:20px;margin:20px 0;border-radius:10px}
input,select,button{padding:10px;margin:5px;width:100%}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #ddd;padding:8px}
h1{color:#0a58ca}
.stat{display:inline-block;padding:15px;background:#fff;margin:10px;border-radius:8px}
</style>
</head>
<body>
<div class="container">
<h1>Hospital Management System</h1>

<?php if(!isset($_SESSION['admin'])){ ?>
<div class="card">
<h2>Admin Login</h2>
<form method="post">
<input name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button name="admin_login">Login</button>
</form>
<p>Default: admin / admin123</p>
</div>
<?php } else { ?>

<a href="?logout=1">Logout</a>

<div>
<?php
$p=$conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$d=$conn->query("SELECT COUNT(*) c FROM doctors")->fetch_assoc()['c'];
$a=$conn->query("SELECT COUNT(*) c FROM appointments")->fetch_assoc()['c'];
?>
<div class="stat">Patients: <?php echo $p; ?></div>
<div class="stat">Doctors: <?php echo $d; ?></div>
<div class="stat">Appointments: <?php echo $a; ?></div>
</div>

<div class="card">
<h2>Add Patient</h2>
<form method="post">
<input name="name" placeholder="Patient Name" required>
<input type="number" name="age" placeholder="Age" required>
<input name="gender" placeholder="Gender" required>
<input name="phone" placeholder="Phone" required>
<input name="disease" placeholder="Disease" required>
<button name="add_patient">Add Patient</button>
</form>
</div>

<div class="card">
<h2>Add Doctor</h2>
<form method="post">
<input name="dname" placeholder="Doctor Name" required>
<input name="specialization" placeholder="Specialization" required>
<input name="dphone" placeholder="Phone" required>
<button name="add_doctor">Add Doctor</button>
</form>
</div>

<div class="card">
<h2>Book Appointment</h2>
<form method="post">
<input name="patient" placeholder="Patient Name" required>
<input name="doctor" placeholder="Doctor Name" required>
<input type="date" name="date" required>
<button name="book">Book Appointment</button>
</form>
</div>

<div class="card">
<h2>Patients</h2>
<table>
<tr><th>Name</th><th>Age</th><th>Disease</th></tr>
<?php
$r=$conn->query("SELECT * FROM patients");
while($row=$r->fetch_assoc()){
echo "<tr><td>{$row['name']}</td><td>{$row['age']}</td><td>{$row['disease']}</td></tr>";
}
?>
</table>
</div>

<div class="card">
<h2>Appointments</h2>
<table>
<tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th></tr>
<?php
$r=$conn->query("SELECT * FROM appointments");
while($row=$r->fetch_assoc()){
echo "<tr><td>{$row['patient_name']}</td><td>{$row['doctor_name']}</td><td>{$row['appointment_date']}</td><td>{$row['status']}</td></tr>";
}
?>
</table>
</div>

<?php } ?>
</div>
</body>
</html>