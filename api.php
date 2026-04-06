<?php
/**
 * Gallery API
 * Returns album/image data as JSON.
 * 
 * Usage:
 *   api.php                     → list all albums
 *   api.php?album=my-album      → list images in an album
 * 
 * Directory structure expected:
 *   /photos/
 *     album-dir/
 *       title.txt
 *       description.txt
 *       image1.jpg
 *       image2.jpg
 *       ...
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ── Configuration ─────────────────────────────────────────────────────────────
define('PHOTOS_DIR', __DIR__ . '/photos/');   // absolute path to photos root
define('PHOTOS_URL', 'photos/');              // web-accessible URL prefix
define('IMAGE_EXTS', ['jpg','jpeg','png','gif','webp','avif']);

// ── Helpers ───────────────────────────────────────────────────────────────────

function isImage(string $filename): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, IMAGE_EXTS, true);
}

function readText(string $path): string {
    return file_exists($path) ? trim(file_get_contents($path)) : '';
}

function getImages(string $albumDir): array {
    $files = scandir($albumDir);
    $images = [];
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        if (isImage($f)) $images[] = $f;
    }
    sort($images); // ASC by filename
    return $images;
}

// ── Router ────────────────────────────────────────────────────────────────────

if (!is_dir(PHOTOS_DIR)) {
    http_response_code(500);
    echo json_encode(['error' => 'Photos directory not found: ' . PHOTOS_DIR]);
    exit;
}

$album = isset($_GET['album']) ? basename($_GET['album']) : null;

// ── Album detail ──────────────────────────────────────────────────────────────
if ($album !== null) {
    $albumDir = PHOTOS_DIR . $album . '/';
    if (!is_dir($albumDir)) {
        http_response_code(404);
        echo json_encode(['error' => 'Album not found']);
        exit;
    }

    $images = getImages($albumDir);
    $imageUrls = array_map(fn($f) => PHOTOS_URL . $album . '/' . rawurlencode($f), $images);

    echo json_encode([
        'album'       => $album,
        'title'       => readText($albumDir . 'title.txt') ?: $album,
        'description' => readText($albumDir . 'description.txt'),
        'images'      => $imageUrls,
        'filenames'   => $images,
    ]);
    exit;
}

// ── Album list ────────────────────────────────────────────────────────────────
$dirs = scandir(PHOTOS_DIR);
$albums = [];

foreach ($dirs as $dir) {
    if ($dir === '.' || $dir === '..') continue;
    $albumDir = PHOTOS_DIR . $dir . '/';
    if (!is_dir($albumDir)) continue;

    $images = getImages($albumDir);
    if (empty($images)) continue; // skip empty dirs

    $cover = PHOTOS_URL . $dir . '/' . rawurlencode($images[0]);

    $albums[] = [
        'slug'        => $dir,
        'title'       => readText($albumDir . 'title.txt') ?: $dir,
        'description' => readText($albumDir . 'description.txt'),
        'cover'       => $cover,
        'count'       => count($images),
    ];
}

// Sort albums by directory name DESC (newest first, assumes date-prefixed names)
usort($albums, fn($a, $b) => strcmp($b['slug'], $a['slug']));

echo json_encode(['albums' => $albums]);
