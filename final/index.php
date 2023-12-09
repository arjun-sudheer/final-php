<?php
include('db_config.php');
include('header.php');

// Clear any existing session data
session_start();
session_destroy();
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: content.php"); // Redirect to the content page if logged in
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate login credentials
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        // Verify the password
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];

            // Redirect to the content page
            header("Location: content.php");
            exit();
        } else {
            // Display an error message for incorrect password
            $error_message = "Invalid password";
        }
    } else {
        // Display an error message for invalid email
        $error_message = "Invalid email";
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
}
?>

<!-- Your Home Page Content Here -->
<h2>Welcome to the Home Page!</h2>

<!-- Login Form -->
<form method="post" action="">
    <label for="email">Username:</label>
    <input type="text" name="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" required>

    <input type="submit" value="Login">
</form>
<!-- Display error message  -->
<?php if (isset($error_message)) { ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php } ?>

<?php
include('footer.php');
?>
