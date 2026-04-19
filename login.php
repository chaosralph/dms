<?php
/**
 * Einfache Session-basierte Authentifizierung.
 * In der Produktivumgebung sollte dies mit dem bestehenden
 * Login-System von rd.timepro-solutions.de verknüpft werden.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/helpers.php';

session_name(SESSION_NAME);
session_start();

if (!empty($_SESSION['dms_logged_in'])) {
    header('Location: ' . SITE_URL . '/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hier ggf. Anbindung an das bestehende Auth-System
    // Für den Start: einfache Prüfung (bitte anpassen!)
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // TODO: Durch echte Authentifizierung ersetzen
    if (!empty($email) && !empty($password)) {
        $_SESSION['dms_logged_in'] = true;
        $_SESSION['dms_user_email'] = $email;
        header('Location: ' . SITE_URL . '/');
        exit;
    }

    $error = 'Ungültige Anmeldedaten.';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-lg);
        }
        .login-card h1 {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .login-card p {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }
        .login-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-logo-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: var(--radius);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
        }
        .login-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        .login-links a {
            color: var(--primary-light);
            text-decoration: none;
            font-size: 0.875rem;
        }
        .login-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <div class="login-logo-icon">
                    <span class="material-icons-round">description</span>
                </div>
            </div>
            <h1>DMS Login</h1>
            <p>Melden Sie sich an, um Ihre Dokumente zu verwalten.</p>

            <?php if ($error): ?>
                <div class="login-error"><?= sanitize($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="dms-form-group">
                    <label class="dms-form-label" for="email">E-Mail-Adresse</label>
                    <input type="email" class="dms-form-input" id="email" name="email"
                           placeholder="name@example.com" required autofocus
                           value="<?= sanitize($_POST['email'] ?? '') ?>">
                </div>
                <div class="dms-form-group">
                    <label class="dms-form-label" for="password">Passwort</label>
                    <input type="password" class="dms-form-input" id="password" name="password"
                           placeholder="Passwort eingeben" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:0.5rem">
                    <span class="material-icons-round" style="font-size:1.125rem">login</span>
                    Anmelden
                </button>
            </form>

            <div class="login-links">
                <a href="https://rd.timepro-solutions.de/">Zurück zur Hauptseite</a>
            </div>
        </div>
    </div>
</body>
</html>
