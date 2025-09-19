<?php
// checklist.php

echo "=== Laravel Pre-Deployment Checklist ===\n\n";

function checkEnvFile()
{
    if (! file_exists('.env')) {
        echo "[ERROR] File .env tidak ditemukan!\n";
        return false;
    }
    $env = file_get_contents('.env');
    if (strpos($env, 'APP_DEBUG=true') !== false) {
        echo "[WARNING] APP_DEBUG masih true, sebaiknya ubah ke false di production.\n";
    } else {
        echo "[OK] APP_DEBUG sudah false.\n";
    }
    if (strpos($env, 'APP_ENV=production') === false) {
        echo "[WARNING] APP_ENV bukan production.\n";
    } else {
        echo "[OK] APP_ENV sudah production.\n";
    }
    return true;
}

function checkStorageWritable()
{
    $paths       = ['storage', 'bootstrap/cache'];
    $allWritable = true;
    foreach ($paths as $path) {
        if (! is_writable($path)) {
            echo "[ERROR] Folder '$path' tidak writable! Set permission 775 atau 777.\n";
            $allWritable = false;
        } else {
            echo "[OK] Folder '$path' writable.\n";
        }
    }
    return $allWritable;
}

function checkConfigCache()
{
    if (file_exists('bootstrap/cache/config.php')) {
        echo "[OK] Config cache sudah dibuat.\n";
    } else {
        echo "[WARNING] Config cache belum dibuat. Jalankan: php artisan config:cache\n";
    }
}

function checkRouteCache()
{
    if (file_exists('bootstrap/cache/routes-v7.php') || file_exists('bootstrap/cache/routes.php')) {
        echo "[OK] Route cache sudah dibuat.\n";
    } else {
        echo "[WARNING] Route cache belum dibuat. Jalankan: php artisan route:cache\n";
    }
}

function checkViewCache()
{
    // Tidak ada file cache view yang pasti, tapi cek folder
    if (is_dir('storage/framework/views')) {
        echo "[OK] Folder cache view ada.\n";
    } else {
        echo "[WARNING] Folder cache view tidak ditemukan.\n";
    }
}

function checkComposerOptimize()
{
    if (file_exists('vendor/autoload.php')) {
        echo "[OK] Composer dependencies sudah terinstall.\n";
    } else {
        echo "[ERROR] Vendor folder tidak ditemukan. Jalankan: composer install --optimize-autoloader --no-dev\n";
    }
}

function checkEnvNotInPublic()
{
    if (file_exists('public/.env')) {
        echo "[ERROR] File .env ditemukan di folder public! Harus dihapus segera.\n";
    } else {
        echo "[OK] File .env tidak ada di folder public.\n";
    }
}

function checkDatabaseMigration()
{
    // Cek apakah tabel migrations ada di database
    $env = parse_ini_file('.env');
    $db  = [
        'host'     => $env['DB_HOST'] ?? '127.0.0.1',
        'port'     => $env['DB_PORT'] ?? '3306',
        'database' => $env['DB_DATABASE'] ?? '',
        'username' => $env['DB_USERNAME'] ?? '',
        'password' => $env['DB_PASSWORD'] ?? '',
    ];

    try {
        $pdo  = new PDO("mysql:host={$db['host']};port={$db['port']};dbname={$db['database']}", $db['username'], $db['password']);
        $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
        if ($stmt && $stmt->rowCount() > 0) {
            echo "[OK] Tabel migrations ditemukan di database.\n";
        } else {
            echo "[WARNING] Tabel migrations tidak ditemukan. Pastikan sudah menjalankan migrasi.\n";
        }
    } catch (Exception $e) {
        echo "[ERROR] Tidak bisa konek ke database: " . $e->getMessage() . "\n";
    }
}

function main()
{
    checkEnvFile();
    echo "\n";
    checkStorageWritable();
    echo "\n";
    checkConfigCache();
    checkRouteCache();
    checkViewCache();
    echo "\n";
    checkComposerOptimize();
    echo "\n";
    checkEnvNotInPublic();
    echo "\n";
    checkDatabaseMigration();
    echo "\n";

    echo "=== Checklist selesai ===\n";
}

main();
