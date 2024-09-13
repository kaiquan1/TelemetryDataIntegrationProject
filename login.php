<?php
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch users data from a file (you can replace this with database logic)
    $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Validate user credentials
    foreach ($users as $user) {
        list($storedUser, $storedPass) = explode(':', $user);
        if ($storedUser === $username && $storedPass === $password) {
            // Correct username and password, start a session
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        }
    }

    // If credentials are invalid, redirect back to the login page with an error
    header("Location: index.html?error=1");
    exit();
}
?>
