<?php
use App\Config\App;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Login - Kyle HMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-container { width: 100%; max-width: 450px; padding: 15px; }
        .auth-card { background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); padding: 40px; }
        .auth-logo { text-align: center; margin-bottom: 30px; }
        .auth-logo i { font-size: 3rem; color: #667eea; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="text-center mb-3">
            <a href="<?= url('/') ?>" class="text-white text-decoration-none">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
        <div class="auth-card">
            <div class="auth-logo">
                <i class="fas fa-hospital"></i>
                <h4 class="mt-2">Kyle-HMS</h4>
                <p class="text-muted"><?= $subtitle ?? 'Hospital Management System' ?></p>
            </div>
            <?php if (isset($_SESSION['flash_message'])): ?>
                <?= displayFlash() ?>
            <?php endif; ?>
            <?php require __DIR__ . '/../' . str_replace('.', '/', $contentView) . '.php'; ?>
        </div>
        <div class="text-center mt-3">
            <small class="text-white">&copy; <?= date('Y') ?> Kyle-HMS. All rights reserved.</small>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</body>
</html>