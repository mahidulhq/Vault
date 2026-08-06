<?php
// Technologies Used: PHP PDO, HTML5, CSS3 (Modern Dark Theme, Custom Variables, Glassmorphism), JavaScript
// Functionality: Modernized UI Game Library with live SQL dynamic filtering and local cover image fallback verification.
session_start();
require 'db.php';

// Dynamic Filters Setup
$search = $_GET['search'] ?? '';
$genre_filter = $_GET['genre'] ?? '';
$platform_filter = $_GET['platform'] ?? '';

// Dynamic Query Builder (Limited to 12 games)
$sql = "SELECT g.*, gen.Genre_Name, p.Platform_Name, a.Rating_Name 
        FROM Games g 
        LEFT JOIN Genres gen ON g.Genre_ID = gen.Genre_ID 
        LEFT JOIN Platforms p ON g.Platform_ID = p.Platform_ID 
        LEFT JOIN AgeRatings a ON g.AgeRating_ID = a.AgeRating_ID 
        WHERE 1=1";

$params = [];

if (!empty($search)) {
    $sql .= " AND g.Title LIKE ?";
    $params[] = "%$search%";
}

if (!empty($genre_filter)) {
    $sql .= " AND g.Genre_ID = ?";
    $params[] = $genre_filter;
}

if (!empty($platform_filter)) {
    $sql .= " AND g.Platform_ID = ?";
    $params[] = $platform_filter;
}

// Strictly limit to 12 games
$sql .= " LIMIT 12";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

$genres = $pdo->query("SELECT * FROM Genres")->fetchAll();
$platforms = $pdo->query("SELECT * FROM Platforms")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vault — Next-Gen Game Library</title>
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
        }

        .btn-outline-custom:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-strong);
        }

        .hero-section {
            padding: 3rem 0 1.5rem;
            text-align: center;
            position: relative;
        }

        .hero-title {
            font-weight: 800;
            font-size: 2.75rem;
            margin-bottom: 0.75rem;
            color: var(--text-main);
        }

        .hero-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 550px;
            margin: 0 auto 2rem;
        }

        .filter-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .form-control-custom,
        .form-select-custom {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            border-radius: 12px !important;
            padding: 0.6rem 1rem !important;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            border-color: var(--accent-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important;
        }

        .form-select-custom option {
            background-color: var(--bg-card);
            color: var(--text-main);
        }

        .showcase-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .showcase-wrapper {
            display: flex;
            gap: 1.25rem;
            overflow-x: auto;
            padding: 1rem 0;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .showcase-wrapper::-webkit-scrollbar {
            display: none;
        }

        .scroll-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            background: rgba(24, 30, 34, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .scroll-arrow:hover {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: #FFF;
            transform: translateY(-50%) scale(1.1);
        }

        .scroll-arrow.left {
            left: -20px;
        }

        .scroll-arrow.right {
            right: -20px;
        }

        .portrait-card {
            flex: 0 0 210px;
            height: 330px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 1.25rem;
        }

        .portrait-card:hover {
            transform: translateY(-8px) scale(1.02);
            background: var(--bg-card-hover);
            border-color: var(--accent-primary);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6), 0 0 20px rgba(76, 175, 80, 0.25);
        }

        .portrait-card-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.85;
            transition: transform 0.5s ease, opacity 0.3s ease;
        }

        .portrait-card:hover .portrait-card-bg {
            transform: scale(1.08);
            opacity: 1;
        }

        .portrait-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(16, 20, 22, 0.1) 0%, rgba(16, 20, 22, 0.95) 90%);
            z-index: 1;
        }

        .portrait-card-content {
            position: relative;
            z-index: 2;
        }

        .portrait-title {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.4rem;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .portrait-meta {
            font-size: 0.75rem;
            color: var(--text-secondary);
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }

        .portrait-badge {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            padding: 2px 8px;
            border-radius: 6px;
            color: var(--text-main);
            font-size: 0.7rem;
            font-weight: 600;
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

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <div class="brand-icon">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <span>VAULT</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="btn btn-outline-custom nav-btn" href="profile.php">
                        <i class="fa-regular fa-user text-warning me-1"></i> My Profile (<?= htmlspecialchars($_SESSION['username']); ?>)
                    </a>
                    <a class="btn btn-outline-custom nav-btn" href="logout.php">Logout</a>
                <?php elseif (isset($_SESSION['admin_id'])): ?>
                    <a class="btn btn-gradient nav-btn me-2" href="admin_dashboard.php"><i class="fa-solid fa-gauge me-1"></i> Admin Dashboard</a>
                    <a class="btn btn-outline-custom nav-btn" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="btn btn-outline-custom nav-btn" href="login.php">Log In</a>
                    <a class="btn btn-gradient nav-btn" href="register.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <header class="hero-section">
            <h1 class="hero-title">Game Library Management System</h1>
            <p class="hero-subtitle">Organize, Browse, and Review Your Video Game Collection with Ease.</p>
            <a href="#games-showcase" class="btn btn-gradient nav-btn px-4 py-2">Explore Library <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </header>

        <div class="filter-container mt-4">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-custom" placeholder="Search title..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select name="genre" class="form-select form-select-custom">
                        <option value="">All Genres</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?= $g['Genre_ID'] ?>" <?= $genre_filter == $g['Genre_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['Genre_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="platform" class="form-select form-select-custom">
                        <option value="">All Platforms</option>
                        <?php foreach ($platforms as $p): ?>
                            <option value="<?= $p['Platform_ID'] ?>" <?= $platform_filter == $p['Platform_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['Platform_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-gradient w-100 nav-btn py-2">
                        <i class="fa-solid fa-sliders me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <div id="games-showcase" class="showcase-container">
            <button class="scroll-arrow left" id="scrollLeft" aria-label="Scroll Left">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="showcase-wrapper" id="showcaseWrapper">
                <?php if (!empty($games)): ?>
                    <?php foreach ($games as $game): ?>
                        <?php
                        $cover = trim($game['Cover_Image'] ?? '');
                        $localFile = 'uploads/' . ltrim(str_replace('uploads/', '', $cover), '/');

                        if (!empty($cover) && file_exists(__DIR__ . '/' . $localFile)) {
                            $image_url = $localFile;
                        } else {
                            $image_url = "https://placehold.co/400x600/181E22/4CAF50?text=" . urlencode($game['Title']);
                        }
                        ?>
                        <a href="game_details.php?id=<?= $game['Game_ID'] ?>" class="portrait-card">
                            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($game['Title']) ?>" class="portrait-card-bg" loading="lazy">
                            <div class="portrait-card-overlay"></div>
                            <div class="portrait-card-content">
                                <h5 class="portrait-title" title="<?= htmlspecialchars($game['Title']) ?>"><?= htmlspecialchars($game['Title']) ?></h5>
                                <div class="portrait-meta">
                                    <span class="portrait-badge"><?= htmlspecialchars($game['Genre_Name'] ?? 'Game') ?></span>
                                    <span>• <?= htmlspecialchars($game['Release_Year']) ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="w-100 text-center py-5">
                        <div class="p-4 rounded-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                            <i class="fa-solid fa-ghost fa-2x text-muted mb-2"></i>
                            <h5 class="fw-bold">No Games Found</h5>
                            <p class="text-muted small">Try adjusting your filters or search terms.</p>
                            <a href="index.php" class="btn btn-gradient nav-btn btn-sm mt-1">Reset Filters</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <button class="scroll-arrow right" id="scrollRight" aria-label="Scroll Right">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <footer class="footer-custom text-center">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> built with ❤️</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const container = id => document.getElementById(id);
        const wrapper = container('showcaseWrapper');

        container('scrollLeft').addEventListener('click', () => {
            wrapper.scrollBy({
                left: -300,
                behavior: 'smooth'
            });
        });

        container('scrollRight').addEventListener('click', () => {
            wrapper.scrollBy({
                left: 300,
                behavior: 'smooth'
            });
        });
    </script>
</body>

</html>