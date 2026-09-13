<?php

require_once 'includes/db.php';

/* Create users table */

$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if (!$conn->query($sql)) {
    die("Error creating users table: " . $conn->error);
}


/* Admin login details */

$username = "admin";
$password = "admin123";

$hashed_password = password_hash($password, PASSWORD_DEFAULT);


/* Check whether admin already exists */

$check = $conn->prepare(
    "SELECT user_id FROM users WHERE username = ?"
);

$check->bind_param("s", $username);
$check->execute();

$result = $check->get_result();


if ($result->num_rows == 0) {

    $stmt = $conn->prepare(
        "INSERT INTO users (username, password)
         VALUES (?, ?)"
    );

    $stmt->bind_param(
        "ss",
        $username,
        $hashed_password
    );

    if ($stmt->execute()) {

        echo "<h2>✅ Login account created successfully!</h2>";
        echo "<p>Username: <b>admin</b></p>";
        echo "<p>Password: <b>admin123</b></p>";
        echo "<p>Now go to <a href='login.php'>Login Page</a></p>";

    } else {

        echo "Error creating account: " . $stmt->error;

    }

} else {

    echo "<h2>✅ Admin account already exists!</h2>";
    echo "<p>Username: <b>admin</b></p>";
    echo "<p>Now go to <a href='login.php'>Login Page</a></p>";

}

?>