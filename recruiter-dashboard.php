<?php
session_start();
include('db.php'); // your DB connection file

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'recruiter') {
    header("Location: login.php");
    exit();
}

echo "<div style='background:#e0f7fa;padding:20px;border-radius:8px;'>Welcome, <b>" . $_SESSION['username'] . "</b>!<br>Your role: Recruiter<br><br>
<a href='profile.php'>My Profile</a> | <a href='logout.php'>Logout</a></div><br>";

// =============== HANDLE JOB POSTING ===============
if (isset($_POST['submit_job'])) {
    $job_title = $_POST['job_title'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($job_title && $description) {
        $sql = "INSERT INTO jobs (title, description) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $job_title, $description);
        $stmt->execute();
        echo "<p style='color:green;'>Job posted successfully!</p>";
    } else {
        echo "<p style='color:red;'>Please fill all job fields!</p>";
    }
}

// =============== HANDLE STUDENT ACCEPT/REJECT ===============
if (isset($_POST['action']) && isset($_POST['student_id'])) {
    $action = $_POST['action']; // 'accept' or 'reject'
    $student_id = $_POST['student_id'];

    if ($action == 'accept' || $action == 'reject') {
        $status = ($action == 'accept') ? 'Accepted' : 'Rejected';
        $sql = "UPDATE students SET status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $student_id);
        $stmt->execute();

        echo "<p style='color:blue;'>Student status updated to: $status</p>";
    }
}

// =============== DISPLAY APPLICATIONS ===============
$result = $conn->query("SELECT * FROM students");
?>

<h2>Recruiter Dashboard</h2>

<!-- Post a Job -->
<form method="POST" action="">
    <h3>Post a Job</h3>
    <input type="text" name="job_title" placeholder="Job Title" required><br>
    <textarea name="description" placeholder="Job Description" required></textarea><br>
    <button type="submit" name="submit_job">Post Job</button>
</form>

<hr>

<!-- View All Applications -->
<h3>View All Applications</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Student Name</th>
        <th>Resume</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><a href="<?= $row['resume'] ?>" target="_blank">View Resume</a></td>
            <td><?= $row['status'] ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="student_id" value="<?= $row['id'] ?>">
                    <button name="action" value="accept">Accept</button>
                    <button name="action" value="reject">Reject</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
