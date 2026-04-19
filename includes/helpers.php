<?php

function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function jsonResponse(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError(string $message, int $code = 400): never
{
    jsonResponse(['success' => false, 'error' => $message], $code);
}

function generateFilename(string $extension = 'pdf'): string
{
    return date('Y-m-d_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
}

function formatFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

function ensureDirectories(): void
{
    $dirs = [UPLOAD_DIR, ORIGINALS_DIR, PDF_DIR];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

function checkAuth(): void
{
    if (!AUTH_REQUIRED) {
        return;
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
    if (empty($_SESSION['dms_logged_in'])) {
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
            jsonError('Nicht autorisiert', 401);
        }
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

function createThumbnail(string $sourcePath, string $destPath, int $maxWidth = 300, int $maxHeight = 400): bool
{
    $info = getimagesize($sourcePath);
    if ($info === false) {
        return false;
    }

    [$origWidth, $origHeight, $type] = $info;

    $image = match ($type) {
        IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
        IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
        IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
        default => false,
    };

    if ($image === false) {
        return false;
    }

    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = (int)round($origWidth * $ratio);
    $newHeight = (int)round($origHeight * $ratio);

    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    $white = imagecolorallocate($thumb, 255, 255, 255);
    imagefill($thumb, 0, 0, $white);
    imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    $result = imagejpeg($thumb, $destPath, 85);

    imagedestroy($image);
    imagedestroy($thumb);

    return $result;
}
