<?php
session_start();
require 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure only authenticated admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$game_id = $_GET['id'] ?? null;

if (!$game_id) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';

// 1. Fetch game details
$stmt = $pdo->prepare("SELECT * FROM Games WHERE Game_ID = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch();

if (!$game) {
    die("Game not found!");
}

// 2. Fetch dropdown option lists directly matching schema.sql
$genres = $pdo->query("SELECT * FROM Genres")->fetchAll();
$platforms = $pdo->query("SELECT * FROM Platforms")->fetchAll();
$ageRatings = $pdo->query("SELECT * FROM AgeRatings")->fetchAll();

// 3. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $genre_id = $_POST['genre_id'];
    $platform_id = $_POST['platform_id'];
    $agerating_id = $_POST['agerating_id'];
    $release_year = $_POST['release_year'];
    $description = trim($_POST['description']);
    $cover_image = $game['Cover_Image']; // Default to existing cover filename

    // Handle Image Upload if a new file was chosen
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cover_image']['tmp_name'];
        $fileName = $_FILES['cover_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
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
        // Precise SQL query matching schema.sql column names
        $updateStmt = $pdo->prepare("
            UPDATE Games 
            SET Title = ?, Genre_ID = ?, Platform_ID = ?, AgeRating_ID = ?, Release_Year = ?, Description = ?, Cover_Image = ? 
            WHERE Game_ID = ?
        ");
        $updateStmt->execute([$title, $genre_id, $platform_id, $agerating_id, $release_year, $description, $cover_image, $game_id]);
        
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Title field cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Game — Vault</title>
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
        }
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #FFF !important;
            border-radius: 10px;
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
        }
        .btn-success-custom:hover {
            color: #FFF;
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Edit Game #<?= htmlspecialchars($game['Game_ID']) ?></h2>
            <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Cancel</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Game Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($game['Title']) ?>" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Genre</label>
                    <select name="genre_id" class="form-select">
                        <?php foreach ($genres as $g): ?>
                            <option value="<?= $g['Genre_ID'] ?>" <?= $g['Genre_ID'] == $game['Genre_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['Genre_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Platform</label>
                    <select name="platform_id" class="form-select">
                        <?php foreach ($platforms as $p): ?>
                            <option value="<?= $p['Platform_ID'] ?>" <?= $p['Platform_ID'] == $game['Platform_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['Platform_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Age Rating</label>
                    <select name="agerating_id" class="form-select">
                        <?php foreach ($ageRatings as $r): ?>
                            <option value="<?= $r['AgeRating_ID'] ?>" <?= $r['AgeRating_ID'] == $game['AgeRating_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['Rating_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Release Year</label>
                <input type="number" name="release_year" class="form-control" value="<?= htmlspecialchars($game['Release_Year']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($game['Description'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Update Cover Image (Optional)</label>
                <input type="file" name="cover_image" class="form-control">
                <small class="text-muted mt-1 d-block">Current: <?= htmlspecialchars($game['Cover_Image']) ?></small>
            </div>

            <button type="submit" class="btn btn-success-custom w-100 py-2">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>