<?php
require 'db.php';

$username = 'admin';
$email = 'admin@vault.com';
$password = password_hash('admin123', PASSWORD_BCRYPT);

// Clear old admin and insert new hash
$pdo->exec("TRUNCATE TABLE admins");
$stmt = $pdo->prepare("INSERT INTO admins (Username, Email, Password) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $password]);

echo "Admin account created successfully! Try logging in now with username: <strong>admin</strong> and password: <strong>admin123</strong>";