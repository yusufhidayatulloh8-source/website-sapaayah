<?php

function app_config(?string $key = null, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config/app.php';
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function db_config(?string $key = null, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config/database.php';
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function base_url(string $path = ''): string
{
    $root = rtrim(app_config('app_url', ''), '/');
    $path = ltrim($path, '/');

    return $path ? $root . '/' . $path : $root;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function clean_input(?string $value): string
{
    return trim(strip_tags((string) $value));
}

function redirect(string $path): void
{
    header('Location: ' . (preg_match('/^https?:\/\//', $path) ? $path : base_url($path)));
    exit;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function asset(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function upload_url(string $path): string
{
    return base_url('uploads/' . ltrim($path, '/'));
}

function flash(string $key, ?string $message = null)
{
    if (!isset($_SESSION)) {
        return null;
    }

    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);

    return $value;
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['old'][$key] ?? $default);
}

function keep_old_input(array $input): void
{
    $_SESSION['old'] = $input;
}

function clear_old_input(): void
{
    unset($_SESSION['old']);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function active_page(string $expected, string $class = 'active'): string
{
    $current = $_GET['page'] ?? 'home';
    return $current === $expected ? $class : '';
}

function format_date(?string $date, string $format = 'd M Y'): string
{
    if (!$date) {
        return '-';
    }
    return date($format, strtotime($date));
}

function limit_words(string $text, int $limit = 20): string
{
    $words = preg_split('/\s+/', strip_tags($text));
    if (!$words || count($words) <= $limit) {
        return $text;
    }
    return implode(' ', array_slice($words, 0, $limit)) . '...';
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
    $value = preg_replace('/\s+/', '-', $value);
    $value = preg_replace('/-+/', '-', $value);

    return trim($value, '-');
}

function setting(string $key, string $default = ''): string
{
    static $cache = [];

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    try {
        $row = db_one('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1', ['key' => $key]);
        $cache[$key] = $row['setting_value'] ?? $default;
    } catch (Throwable $e) {
        $cache[$key] = $default;
    }

    return $cache[$key];
}

function youtube_embed_url(string $url, string $default = 'https://www.youtube.com/embed/dQw4w9WgXcQ'): string
{
    $url = trim($url);
    if ($url === '') {
        return $default;
    }

    $parts = parse_url($url);
    if (!$parts || empty($parts['host'])) {
        return $url;
    }

    $host = strtolower($parts['host']);
    $host = preg_replace('/^www\./', '', $host);
    $path = trim($parts['path'] ?? '', '/');
    $query = [];

    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }

    $videoId = '';
    if ($host === 'youtu.be') {
        $videoId = explode('/', $path)[0] ?? '';
    } elseif ($host === 'youtube.com' || $host === 'youtube-nocookie.com') {
        if (!empty($query['v'])) {
            $videoId = (string) $query['v'];
        } elseif (preg_match('~^(embed|shorts|live)/([^/?#]+)~', $path, $matches)) {
            $videoId = $matches[2];
        }
    }

    if (!preg_match('/^[A-Za-z0-9_-]{6,}$/', $videoId)) {
        return $url;
    }

    $embedQuery = [];
    if (!empty($query['list'])) {
        $embedQuery['list'] = (string) $query['list'];
    }
    if (!empty($query['start'])) {
        $embedQuery['start'] = (string) $query['start'];
    } elseif (!empty($query['t']) && preg_match('/^(\d+)s?$/', (string) $query['t'], $timeMatches)) {
        $embedQuery['start'] = $timeMatches[1];
    }

    $embedUrl = 'https://www.youtube.com/embed/' . rawurlencode($videoId);
    if ($embedQuery) {
        $embedUrl .= '?' . http_build_query($embedQuery);
    }

    return $embedUrl;
}

function ensure_program_registrations_table(): void
{
    static $ready = false;

    if ($ready) {
        return;
    }

    db_execute(
        'CREATE TABLE IF NOT EXISTS program_registrations (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            program_id BIGINT UNSIGNED NOT NULL,
            full_name VARCHAR(150) NOT NULL,
            email VARCHAR(150) DEFAULT NULL,
            phone VARCHAR(30) NOT NULL,
            city VARCHAR(120) DEFAULT NULL,
            motivation TEXT DEFAULT NULL,
            status ENUM("pending","contacted","accepted","cancelled") NOT NULL DEFAULT "pending",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_program_registrations_program (program_id),
            INDEX idx_program_registrations_status (status),
            CONSTRAINT fk_program_registrations_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $ready = true;
}

function is_admin_path(): bool
{
    return str_contains($_SERVER['PHP_SELF'] ?? '', '/admin/');
}
