<?php
class Env {
    private static $loaded = false;
    private static $variables = [];

    public static function load($path = null) {
        if (self::$loaded) return;

        if ($path === null) {
            $path = __DIR__ . '/.env';
        }

        if (!file_exists($path)) {
            throw new Exception("Environment file not found: " . $path);
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; // Skip comments

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            if (preg_match('/^["\'](.*)["\']$/', $value, $matches)) {
                $value = $matches[1];
            }

            self::$variables[$name] = $value;
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables[$key] ?? $default;
    }
}

// Auto-load on include
Env::load();