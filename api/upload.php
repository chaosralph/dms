<?php
/**
 * API: Bilder hochladen und als PDF speichern
 * POST /api/upload.php
 * 
 * Erwartet:
 *   - images[]     : Bilddateien (multipart/form-data)
 *   - title        : Dokumententitel
 *   - description  : Beschreibung (optional)
 *   - category_id  : Kategorie-ID (optional)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PdfGenerator.php';

checkAuth();
ensureDirectories();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Nur POST erlaubt', 405);
}

$title = sanitize($_POST['title'] ?? '');
$description = sanitize($_POST['description'] ?? '');
$categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

if (empty($title)) {
    jsonError('Titel ist erforderlich');
}

if (empty($_FILES['images']) || empty($_FILES['images']['name'][0])) {
    jsonError('Mindestens ein Bild ist erforderlich');
}

$files = $_FILES['images'];
$fileCount = count($files['name']);
$imagePaths = [];
$originalFilenames = [];

try {
    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Fehler beim Upload von Datei " . ($i + 1));
        }

        if ($files['size'][$i] > MAX_FILE_SIZE) {
            throw new RuntimeException("Datei " . ($i + 1) . " ist zu groß (max. " . formatFileSize(MAX_FILE_SIZE) . ")");
        }

        $mimeType = mime_content_type($files['tmp_name'][$i]);
        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
            throw new RuntimeException("Datei " . ($i + 1) . ": Ungültiger Dateityp ($mimeType)");
        }

        $ext = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $origFilename = generateFilename($ext);
        $origPath = ORIGINALS_DIR . $origFilename;

        if (!move_uploaded_file($files['tmp_name'][$i], $origPath)) {
            throw new RuntimeException("Fehler beim Speichern von Datei " . ($i + 1));
        }

        $imagePaths[] = $origPath;
        $originalFilenames[] = $origFilename;
    }

    $pdfFilename = generateFilename('pdf');
    $pdfPath = PDF_DIR . $pdfFilename;

    if (!PdfGenerator::createFromImages($imagePaths, $pdfPath, $title)) {
        throw new RuntimeException('Fehler bei der PDF-Erzeugung');
    }

    $thumbnailFilename = 'thumb_' . generateFilename('jpg');
    $thumbnailPath = PDF_DIR . $thumbnailFilename;
    if (!createThumbnail($imagePaths[0], $thumbnailPath)) {
        $thumbnailFilename = null;
    }

    $pdfSize = filesize($pdfPath);

    $db = Database::getConnection();
    $db->beginTransaction();

    $stmt = $db->prepare(
        'INSERT INTO dms_documents (title, description, category_id, pdf_filename, pdf_size, page_count, thumbnail)
         VALUES (:title, :description, :category_id, :pdf_filename, :pdf_size, :page_count, :thumbnail)'
    );
    $stmt->execute([
        ':title' => $title,
        ':description' => $description ?: null,
        ':category_id' => $categoryId,
        ':pdf_filename' => $pdfFilename,
        ':pdf_size' => $pdfSize,
        ':page_count' => $fileCount,
        ':thumbnail' => $thumbnailFilename,
    ]);

    $documentId = (int)$db->lastInsertId();

    $pageStmt = $db->prepare(
        'INSERT INTO dms_document_pages (document_id, page_number, original_filename)
         VALUES (:document_id, :page_number, :original_filename)'
    );

    foreach ($originalFilenames as $index => $filename) {
        $pageStmt->execute([
            ':document_id' => $documentId,
            ':page_number' => $index + 1,
            ':original_filename' => $filename,
        ]);
    }

    $db->commit();

    jsonResponse([
        'success' => true,
        'document' => [
            'id' => $documentId,
            'title' => $title,
            'page_count' => $fileCount,
            'pdf_size' => formatFileSize($pdfSize),
        ],
    ], 201);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    foreach ($imagePaths as $path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }
    if (isset($pdfPath) && file_exists($pdfPath)) {
        unlink($pdfPath);
    }
    if (isset($thumbnailPath) && file_exists($thumbnailPath)) {
        unlink($thumbnailPath);
    }

    jsonError($e->getMessage(), 500);
}
