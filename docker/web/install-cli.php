<?php
/**
 * Headless WebEngine CMS installer.
 *
 * Creates the WEBENGINE_* tables and seeds the cron jobs in the game database,
 * reusing WebEngine's own dB class, SQL table files and installer definitions
 * (the same work the web installer at /install does in steps 3 and 4).
 *
 * Idempotent: existing tables and cron rows are left untouched, so it is safe
 * to run on every `just init`.
 *
 * Reads DB settings from the environment (DB_HOST, DB_PORT, DB_NAME, DB_USER,
 * DB_PASS, SQL_PDO_DRIVER).
 */

define('access', 'install'); // satisfies the guards in the core files below

$core = getenv('WEBENGINE_CORE') ?: '/opt/webengine-core';

$host   = getenv('DB_HOST');
$port   = getenv('DB_PORT') ?: '1433';
$name   = getenv('DB_NAME') ?: 'MuOnline';
$user   = getenv('DB_USER') ?: 'sa';
$pass   = getenv('DB_PASS');
$driver = getenv('SQL_PDO_DRIVER') ?: '1';

// The dB class writes connection errors to this constant's path on failure.
if (!defined('WEBENGINE_DATABASE_ERRORLOG')) {
    define('WEBENGINE_DATABASE_ERRORLOG', '/tmp/webengine_db_errors.log');
}

require $core . '/includes/config/webengine.tables.php'; // WE_PREFIX + WEBENGINE_* constants
require $core . '/includes/functions.php';               // check_value() and other helpers
require $core . '/includes/classes/class.database.php';  // dB
require $core . '/install/definitions.php';              // $install['sql_list'], ['cron_jobs']

$db = new dB($host, $port, $name, $user, $pass, $driver);
if ($db->dead) {
    fwrite(STDERR, "[install] Database connection failed: {$db->error}\n");
    exit(1);
}
echo "[install] Connected to {$name} on {$host}:{$port}\n";

// --- Step 3: create tables -------------------------------------------------
foreach ($install['sql_list'] as $file => $table) {
    $sqlPath = "$core/install/sql/$file.txt";
    if (!is_file($sqlPath)) {
        fwrite(STDERR, "[install] Missing SQL file: $file.txt\n");
        exit(1);
    }

    $exists = $db->query_fetch_single(
        "SELECT * FROM sysobjects WHERE xtype = 'U' AND name = ?",
        array($table)
    );
    if ($exists) {
        echo "[install] table $table already exists, skipping\n";
        continue;
    }

    $sql = str_replace('{TABLE_NAME}', $table, file_get_contents($sqlPath));
    if ($db->query($sql)) {
        echo "[install] created $table\n";
    } else {
        fwrite(STDERR, "[install] FAILED to create $table\n");
        exit(1);
    }
}

// --- Step 4: seed cron jobs ------------------------------------------------
foreach ($install['cron_jobs'] as $cron) {
    $cronFile = "$core/includes/cron/{$cron[2]}";
    $cron[] = is_file($cronFile) ? md5_file($cronFile) : '';

    $exists = $db->query_fetch_single(
        "SELECT * FROM " . WEBENGINE_CRON . " WHERE cron_file_run = ?",
        array($cron[2])
    );
    if ($exists) {
        continue;
    }

    $db->query(
        "INSERT INTO " . WEBENGINE_CRON . " (cron_name,cron_description,cron_file_run,cron_run_time,cron_status,cron_protected,cron_file_md5) VALUES (?, ?, ?, ?, ?, ?, ?)",
        $cron
    );
    echo "[install] cron added {$cron[2]}\n";
}

echo "[install] WebEngine install complete.\n";
