<?php
session_start();
require 'db_connect.php';

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle delete user
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin-panel.php");
    exit;
}

// Handle role change
if (isset($_POST['change_role'])) {
    $id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
    $stmt->bind_param("si", $new_role, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin-panel.php");
    exit;
}

// Fetch all users
$result = $conn->query("SELECT id, name, email, role, created_at FROM users");
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Panel</title></head>
<body>
<h2>Admin Panel</h2>
<p><a href="logout.php">Logout</a></p>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f1f2f6;
    padding: 20px;
}

h2 {
    color: #2d3436;
}

table {
    width: 90%;
    max-width: 800px;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #ffffff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

table th, table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dfe6e9;
}

table th {
    background-color: #0984e3;
    color: white;
}

tr:hover {
    background-color: #dfe6e9;
}

button {
    background-color: #d63031;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #e17055;
}
</style>


<table border="1" cellpadding="5" cellspacing="0">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Created At</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td>
        <form method="post" style="margin:0;">
            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
            <select name="role">
                <option value="student" <?php if($row['role']=='student') echo 'selected'; ?>>Student</option>
                <option value="recruiter" <?php if($row['role']=='recruiter') echo 'selected'; ?>>Recruiter</option>
                <option value="admin" <?php if($row['role']=='admin') echo 'selected'; ?>>Admin</option>
            </select>
            <button type="submit" name="change_role">Change</button>
        </form>
    </td>
    <td><?php echo $row['created_at']; ?></td>
    <td>
        <a href="admin-panel.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this user?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
