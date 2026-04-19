<?php
/**
 * DMS Configuration
 * Anpassung an die jeweilige Serverumgebung nötig.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'dms_database');
define('DB_USER', 'dms_user');
define('DB_PASS', 'CHANGE_ME');
define('DB_CHARSET', 'utf8mb4');

define('SITE_URL', 'https://');
define('SITE_TITLE', 'DMS - Dokumentenmanagement');

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('ORIGINALS_DIR', UPLOAD_DIR . 'originals/');
define('PDF_DIR', UPLOAD_DIR . 'pdfs/');

define('MAX_FILE_SIZE', 20 * 1024 * 1024); // 20 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/heic']);
define('PDF_AUTHOR', 'RD Formstack Solutions');

define('ITEMS_PER_PAGE', 12);

// Session-basierte Auth – muss mit dem bestehenden Login-System abgestimmt werden
define('SESSION_NAME', 'dms_session');
define('AUTH_REQUIRED', true);
