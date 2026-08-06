<?php
// Technologies Used: PHP File Upload Handling & Prepared SQL Statements (Create Operation)
session_start();
require 'db.php';

// Enable error reporting during debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure only authenticated admins can access this page
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $genre_id = $_POST['genre_id'] ?? null;
    $platform_id = $_POST['platform_id'] ?? null;
    $age_rating_id = $_POST['age_rating_id'] ?? null;
    $release_year = $_POST['release_year'] ?? null;
    $description = trim($_POST['description'] ?? '');

    // Handle Image Upload
    $cover_image = 'default_cover.jpg';
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cover_image']['tmp_name'];
        $fileName = $_FILES['cover_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . md5($fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $cover_image = $newFileName;
            }
        }
    }

    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO Games (Title, Genre_ID, Platform_ID, AgeRating_ID, Release_Year, Description, Cover_Image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $genre_id, $platform_id, $age_rating_id, $release_year, $description, $cover_image]);

        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Title field cannot be empty.";
    }
}

// Fetch option lists from database matching schema.sql
$genres = $pdo->query("SELECT * FROM Genres")->fetchAll();
$platforms = $pdo->query("SELECT * FROM Platforms")->fetchAll();
$ratings = $pdo->query("SELECT * FROM AgeRatings")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Game — Vault</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #101416;
            --bg-card: #181E22;
            --text-main: #F5F7FA;
            --border-color: rgba(76, 175, 80, 0.15);
            --accent-gradient: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        }
        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            padding: 3rem 0;
        }
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #FFF !important;
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4CAF50 !important;
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important;
        }
        .form-select option {
            background-color: #181E22;
            color: #FFF;
        }
        .btn-success-custom {
            background: var(--accent-gradient);
            color: #FFF;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-success-custom:hover {
            color: #FFF;
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0"><i class="fa-solid fa-plus-circle me-2 text-success"></i>Add New Game</h2>
            <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Cancel</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger p-3 mb-4 rounded-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label font-weight-semibold">Game Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Elden Ring" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Genre</label>
                    <select name="genre_id" class="form-select" required>
                        <option value="" disabled selected>Select Genre</option>
                        <?php foreach($genres as $g): ?>
                            <option value="<?= $g['Genre_ID'] ?>"><?= htmlspecialchars($g['Genre_Name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Platform</label>
                    <select name="platform_id" class="form-select" required>
                        <option value="" disabled selected>Select Platform</option>
                        <?php foreach($platforms as $p): ?>
                            <option value="<?= $p['Platform_ID'] ?>"><?= htmlspecialchars($p['Platform_Name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Age Rating</label>
                    <select name="age_rating_id" class="form-select" required>
                        <option value="" disabled selected>Select Rating</option>
                        <?php foreach($ratings as $r): ?>
                            <option value="<?= $r['AgeRating_ID'] ?>"><?= htmlspecialchars($r['Rating_Name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Release Year</label>
                <input type="number" name="release_year" class="form-control" placeholder="e.g. 2024" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Brief overview of the game..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Cover Image</label>
                <input type="file" name="cover_image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-success-custom w-100 py-2">Save Game Record</button>
        </form>
    </div>
</div>

</body>
</html>