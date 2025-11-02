<?php
include('config.php'); // connect to database

// Fetch all job applications
$sql = "SELECT a.application_id, a.applied_on, s.name AS student_name, 
               s.email AS student_email, s.resume, j.job_title 
        FROM applications a
        JOIN students s ON a.student_id = s.id
        JOIN jobs j ON a.job_id = j.id
        ORDER BY a.applied_on DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Applications</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f5f5f5;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        a {
            color: blue;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>All Job Applications</h2>
<table>
    <tr>
        <th>Student Name</th>
        <th>Email</th>
        <th>Job Title</th>
        <th>Resume</th>
        <th>Applied On</th>
    </tr>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['student_name']}</td>
                    <td>{$row['student_email']}</td>
                    <td>{$row['job_title']}</td>
                    <td><a href='uploads/{$row['resume']}' target='_blank'>View Resume</a></td>
                    <td>{$row['applied_on']}</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No applications yet.</td></tr>";
    }
    ?>
</table>

</body>
</html>
