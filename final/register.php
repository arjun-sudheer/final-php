<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
include('db_config.php'); // Include the file with your database configuration
include('header.php'); // Include the header file

// Function to sanitize and validate input
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize variables for form validation
$nameErr = $emailErr = $phoneErr = $passwordErr = $imageErr = "";
$name = $email = $phone = $password = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and process registration data
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $phone = test_input($_POST['phone']);
    $password = test_input($_POST['password']);

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Validate Name
    if (empty($name)) {
        $nameErr = "Name is required";
    }

    // Validate Email
    if (empty($email)) {
        $emailErr = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }

    // Validate Phone
    if (empty($phone)) {
        $phoneErr = "Phone is required";
    }

    // Validate Password
    if (empty($password)) {
        $passwordErr = "Password is required";
    }

    // Check if email is unique (you should check against a database)
    // Example: Assuming $db is your database connection
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $emailErr = "Email address is already taken";
    }

    // Handle image upload
    $targetDir = "uploads/";  // Set your desired upload directory
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if (!empty($_FILES["image"]["tmp_name"])) {
        // Check if the uploaded file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $imageErr = "File is not an image.";
        } elseif ($_FILES["image"]["size"] > 500000) { // Check file size
            $imageErr = "Sorry, your file is too large.";
        } elseif (!in_array($imageFileType, array("jpg", "jpeg", "png", "gif"))) { // Allow only certain file formats
            $imageErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) { // If there are no errors, move the uploaded file
            // File uploaded successfully
            // You can save the file path in the database if needed
        } else {
            $imageErr = "Sorry, there was an error uploading your file.";
        }
    }

    // Save the user information to the database (using prepared statements to prevent SQL injection)
    $query = "INSERT INTO users (name, email, phone, password, image_path) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sssss", $name, $email, $phone, $hashedPassword, $targetFile);
        $stmt->execute();

        // Check for SQL query execution errors
        if ($stmt->error) {
            echo "Error: " . $stmt->error;
        } else {
            // Redirect to a success page or login page
            header("Location: index.php");
            exit();
        }

        $stmt->close();
    } else {
        echo "Error: " . $db->error;
    }
}
?>

<!-- Include your stylesheet -->
<link rel="stylesheet" href="./css/style.css">

<!-- Your Registration Form Here -->
<h2>Register</h2>
<form method="post" action="register.php" enctype="multipart/form-data">
    <!-- Your registration form fields here -->
    <label for="name">Name:</label>
    <input type="text" name="name" value="<?php echo $name; ?>">
    <span class="error"><?php echo $nameErr; ?></span>

    <label for="email">Email:</label>
    <input type="text" name="email" value="<?php echo $email; ?>">
    <span class="error"><?php echo $emailErr; ?></span>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" value="<?php echo $phone; ?>">
    <span class="error"><?php echo $phoneErr; ?></span>

    <label for="password">Password:</label>
    <input type="password" name="password">
    <span class="error"><?php echo $passwordErr; ?></span>

    <label for="image">Profile Image:</label>
    <input type="file" name="image">
    <span class="error"><?php echo $imageErr; ?></span>

    <input type="submit" value="Register">
</form>

<?php
include('footer.php');
?>
