<?php
// Technologies Used: PHP Admin Panel & Management Controls
// Operations: Read, Trigger Update, Trigger Delete across records
session_start();
require 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$games = $pdo->query("SELECT g.*, gen.Genre_Name, p.Platform_Name FROM Games g LEFT JOIN Genres gen ON g.Genre_ID = gen.Genre_ID LEFT JOIN Platforms p ON g.Platform_ID = p.Platform_ID")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Vault</title>
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

        .custom-table {
            color: var(--text-main);
            vertical-align: middle;
            margin-bottom: 0;
        }

        .custom-table th {
            background-color: rgba(255, 255, 255, 0.02);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
        }

        .custom-table td {
            background-color: transparent;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
        }

        .custom-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: var(--bg-card-hover);
        }

        .cover-thumb {
            width: 50px;
            height: 65px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .badge-pill {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 5px 12px;
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

        .badge-id {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--accent-primary);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .btn-action-edit {
            background: rgba(249, 168, 37, 0.15);
            border: 1px solid rgba(249, 168, 37, 0.3);
            color: var(--accent-warning) !important;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-action-edit:hover {
            background: rgba(249, 168, 37, 0.3);
            color: #FFF !important;
        }

        .btn-action-delete {
            background: rgba(211, 47, 47, 0.15);
            border: 1px solid rgba(211, 47, 47, 0.3);
            color: #EF9A9A !important;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-action-delete:hover {
            background: rgba(211, 47, 47, 0.3);
            color: #FFF !important;
        }

        .text-subheading {
            color: var(--text-secondary) !important;
            font-size: 0.95rem;
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
                    <a href="index.php" class="btn btn-outline-custom nav-btn">
                        <i class="fa-solid fa-house me-1"></i> Library
                    </a>
                    <a href="logout.php" class="btn btn-outline-custom nav-btn">Logout</a>
                </div>
            </div>
        </nav>

        <div class="container my-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="fw-bold mb-1" style="font-size: 2rem; letter-spacing: -0.5px;">
                        <i class="fa-solid fa-gauge text-accent me-2"></i>Admin Control Panel
                    </h1>
                    <p class="text-subheading mb-0">Manage game titles, metadata, and platform listings.</p>
                </div>
                <div>
                    <a href="add_game.php" class="btn btn-gradient nav-btn d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Add New Game
                    </a>
                </div>
            </div>

            <div class="glass-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table custom-table align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Genre</th>
                                <th>Platform</th>
                                <th>Release</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($games) > 0): ?>
                                <?php foreach($games as $g): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge-id">#<?= $g['Game_ID'] ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                                $cover = trim($g['Cover_Image'] ?? '');
                                                $localFile = 'uploads/' . ltrim(str_replace('uploads/', '', $cover), '/');

                                                if (!empty($cover) && file_exists(__DIR__ . '/' . $localFile)) {
                                                    $imgSrc = $localFile;
                                                } else {
                                                    $imgSrc = 'https://placehold.co/100x100/181E22/4CAF50?text=' . urlencode($g['Title']);
                                                }
                                            ?>
                                            <img src="<?= htmlspecialchars($imgSrc) ?>" class="cover-thumb" alt="<?= htmlspecialchars($g['Title']) ?>" referrerpolicy="no-referrer">
                                        </td>
                                        <td class="fw-bold text-light"><?= htmlspecialchars($g['Title']) ?></td>
                                        <td>
                                            <span class="badge-pill badge-genre">
                                                <?= htmlspecialchars($g['Genre_Name'] ?? 'General') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge-pill badge-platform">
                                                <?= htmlspecialchars($g['Platform_Name'] ?? 'Multiplatform') ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-light">
                                            <?= htmlspecialchars($g['Release_Year'] ?? 'N/A') ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <a href="edit_game.php?id=<?= $g['Game_ID'] ?>" class="btn btn-action-edit">
                                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                                </a>
                                                <a href="delete_game.php?id=<?= $g['Game_ID'] ?>" class="btn btn-action-delete" onclick="return confirm('Are you sure you want to delete this game?')">
                                                    <i class="fa-solid fa-trash me-1"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-subheading">
                                        <i class="fa-solid fa-gamepad fa-2x mb-3 opacity-50"></i>
                                        <p class="mb-0">No games found in the database.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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