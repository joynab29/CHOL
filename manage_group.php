<?php
session_start();
include 'auth.php';
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($group_id === 0) {
    die("Invalid group ID.");
}

// Fetch group details
$groupQuery = $conn->prepare("SELECT * FROM groups WHERE id = ?");
$groupQuery->bind_param("i", $group_id);
$groupQuery->execute();
$group = $groupQuery->get_result()->fetch_assoc();

if (!$group) {
    die("Group not found.");
}

// Check if user is owner
$isOwner = false;
$ownerCheck = $conn->prepare("SELECT role FROM groupmembers WHERE group_id = ? AND user_id = ?");
$ownerCheck->bind_param("ii", $group_id, $user_id);
$ownerCheck->execute();
$ownerResult = $ownerCheck->get_result()->fetch_assoc();
if ($ownerResult && $ownerResult['role'] === 'owner') {
    $isOwner = true;
}

$message = "";

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add member
    if (isset($_POST['add_member'])) {
        $friend_id = intval($_POST['friend_id']);
        $insertQuery = $conn->prepare("INSERT IGNORE INTO groupmembers (group_id, user_id, role) VALUES (?, ?, 'member')");
        $insertQuery->bind_param("ii", $group_id, $friend_id);
        if ($insertQuery->execute()) {
            $message = "Member added successfully!";
        } else {
            $message = "Error adding member.";
        }
    }

    // Remove member
    if (isset($_POST['remove_member']) && $isOwner) {
        $member_id = intval($_POST['member_id']);
        if ($member_id != $user_id) {
            $removeQuery = $conn->prepare("DELETE FROM groupmembers WHERE group_id = ? AND user_id = ?");
            $removeQuery->bind_param("ii", $group_id, $member_id);
            $removeQuery->execute();
            $message = "Member removed successfully!";
        } else {
            $message = "Owner cannot remove themselves.";
        }
    }

    // Change role
    if (isset($_POST['change_role']) && $isOwner) {
        $member_id = intval($_POST['member_id']);
        $new_role = ($_POST['new_role'] === 'owner') ? 'owner' : 'member';
        if ($member_id != $user_id) {
            $updateQuery = $conn->prepare("UPDATE groupmembers SET role = ? WHERE group_id = ? AND user_id = ?");
            $updateQuery->bind_param("sii", $new_role, $group_id, $member_id);
            $updateQuery->execute();
            $message = "Role updated successfully!";
        }
    }
}

// Fetch members
$membersQuery = $conn->prepare("
    SELECT u.id, u.Username AS username, gm.role
    FROM user u
    INNER JOIN groupmembers gm ON u.id = gm.user_id
    WHERE gm.group_id = ?
");
$membersQuery->bind_param("i", $group_id);
$membersQuery->execute();
$members = $membersQuery->get_result();

// Fetch friends not in this group (status = accepted), prevent duplicates
$friendsQuery = $conn->prepare("
    SELECT DISTINCT u.id, u.Username AS username
    FROM user u
    INNER JOIN friends f ON (f.friend_id = u.id OR f.user_id = u.id)
    WHERE (f.user_id = ? OR f.friend_id = ?)
      AND f.status = 'accepted'
      AND u.id != ?
      AND u.id NOT IN (SELECT user_id FROM groupmembers WHERE group_id = ?)
");
$friendsQuery->bind_param("iiii", $user_id, $user_id, $user_id, $group_id);
$friendsQuery->execute();
$friends = $friendsQuery->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Group - <?= htmlspecialchars($group['name']) ?></title>
    <link rel="stylesheet" href="groups.css">
</head>
<body>
    <div class="topnav">
        <div class="logo">MySocial</div>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="create_group.php">Groups</a></li>
        </ul>
    </div>

    <div class="main-container">
        <!-- Left Panel: Group Members -->
        <div class="panel">
            <h2><?= htmlspecialchars($group['name']) ?></h2>
            <?php if (!empty($message)): ?>
                <p class="success-message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <h3>Group Members</h3>
            <div class="scroll">
                <?php if ($members->num_rows === 0): ?>
                    <p class="muted">No members in this group yet.</p>
                <?php else: ?>
                    <?php while ($row = $members->fetch_assoc()): ?>
                        <div class="card">
                            <span><?= htmlspecialchars($row['username']) ?> (<?= $row['role'] ?>)</span>
                            <?php if ($isOwner && $row['id'] != $user_id): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="member_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="remove_member" class="btn tiny">Remove</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="member_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="new_role" value="<?= ($row['role'] === 'owner') ? 'member' : 'owner' ?>">
                                    <button type="submit" name="change_role" class="btn tiny">
                                        Make <?= ($row['role'] === 'owner') ? 'Member' : 'Owner' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Panel: Add Friends -->
        <div class="panel">
            <h3>Add Friends to Group</h3>
            <div class="scroll">
                <?php if ($friends->num_rows === 0): ?>
                    <p class="muted">No friends available to add.</p>
                <?php else: ?>
                    <?php while ($row = $friends->fetch_assoc()): ?>
                        <div class="card">
                            <span><?= htmlspecialchars($row['username']) ?></span>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="friend_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="add_member" class="btn add">Add</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
