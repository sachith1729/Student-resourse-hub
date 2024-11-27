<?php
// Include database connection
include 'db_connection.php'; // Replace this with your actual database connection file

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variables for authenticated user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                // Redirect to the dashboard or homepage
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "Email not found. Please register first.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login-styles.css">
    <script src="login-scripts.js" defer></script>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</body>
</html>
