<?php
// Technologies Used: PHP Sessions, PDO Prepared Statements, SQL JOIN operations
session_start();
require 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Fetch User Information
$stmt = $pdo->prepare("SELECT User_ID, Username, Email FROM Users WHERE User_ID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// 2. Handle Favorite Removal
if (isset($_POST['remove_favorite_id'])) {
    $game_to_remove = $_POST['remove_favorite_id'];
    $del_stmt = $pdo->prepare("DELETE FROM Favorites WHERE User_ID = ? AND Game_ID = ?");
    $del_stmt->execute([$user_id, $game_to_remove]);
    header("Location: profile.php");
    exit;
}

// 3. Fetch User's Favorites
$fav_stmt = $pdo->prepare("
    SELECT g.*, gen.Genre_Name, p.Platform_Name, f.Date_Added
    FROM Favorites f
    JOIN Games g ON f.Game_ID = g.Game_ID
    LEFT JOIN Genres gen ON g.Genre_ID = gen.Genre_ID
    LEFT JOIN Platforms p ON g.Platform_ID = p.Platform_ID
    WHERE f.User_ID = ?
    ORDER BY f.Date_Added DESC
");
$fav_stmt->execute([$user_id]);
$favorites = $fav_stmt->fetchAll();

// 4. Fetch User's Reviews Given
$rev_stmt = $pdo->prepare("
    SELECT r.*, g.Title as Game_Title, g.Cover_Image
    FROM Reviews r
    JOIN Games g ON r.Game_ID = g.Game_ID
    WHERE r.User_ID = ?
    ORDER BY r.Created_At DESC
");
$rev_stmt->execute([$user_id]);
$reviews = $rev_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — Vault</title>
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

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            color: #ffffff;
        }

        .nav-btn {
            border-radius: 12px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-gradient {
            background: var(--accent-gradient);
            color: #FFF !important;
            border: none;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            opacity: 0.95;
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .btn-outline-custom {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            color: var(--text-main) !important;
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .btn-outline-custom:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-strong);
        }

        .profile-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .avatar-placeholder {
            width: 76px;
            height: 76px;
            border-radius: 18px;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 800;
            color: #FFFFFF;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
        }

        .nav-tabs .nav-link {
            color: var(--text-muted);
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0;
        }

        .nav-tabs .nav-link.active {
            color: var(--text-main);
            background: transparent;
            border-bottom: 3px solid var(--accent-primary);
        }

        .portrait-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .portrait-card:hover {
            border-color: var(--accent-primary);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        .portrait-card-bg {
            height: 220px;
            width: 100%;
            object-fit: cover;
        }

        .badge-custom {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            padding: 3px 10px;
            border-radius: 6px;
            color: var(--text-main);
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* Rounded Action Buttons matching site themes */
        .btn-remove {
            background: rgba(211, 47, 47, 0.15);
            color: #EF9A9A;
            border: 1px solid rgba(211, 47, 47, 0.3);
            border-radius: 12px;
            padding: 0.4rem 0.8rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-remove:hover {
            background: var(--accent-danger);
            color: #FFF;
            border-color: var(--accent-danger);
            transform: translateY(-2px);
        }

        .review-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.25rem;
        }

        .star-rating {
            color: var(--accent-warning);
        }

        .footer-custom {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 0;
            margin-top: auto;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <div class="brand-icon">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <span>VAULT</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a class="btn btn-outline-custom nav-btn" href="index.php"><i class="fa-solid fa-house me-1"></i> Library</a>
                <a class="btn btn-outline-custom nav-btn" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <!-- Profile Header Section -->
        <div class="profile-card">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="avatar-placeholder">
                        <?= strtoupper(substr($user['Username'], 0, 1)) ?>
                    </div>
                </div>
                <div class="col">
                    <h1 class="h3 fw-bold mb-1 text-white"><?= htmlspecialchars($user['Username']) ?></h1>
                    <p class="mb-0" style="color: var(--text-muted);"><i class="fa-regular fa-envelope me-2"></i><?= htmlspecialchars($user['Email']) ?></p>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites" type="button">
                    <i class="fa-solid fa-heart me-2 text-danger"></i>My Favorites (<?= count($favorites) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                    <i class="fa-solid fa-star me-2 text-warning"></i>My Reviews Given (<?= count($reviews) ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabsContent">
            <!-- Tab 1: Favorites -->
            <div class="tab-pane fade show active" id="favorites" role="tabpanel">
                <?php if (empty($favorites)): ?>
                    <div class="text-center py-5 rounded-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                        <i class="fa-regular fa-heart fa-2x text-muted mb-2"></i>
                        <h5 class="fw-bold text-white">No Favorite Games Saved Yet</h5>
                        <p class="text-muted small mb-3">Explore the library to save games to your personal list.</p>
                        <a href="index.php" class="btn btn-gradient nav-btn btn-sm">Browse Games</a>
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                        <?php foreach ($favorites as $game): 
                            $cover = trim($game['Cover_Image'] ?? '');
                            $localFile = 'uploads/' . ltrim(str_replace('uploads/', '', $cover), '/');

                            if (!empty($cover) && file_exists(__DIR__ . '/' . $localFile)) {
                                $coverPath = $localFile;
                            } else {
                                $coverPath = "https://placehold.co/400x600/181E22/4CAF50?text=" . urlencode($game['Title']);
                            }
                        ?>
                            <div class="col">
                                <div class="portrait-card">
                                    <img src="<?= htmlspecialchars($coverPath) ?>" class="portrait-card-bg" alt="<?= htmlspecialchars($game['Title']) ?>">
                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <div class="mb-2">
                                            <?php if (!empty($game['Genre_Name'])): ?>
                                                <span class="badge-custom"><?= htmlspecialchars($game['Genre_Name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="h6 fw-bold text-white mb-1 text-truncate"><?= htmlspecialchars($game['Title']) ?></h3>
                                        <p class="small mb-3 flex-grow-1" style="color: var(--text-muted);">Released: <?= htmlspecialchars($game['Release_Year']) ?></p>
                                        <div class="d-flex gap-2 align-items-center">
                                            <a href="game_details.php?id=<?= $game['Game_ID'] ?>" class="btn btn-outline-custom nav-btn btn-sm flex-grow-1 text-center py-1">View Details</a>
                                            <form method="POST" onsubmit="return confirm('Remove game from favorites?');" class="m-0">
                                                <input type="hidden" name="remove_favorite_id" value="<?= $game['Game_ID'] ?>">
                                                <button type="submit" class="btn btn-remove" title="Remove Favorite">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 2: Reviews -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <?php if (empty($reviews)): ?>
                    <div class="text-center py-5 rounded-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                        <i class="fa-regular fa-comment fa-2x text-muted mb-2"></i>
                        <h5 class="fw-bold text-white">You Haven't Left Any Reviews Yet</h5>
                        <p class="text-muted small">Visit game details pages to rate and write reviews.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($reviews as $rev): 
                            $cover = trim($rev['Cover_Image'] ?? '');
                            $localFile = 'uploads/' . ltrim(str_replace('uploads/', '', $cover), '/');

                            if (!empty($cover) && file_exists(__DIR__ . '/' . $localFile)) {
                                $revCoverPath = $localFile;
                            } else {
                                $revCoverPath = "https://placehold.co/200x200/181E22/4CAF50?text=" . urlencode($rev['Game_Title']);
                            }
                        ?>
                            <div class="review-card">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="<?= htmlspecialchars($revCoverPath) ?>" width="60" height="60" style="object-fit:cover; border-radius:12px;" alt="Game Cover">
                                    </div>
                                    <div class="col">
                                        <h3 class="h6 fw-bold text-white mb-1"><?= htmlspecialchars($rev['Game_Title']) ?></h3>
                                        <div class="star-rating small mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa-<?= $i <= $rev['Rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2 small" style="color: var(--text-muted);"><?= date('M d, Y', strtotime($rev['Created_At'])) ?></span>
                                        </div>
                                        <p class="mb-0 small" style="color: var(--text-secondary);"><?= nl2br(htmlspecialchars($rev['Review_Text'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer-custom text-center">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> built with ❤️</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>