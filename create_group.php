<?php
session_start();
include 'auth.php';
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$me = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['group_name'])) {
        $group_name = trim($_POST['group_name']);

        // Check if group name already exists for this user
        $check = $conn->prepare("SELECT id FROM groups WHERE created_by = ? AND name = ?");
        $check->bind_param("is", $me, $group_name);
        $check->execute();
        $exists = $check->get_result();
        if ($exists->num_rows > 0) {
            $error = "You already have a group with this name.";
        } else {
            // Insert group
            $stmt = $conn->prepare("INSERT INTO groups (name, created_by) VALUES (?, ?)");
            $stmt->bind_param("si", $group_name, $me);
            if ($stmt->execute()) {
                $group_id = $conn->insert_id;

                // Add owner to groupmembers
                $stmt2 = $conn->prepare("INSERT INTO groupmembers (group_id, user_id, role) VALUES (?, ?, 'owner')");
                $stmt2->bind_param("ii", $group_id, $me);
                $stmt2->execute();

                $success = "Group created successfully!";
            } else {
                $error = "Failed to create group.";
            }
        }
    } else {
        $error = "Group name cannot be empty.";
    }
}

// Fetch groups
$query = $conn->prepare("
    SELECT g.id, g.name, COUNT(gm.user_id) AS members
    FROM groups g
    LEFT JOIN groupmembers gm ON g.id = gm.group_id
    WHERE g.created_by = ?
    GROUP BY g.id
");
$query->bind_param("i", $me);
$query->execute();
$groups = $query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Groups</title>
    <link rel="stylesheet" href="groups.css">
</head>
<body>
<div class="topnav">
    <div class="logo">MySocial</div>
    <a href="home.php" class="btn">Home</a>
</div>
<div class="main-container">
    <!-- Left Panel -->
    <div class="panel">
        <h2>Create New Group</h2>
        <form method="post" class="create-group-form">
            <input type="text" name="group_name" placeholder="Enter group name" required>
            <button type="submit" class="btn">Create</button>
        </form>
        <?php if ($success): ?><p class="success-message"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error-message"><?= $error ?></p><?php endif; ?>
    </div>

    <!-- Right Panel -->
    <div class="panel">
        <h2>My Groups</h2>
        <div class="scroll">
            <?php if ($groups->num_rows == 0): ?>
                <p class="muted">No groups created yet.</p>
            <?php else: ?>
                <?php while ($row = $groups->fetch_assoc()): ?>
                    <div class="card">
                        <span><?= htmlspecialchars($row['name']) ?> (<?= $row['members'] ?> members)</span>
                        <a href="manage_group.php?id=<?= $row['id'] ?>" class="btn">Manage</a>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
