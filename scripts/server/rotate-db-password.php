<?php
// Rotates the application database user's password on the server. The old
// and new passwords are generated/read/compared entirely in PHP: neither
// value is ever printed to stdout/stderr, passed as a CLI argument, put in a
// log, or embedded in an exception message.
//
// Usage: php rotate-db-password.php <app-dir> <credentials-file>
//
// For local testing only, when a throwaway <app-dir> cannot boot the full
// framework (e.g. it has no routes/, resources/ or Modules/ directories),
// set ROTATE_DB_ENV_FILE to a .env-style file and the script reads the DB
// connection values from it with Dotenv instead of bootstrapping Laravel.
// Never set this in production.
//
// Order of operations. Every failure path is designed to leave the site
// working with its current (old) password still valid:
//   1. Bootstrap Laravel from <app-dir> and read the current DB connection
//      config, the same way scripts/server/db-dump.php does (or, under
//      ROTATE_DB_ENV_FILE, read it from that file instead - see above).
//   2. Generate a new 32-character alphanumeric password.
//   3. Append "<timestamp>\tdb:<username>\t<new>\n" to <credentials-file>
//      under an exclusive lock, creating it (and its parent dir) with
//      restrictive permissions first. If this fails, exit 1 untouched.
//   4. Read <app-dir>/.env and require exactly one DB_PASSWORD= line before
//      touching the database. If not, exit 1.
//   5. Copy .env to "<credentials-file dir>/env-before-db-rotation-<ts>"
//      (mode 0600).
//   6. Stage the replacement .env content in <app-dir>/.env.rotating
//      (mode 0600 while it holds the secret, then the original file's
//      permissions) without renaming it over .env yet.
//   7. Open a PDO connection with the CURRENT credentials and run
//      SET PASSWORD = PASSWORD(?) with the new password bound. On failure,
//      delete .env.rotating, report only the exception class, and exit 1 -
//      the live .env and the DB password are both untouched.
//   8. Atomically rename .env.rotating over .env. If this fails the DB
//      password has already changed but .env has not: exit 2 with a loud
//      message, since that needs a human immediately.
//   9. Verify a brand new PDO connection using the NEW password. If this
//      fails, exit 3 pointing at the backup and credentials files.
//  10. Print one confirmation line naming no secret.

[, $appDir, $credentialsFile] = $argv + [null, null, null];
if (! $appDir || ! $credentialsFile) {
    fwrite(STDERR, "usage: php rotate-db-password.php <app-dir> <credentials-file>\n");
    exit(2);
}

$appDir = rtrim($appDir, '/');

/**
 * Ensure a credentials/log file (and its parent directory) exist with
 * restrictive permissions before anything is appended to it.
 */
function rotate_ensure_secret_file(string $file): bool
{
    $dir = dirname($file);

    if (! is_dir($dir)) {
        if (! @mkdir($dir, 0700, true) && ! is_dir($dir)) {
            return false;
        }
    }

    if (! file_exists($file)) {
        $handle = @fopen($file, 'c');
        if ($handle === false) {
            return false;
        }
        fclose($handle);
    }

    return @chmod($file, 0600);
}

/** Append one line to a file under an exclusive lock. */
function rotate_append_locked(string $file, string $line): bool
{
    $handle = @fopen($file, 'ab');
    if ($handle === false) {
        return false;
    }

    $written = false;
    if (flock($handle, LOCK_EX)) {
        $written = fwrite($handle, $line) !== false;
        flock($handle, LOCK_UN);
    }
    fclose($handle);

    return $written;
}

/**
 * Write staged .env content to $path: 0600 while the secret is being
 * written, then $finalMode (the live .env's own permissions) once the
 * content is safely on disk. The caller renames it into place separately.
 */
function rotate_write_staged_env(string $path, string $content, int $finalMode): bool
{
    $handle = @fopen($path, 'wb');
    if ($handle === false) {
        return false;
    }
    if (! @chmod($path, 0600)) {
        fclose($handle);
        return false;
    }

    $written = false;
    if (flock($handle, LOCK_EX)) {
        $written = fwrite($handle, $content) !== false;
        flock($handle, LOCK_UN);
    }
    fclose($handle);

    return $written && @chmod($path, $finalMode);
}

function rotate_dsn(array $db): string
{
    return sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['port'], $db['database']);
}

// Step 1: bootstrap Laravel from <app-dir> and read the current DB config.
require $appDir . '/vendor/autoload.php';

$envOverride = getenv('ROTATE_DB_ENV_FILE');
if ($envOverride !== false && $envOverride !== '') {
    // Local-test escape hatch (see the comment at the top of this file):
    // read connection values straight from a .env file instead of booting
    // the full framework.
    $vars = Dotenv\Dotenv::createImmutable(dirname($envOverride), basename($envOverride))->load();
    $db = [
        'host'     => $vars['DB_HOST'] ?? '127.0.0.1',
        'port'     => $vars['DB_PORT'] ?? '3306',
        'database' => $vars['DB_DATABASE'] ?? '',
        'username' => $vars['DB_USERNAME'] ?? '',
        'password' => $vars['DB_PASSWORD'] ?? '',
    ];
} else {
    $app = require $appDir . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    $db = config('database.connections.' . config('database.default'));
}

$timestamp = gmdate('Y-m-d\TH:i:s\Z');

// Step 2: generate the new password. Str::random() only emits A-Za-z0-9, so
// it never needs quoting when written into .env.
$new = Illuminate\Support\Str::random(32);

// Step 3: record the new value before anything else changes.
if (! rotate_ensure_secret_file($credentialsFile)) {
    fwrite(STDERR, "Could not prepare the credentials file {$credentialsFile}.\n");
    exit(1);
}

if (! rotate_append_locked($credentialsFile, "{$timestamp}\tdb:{$db['username']}\t{$new}\n")) {
    fwrite(STDERR, "Could not write to the credentials file {$credentialsFile}.\n");
    exit(1);
}

// Step 4: the live .env must hold exactly one DB_PASSWORD line before the
// database is touched at all.
$envPath = $appDir . '/.env';
$envContent = @file_get_contents($envPath);
if ($envContent === false) {
    fwrite(STDERR, "Could not read {$envPath}.\n");
    exit(1);
}

$envLines = preg_split('/\R/', $envContent);
$passwordLineIndexes = array_keys(preg_grep('/^DB_PASSWORD=/', $envLines));

if (count($passwordLineIndexes) !== 1) {
    fwrite(STDERR, 'Expected exactly one DB_PASSWORD= line in ' . $envPath . ', found ' . count($passwordLineIndexes) . ".\n");
    exit(1);
}

// Step 5: back up the current .env next to the credentials file.
$backupPath = dirname($credentialsFile) . '/env-before-db-rotation-' . $timestamp;
if (! @copy($envPath, $backupPath) || ! @chmod($backupPath, 0600)) {
    fwrite(STDERR, "Could not back up {$envPath} to {$backupPath}.\n");
    exit(1);
}

// Step 6: stage the replacement .env content without going live yet.
$originalMode = fileperms($envPath) & 0777;
$envLines[$passwordLineIndexes[0]] = 'DB_PASSWORD=' . $new;
$newEnvContent = implode("\n", $envLines);

$rotatingPath = $appDir . '/.env.rotating';
if (! rotate_write_staged_env($rotatingPath, $newEnvContent, $originalMode)) {
    fwrite(STDERR, "Could not stage {$rotatingPath}.\n");
    exit(1);
}

// Step 7: change the DB user's own password using the CURRENT credentials.
try {
    $pdo = new PDO(rotate_dsn($db), $db['username'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->prepare('SET PASSWORD = PASSWORD(?)')->execute([$new]);
} catch (Throwable $e) {
    @unlink($rotatingPath);
    fwrite(STDERR, 'DB password NOT changed: ' . get_class($e) . "\n");
    exit(1);
}

// Step 8: flip the new .env live. rename() is atomic on the same filesystem.
if (! @rename($rotatingPath, $envPath)) {
    fwrite(STDERR, "CRITICAL: the DB password was changed but .env.rotating could not be renamed over .env. "
        . "The new password is in {$credentialsFile}; {$envPath} still holds the old one. Fix this by hand now.\n");
    exit(2);
}

// Step 9: verify the new password actually works end to end.
try {
    $verify = new PDO(rotate_dsn($db), $db['username'], $new, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $verify->query('SELECT 1');
} catch (Throwable $e) {
    fwrite(STDERR, "CRITICAL: could not verify the new DB password. Check the backup at {$backupPath} "
        . "and the credentials file {$credentialsFile}.\n");
    exit(3);
}

// Step 10: confirm without naming a secret.
echo "DB password rotated for {$db['username']}; new value appended to {$credentialsFile}; .env backup at {$backupPath}.\n";
