<?php
// Technologies Used: PHP, MySQL, Bootstrap 5, FontAwesome
// Functionality: Read details, Write user reviews, Toggle favorites (Create/Delete)
session_start();
require 'db.php';

$game_id = $_GET['id'] ?? null;
if (!$game_id) { header('Location: index.php'); exit; }

// Handle Favorite Toggle
if (isset($_POST['toggle_favorite']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check = $pdo->prepare("SELECT * FROM Favorites WHERE User_ID = ? AND Game_ID = ?");
    $check->execute([$user_id, $game_id]);
    
    if ($check->rowCount() > 0) {
        $del = $pdo->prepare("DELETE FROM Favorites WHERE User_ID = ? AND Game_ID = ?");
        $del->execute([$user_id, $game_id]);
    } else {
        $ins = $pdo->prepare("INSERT INTO Favorites (User_ID, Game_ID) VALUES (?, ?)");
        $ins->execute([$user_id, $game_id]);
    }
}

// Handle Review Submission
if (isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $rating = $_POST['rating'];
    $text = $_POST['review_text'];
    $user_id = $_SESSION['user_id'];
    
    $ins_rev = $pdo->prepare("INSERT INTO Reviews (Game_ID, User_ID, Rating, Review_Text) VALUES (?, ?, ?, ?)");
    $ins_rev->execute([$game_id, $user_id, $rating, $text]);
}

// Fetch Game Data
$stmt = $pdo->prepare("SELECT g.*, gen.Genre_Name, p.Platform_Name, a.Rating_Name 
                        FROM Games g 
                        LEFT JOIN Genres gen ON g.Genre_ID = gen.Genre_ID 
                        LEFT JOIN Platforms p ON g.Platform_ID = p.Platform_ID 
                        LEFT JOIN AgeRatings a ON g.AgeRating_ID = a.AgeRating_ID 
                        WHERE g.Game_ID = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch();

if (!$game) { header('Location: index.php'); exit; }

// Fetch Reviews
$rev_stmt = $pdo->prepare("SELECT r.*, u.Username FROM Reviews r JOIN Users u ON r.User_ID = u.User_ID WHERE r.Game_ID = ? ORDER BY r.Created_At DESC");
$rev_stmt->execute([$game_id]);
$reviews = $rev_stmt->fetchAll();

// Check if Favorited
$is_favorite = false;
if (isset($_SESSION['user_id'])) {
    $fav_check = $pdo->prepare("SELECT * FROM Favorites WHERE User_ID = ? AND Game_ID = ?");
    $fav_check->execute([$_SESSION['user_id'], $game_id]);
    $is_favorite = $fav_check->rowCount() > 0;
}

// Dynamic image handler
$cover = $game['Cover_Image'] ?? '';
if (!empty($cover) && strpos($cover, 'http') === 0) {
    $image_url = $cover;
} elseif (!empty($cover) && file_exists("uploads/" . $cover)) {
    $image_url = "uploads/" . $cover;
} else {
    $image_url = "https://placehold.co/600x800/181E22/4CAF50?text=" . urlencode($game['Title']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($game['Title']) ?> — Vault</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #101416;
            --bg-secondary: #141A1D;
            --bg-card: #181E22;
            --bg-card-hover: #232A30;
            --text-main: #F5F7FA;
            --text-secondary: #D6DCE2;
            --text-muted: #9AA5B1;
            --border-color: rgba(76, 175, 80, 0.15);
            --border-strong: rgba(76, 175, 80, 0.30);
            --accent-primary: #4CAF50;
            --accent-secondary: #7CB342;
            --accent-tertiary: #A5D66F;
            --accent-gradient: linear-gradient(135deg, #4CAF50 0%, #66BB6A 33%, #7CB342 66%, #7CB342 100%);
            --accent-success: #43A047;
            --accent-warning: #F9A825;
            --accent-danger: #D32F2F;
            --accent-info: #26A69A;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .glass-card p.text-muted, 
        .review-card p.text-muted {
            color: var(--text-secondary) !important;
        }

        .glass-card .badge.text-muted {
            color: var(--text-main) !important;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-wrapper {
            flex: 1 0 auto;
        }

        .navbar-custom {
            background: rgba(16, 20, 22, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            color: var(--text-main) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--accent-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            box-shadow: 0 4px 20px rgba(76, 175, 80, 0.3);
        }

        .nav-btn {
            border-radius: 12px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-gradient {
            background: var(--accent-gradient);
            color: #FFF !important;
            border: none;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-gradient:hover {
            opacity: 0.95;
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
            color: #FFF;
        }

        .btn-outline-custom {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            color: var(--text-main) !important;
        }

        .btn-outline-custom:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-strong);
        }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .cover-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .cover-wrapper img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }

        .badge-pill {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-genre {
            background: rgba(76, 175, 80, 0.15);
            color: var(--accent-tertiary);
            border: 1px solid var(--border-strong);
        }

        .badge-platform {
            background: rgba(38, 166, 154, 0.15);
            color: var(--accent-info);
            border: 1px solid rgba(38, 166, 154, 0.3);
        }

        .badge-rating {
            background: rgba(249, 168, 37, 0.15);
            color: var(--accent-warning);
            border: 1px solid rgba(249, 168, 37, 0.3);
        }

        .btn-fav-active {
            background: rgba(211, 47, 47, 0.15);
            border: 1px solid rgba(211, 47, 47, 0.4);
            color: #EF9A9A;
        }

        .btn-fav-active:hover {
            background: rgba(211, 47, 47, 0.3);
            color: #FFF;
        }

        .form-control-custom, .form-select-custom {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            border-radius: 12px !important;
            padding: 0.8rem 1rem !important;
            font-size: 0.95rem;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            border-color: var(--accent-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important;
        }

        .form-select-custom option {
            background-color: var(--bg-card);
            color: var(--text-main);
        }

        .review-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: border-color 0.3s ease;
        }

        .review-card:hover {
            border-color: var(--border-strong);
        }

        .star-rating {
            color: var(--accent-warning);
            font-size: 0.85rem;
        }

        .text-accent {
            color: var(--accent-primary) !important;
        }

        .footer-custom {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 0;
            margin-top: 8rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <div class="main-wrapper">
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <div class="brand-icon">
                        <i class="fa-solid fa-gamepad"></i>
                    </div>
                    <span>VAULT</span>
                </a>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a class="btn btn-outline-custom nav-btn" href="profile.php">
                            <i class="fa-regular fa-user text-warning me-1"></i> My Profile (<?= htmlspecialchars($_SESSION['username']); ?>)
                        </a>
                        <a class="btn btn-outline-custom nav-btn" href="logout.php">Logout</a>
                    <?php elseif(isset($_SESSION['admin_id'])): ?>
                        <a class="btn btn-gradient nav-btn me-2" href="admin_dashboard.php"><i class="fa-solid fa-gauge me-1"></i> Admin Dashboard</a>
                        <a class="btn btn-outline-custom nav-btn" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="btn btn-outline-custom nav-btn" href="login.php">Log In</a>
                        <a class="btn btn-gradient nav-btn" href="register.php">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="container my-5">
            <a href="index.php" class="btn btn-outline-custom nav-btn mb-4 d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Library
            </a>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="cover-wrapper mb-3">
                        <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($game['Title']) ?>" referrerpolicy="no-referrer">
                    </div>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form method="POST">
                            <button type="submit" name="toggle_favorite" class="btn w-100 nav-btn py-3 d-flex align-items-center justify-content-center gap-2 <?= $is_favorite ? 'btn-fav-active' : 'btn-gradient' ?>">
                                <i class="<?= $is_favorite ? 'fa-solid' : 'fa-regular' ?> fa-heart fs-5"></i>
                                <?= $is_favorite ? 'Remove from Favorites' : 'Add to Favorites' ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="col-lg-8">
                    <div class="glass-card mb-4">
                        <h1 class="fw-bold mb-3" style="font-size: 2.25rem; letter-spacing: -0.5px;"><?= htmlspecialchars($game['Title']) ?></h1>
                        
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="badge-pill badge-genre"><i class="fa-solid fa-layer-group me-1"></i> <?= htmlspecialchars($game['Genre_Name'] ?? 'General') ?></span>
                            <span class="badge-pill badge-platform"><i class="fa-solid fa-desktop me-1"></i> <?= htmlspecialchars($game['Platform_Name'] ?? 'Multiplatform') ?></span>
                            <span class="badge-pill badge-rating"><i class="fa-solid fa-shield-halved me-1"></i> Rating: <?= htmlspecialchars($game['Rating_Name'] ?? 'ESRB') ?></span>
                            <span class="badge-pill btn-outline-custom"><i class="fa-regular fa-calendar me-1"></i> Released: <?= htmlspecialchars($game['Release_Year']) ?></span>
                        </div>

                        <h5 class="fw-bold text-light mb-2"><i class="fa-solid fa-align-left text-accent me-2"></i>Description</h5>
                        <p class="text-muted leading-relaxed mb-0" style="font-size: 1rem; line-height: 1.7;">
                            <?= nl2br(htmlspecialchars($game['Description'] ?? 'No description available for this title.')) ?>
                        </p>
                    </div>

                    <div class="glass-card">
                        <h4 class="fw-bold mb-4 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-comments text-accent"></i> User Reviews 
                            <span class="badge rounded-pill bg-dark text-muted border border-secondary fs-6 ms-2"><?= count($reviews) ?></span>
                        </h4>

                        <?php if(isset($_SESSION['user_id'])): ?>
                            <form method="POST" class="p-4 rounded-4 mb-4" style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color);">
                                <h5 class="fw-bold mb-3 fs-6">Leave a Review</h5>
                                <div class="mb-3">
                                    <label class="form-label text-muted fs-7">Rating</label>
                                    <select name="rating" class="form-select form-select-custom" required>
                                        <option value="5">★★★★★ (5/5 Stars)</option>
                                        <option value="4">★★★★☆ (4/5 Stars)</option>
                                        <option value="3">★★★☆☆ (3/5 Stars)</option>
                                        <option value="2">★★☆☆☆ (2/5 Stars)</option>
                                        <option value="1">★☆☆☆☆ (1/5 Star)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted fs-7">Your Feedback</label>
                                    <textarea name="review_text" class="form-control form-control-custom" rows="3" placeholder="Share your experience playing this game..." required></textarea>
                                </div>
                                <button type="submit" name="submit_review" class="btn btn-gradient nav-btn px-4">
                                    Submit Review <i class="fa-solid fa-paper-plane ms-1"></i>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if(count($reviews) > 0): ?>
                            <?php foreach($reviews as $r): ?>
                                <div class="review-card">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-bold mb-0 text-light"><i class="fa-regular fa-circle-user me-2" style="color: var(--accent-warning);"></i><?= htmlspecialchars($r['Username']) ?></h6>
                                        <div class="star-rating">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i class="<?= $i <= $r['Rating'] ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-2" style="font-size: 0.95rem;"><?= htmlspecialchars($r['Review_Text']) ?></p>
                                    <small class="text-muted opacity-75" style="font-size: 0.75rem;"><i class="fa-regular fa-clock me-1"></i><?= htmlspecialchars($r['Created_At']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fa-regular fa-comment-dots fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0">No reviews yet. Be the first to review this game!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer-custom text-center">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> built with ❤️</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>