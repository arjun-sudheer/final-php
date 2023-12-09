<?php
include('db_config.php');
include('header.php');

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

// Get the user ID of the logged-in user
$user_id = $_SESSION['user_id'];

// Retrieve and display user data from the database
$query = "SELECT * FROM users WHERE id = $user_id"; // Updated column name to 'id'
$result = mysqli_query($db, $query);

if ($result) {
    $user = mysqli_fetch_assoc($result);

    // Handle email update form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newEmail = mysqli_real_escape_string($db, $_POST['new_email']);

        // Update the email in the database
        $updateEmailQuery = "UPDATE users SET email = '$newEmail' WHERE id = $user_id"; // Updated column name to 'id'
        $updateEmailResult = mysqli_query($db, $updateEmailQuery);

        if ($updateEmailResult) {
            // Refresh user data after updating email
            $result = mysqli_query($db, $query);
            $user = mysqli_fetch_assoc($result);
            echo '<p>Email updated successfully!</p>';
        } else {
            echo "Error updating email: " . mysqli_error($db);
        }
    }

    // Handle user deletion
    if (isset($_GET['delete_user_id'])) {
        $deleteUserId = mysqli_real_escape_string($db, $_GET['delete_user_id']);

        // Delete the user from the database
        $deleteUserQuery = "DELETE FROM users WHERE id = $deleteUserId"; // Updated column name to 'id'
        $deleteUserResult = mysqli_query($db, $deleteUserQuery);

        if ($deleteUserResult) {
            // Check if the deleted user is the currently logged-in user
            if ($deleteUserId == $user_id) {
                // If yes, destroy the session and redirect to the login page
                session_destroy();
                header("Location: index.php");
                exit();
            } else {
                echo '<p>User deleted successfully!</p>';
            }
        } else {
            echo "Error deleting user: " . mysqli_error($db);
        }
    }
    
    // Rest of your HTML and PHP code remains unchanged
?>

    <link rel="stylesheet" href="./css/style.css">

    <!-- Display User Data -->
    <h2>Welcome, <?php echo $user['name']; ?>!</h2>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Phone: <?php echo $user['phone']; ?></p>

    <!-- Update Email Section -->
    <h3>Update Email</h3>
    <form method="post" action="">
        <label for="new_email">New Email:</label>
        <input type="email" name="new_email" required>
        <input type="submit" value="Update Email">
    </form>

    <!-- View Users Section -->
    <h3>Registered Users</h3>
    <?php
    // Retrieve all users from the database
    $allUsersQuery = "SELECT * FROM users";
    $allUsersResult = mysqli_query($db, $allUsersQuery);

    if ($allUsersResult) {
        // Display a table of all registered users
        echo '<table border="1">';
        echo '<tr><th>User ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>';
        while ($row = mysqli_fetch_assoc($allUsersResult)) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>'; // Updated column name to 'id'
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td>' . $row['phone'] . '</td>';
            echo '<td><a href="?delete_user_id=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this user?\');">Delete</a></td>'; // Updated to 'id'
        }
        echo '</table>';
    } else {
        echo "Error fetching users: " . mysqli_error($db);
    }

    // Close the result set
    mysqli_free_result($allUsersResult);
    ?>

<?php
} else {
    echo "Error fetching user data: " . mysqli_error($db);
}

include('footer.php');
?>
