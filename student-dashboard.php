<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// ✅ Check if student logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// ✅ Get student info
$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id=$student_id"));

$success = '';
$errors = [];

// ✅ Upload resume
if (isset($_POST['upload_resume'])) {
    if (!empty($_FILES['resume']['name'])) {
        $resume = $_FILES['resume']['name'];
        $tmp_name = $_FILES['resume']['tmp_name'];

        // Create uploads folder if not exists
        if (!is_dir("uploads")) {
            mkdir("uploads");
        }

        // Move file to uploads folder
        if (move_uploaded_file($tmp_name, "uploads/$resume")) {
            mysqli_query($conn, "UPDATE students SET resume='$resume' WHERE id=$student_id");
            $success = "Resume uploaded successfully!";
        } else {
            $errors[] = "Failed to upload resume.";
        }
    } else {
        $errors[] = "Please select a file to upload.";
    }
}

// ✅ Apply for a job
if (isset($_GET['apply'])) {
    $job_id = intval($_GET['apply']);

    // Check if already applied
    $check = mysqli_query($conn, "SELECT * FROM applications WHERE student_id=$student_id AND job_id=$job_id");

    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO applications(student_id, job_id, status, applied_at) VALUES($student_id, $job_id, 'Pending', NOW())");
        $success = "Applied successfully!";
    } else {
        $errors[] = "You already applied for this job!";
    }
}

// ✅ Get all jobs (FIXED → use id instead of posted_at)
$jobs = mysqli_query($conn, "SELECT * FROM jobs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { background: #fff; padding: 20px; border-radius: 8px; max-width: 800px; margin: auto; }
        h1 { color: #333; }
        .success { color: green; }
        .error { color: red; }
        .job { border: 1px solid #ddd; margin: 10px 0; padding: 10px; border-radius: 5px; }
        a.button { background: #28a745; color: white; padding: 6px 12px; text-decoration: none; border-radius: 5px; }
        a.button:hover { background: #218838; }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome, <?php echo $student['name']; ?>!</h1>

    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php foreach ($errors as $e) echo "<p class='error'>$e</p>"; ?>

    <h2>Upload Resume</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="resume" required>
        <button type="submit" name="upload_resume">Upload</button>
    </form>

    <?php if (!empty($student['resume'])): ?>
        <p>Your resume: <a href="uploads/<?php echo $student['resume']; ?>" target="_blank">View Resume</a></p>
    <?php endif; ?>

    <h2>Available Jobs</h2>
    <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
        <div class="job">
            <h3><?php echo $job['title']; ?></h3>
            <p><?php echo $job['description']; ?></p>
            <p><strong>Company:</strong> <?php echo $job['company']; ?></p>
            <a class="button" href="?apply=<?php echo $job['id']; ?>">Apply</a>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
