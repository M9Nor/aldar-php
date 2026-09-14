<?php
// Rotates the application database user's password on the server. The old
// and new passwords are generated/read/compared entirely in PHP: neither
// value is ever printed to stdout/stderr, passed as a CLI argument, put in a
// log, or embedded in an exception message.
//
// Usage: php rotate-db-password.php <app-dir> <credentials-file>
// <app-dir> must be a real (non-symlinked) copy of the application, since
// Laravel's own bootstrap/app.php resolves its base path via dirname(__DIR__)
// and PHP resolves __DIR__ through symlinks - a symlinked bootstrap directory
// silently resolves to wherever the symlink points, not to <app-dir>. This
// script refuses to run if the bootstrapped app doesn't actually match
// <app-dir> (see the guards right after bootstrapping, below).
// <credentials-file> must be an absolute path containing no "~" (the shell
// does not expand "~" after "--generate-to=" or as a bare script argument in
// every quoting style, so a literal "~" would silently create a "./~" dir).
//
// Order of operations. Every failure path is designed to leave the site
// working with its current (old) password still valid:
//   1. Bootstrap Laravel from <app-dir>. Refuse (exit 1, nothing touched) if:
//      - the app's configuration is cached (bypasses .env entirely);
//      - the bootstrapped base path isn't <app-dir> (symlink/APP_BASE_PATH
//        games, or a wrong argument);
//      - the bootstrapped .env file isn't <app-dir>/.env;
//      - <app-dir>/.env doesn't have exactly one DB_PASSWORD= line;
//      - that line's value (unquoted) doesn't match what Laravel actually
//        loaded into config('database...') - a process environment variable
//        can override .env, and this catches that mismatch before we act on
//        stale information.
//   2. Generate a new 32-character alphanumeric password.
//   3. Append "<timestamp>\tdb:<username>\t<new>\n" to <credentials-file>
//      under an exclusive lock (checking every byte was actually written),
//      creating it (and its parent dir) with restrictive permissions first.
//      If this fails, exit 1 untouched.
//   4. Back up <app-dir>/.env to "<credentials-file dir>/env-before-db-
//      rotation-<ts>" (mode 0600), byte for byte.
//   5. Build the replacement .env content by substituting only the matched
//      DB_PASSWORD= line (preserving every other byte, including CRLF line
//      endings and multi-byte UTF-8), and stage it in <app-dir>/.env.rotating
//      (0600 while it holds the secret, then the original file's permissions)
//      without renaming it over .env yet. Re-read it back to confirm the
//      write wasn't partial.
//   6. Open a PDO connection with the CURRENT credentials and run
//      SET PASSWORD = PASSWORD(?) with the new password bound.
//      - Can't even connect: exit 1, nothing changed, staged file removed.
//      - The SET PASSWORD call throws: probe with the OLD password, then the
//        NEW one, to find out what actually happened server-side -
//          - old still works -> really not changed: exit 1, staged file
//            removed, "DB password NOT changed: <exception class>".
//          - only the new one works -> the change DID apply despite the
//            exception: fall through to the rename and the normal
//            verification below.
//          - neither works -> exit 4, UNKNOWN STATE, staged file kept (it is
//            the only remaining record of what .env should say).
//   7. Atomically rename .env.rotating over .env. If this fails, the DB
//      password has already changed but .env has not: exit 2 with a loud
//      message (the staged file is deliberately kept for manual recovery).
//   8. Verify: re-read the now-live .env and require its DB_PASSWORD value to
//      equal the new password, then open a brand new PDO connection with it
//      and run SELECT 1. Either failing means exit 3, pointing at the backup
//      and credentials files (nothing here is a secret, but the state genuinely
//      needs a human).
//   9. Print one confirmation line naming no secret.
//
// .env.rotating is always removed on every failure path before step 7
// commits (rename), including an exception nobody anticipated, via the
// try/finally further down - except for the two states above where the file
// itself is the only recovery record and must be kept on purpose.

umask(0077);

[, $appDir, $credentialsFile] = $argv + [null, null, null];
if (! $appDir || ! $credentialsFile) {
    fwrite(STDERR, "usage: php rotate-db-password.php <app-dir> <credentials-file>\n");
    exit(64);
}

function rotate_is_safe_path(string $path): bool
{
    return $path !== '' && $path[0] === '/' && strpos($path, '~') === false;
}

if (! rotate_is_safe_path($credentialsFile)) {
    fwrite(STDERR, "The credentials-file argument must be an absolute path containing no '~': {$credentialsFile}\n");
    exit(64);
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

/** Append one line to a file under an exclusive lock, verifying a full write. */
function rotate_append_locked(string $file, string $line): bool
{
    $handle = @fopen($file, 'ab');
    if ($handle === false) {
        return false;
    }

    $written = false;
    if (flock($handle, LOCK_EX)) {
        $bytes = fwrite($handle, $line);
        if ($bytes === strlen($line) && fflush($handle)) {
            $written = true;
        }
        flock($handle, LOCK_UN);
    }
    fclose($handle);

    return $written;
}

/**
 * Write staged .env content to $path: 0600 while the secret is being
 * written, then $finalMode (the live .env's own permissions) once the
 * content is confirmed safely on disk by reading it back. The caller renames
 * it into place separately. Cleans up after itself on any failure.
 */
function rotate_write_staged_env(string $path, string $content, int $finalMode): bool
{
    $handle = @fopen($path, 'wb');
    if ($handle === false) {
        return false;
    }
    if (! @chmod($path, 0600)) {
        fclose($handle);
        @unlink($path);
        return false;
    }

    $ok = false;
    if (flock($handle, LOCK_EX)) {
        $bytes = fwrite($handle, $content);
        $ok = $bytes === strlen($content) && fflush($handle);
        flock($handle, LOCK_UN);
    }
    fclose($handle);

    if (! $ok) {
        @unlink($path);
        return false;
    }

    // Confirm the write wasn't partial by reading it back before it is
    // trusted with a live credential.
    $readBack = @file_get_contents($path);
    if ($readBack !== $content) {
        @unlink($path);
        return false;
    }

    if (! @chmod($path, $finalMode)) {
        @unlink($path);
        return false;
    }

    return true;
}

/**
 * Return the unquoted value of the single "DB_PASSWORD=" line in $content,
 * or null if there is not exactly one such line.
 */
function rotate_extract_db_password(string $content): ?string
{
    if (preg_match_all('/^DB_PASSWORD=([^\r\n]*)/m', $content, $matches) !== 1) {
        return null;
    }

    return rotate_unquote_env_value($matches[1][0]);
}

function rotate_unquote_env_value(string $value): string
{
    $len = strlen($value);
    if ($len >= 2) {
        $first = $value[0];
        $last = $value[$len - 1];
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }
    }

    return $value;
}

/**
 * Replace only the DB_PASSWORD= line's value, leaving every other byte -
 * including CRLF line endings and multi-byte UTF-8 elsewhere in the file -
 * untouched. Returns [newContent, replacementCount].
 */
function rotate_replace_db_password(string $content, string $new): array
{
    $count = 0;
    $result = preg_replace('/^DB_PASSWORD=[^\r\n]*/m', 'DB_PASSWORD=' . $new, $content, 1, $count);

    return [$result, $count];
}

function rotate_dsn(array $db): string
{
    return sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['port'], $db['database']);
}

/** Try to connect with the given password and run a trivial query. */
function rotate_can_connect(array $db, string $password): bool
{
    try {
        $pdo = new PDO(rotate_dsn($db), $db['username'], $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $pdo->query('SELECT 1');

        return true;
    } catch (Throwable $e) {
        return false;
    }
}

// Step 1: bootstrap Laravel from <app-dir> and make sure it actually
// resolved to <app-dir> and not somewhere else.
require $appDir . '/vendor/autoload.php';
$app = require $appDir . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if ($app->configurationIsCached()) {
    fwrite(STDERR, "Refusing to run: configuration is cached (run 'php artisan config:clear' first).\n");
    exit(1);
}

$resolvedBasePath = realpath($app->basePath());
$requestedBasePath = realpath($appDir);
if ($resolvedBasePath === false || $requestedBasePath === false || $resolvedBasePath !== $requestedBasePath) {
    fwrite(STDERR, "Refusing to run: the bootstrapped application base path does not match <app-dir>.\n");
    exit(1);
}

$envPath = $appDir . '/.env';
$resolvedEnvFile = realpath($app->environmentFilePath());
$requestedEnvFile = realpath($envPath);
if ($resolvedEnvFile === false || $requestedEnvFile === false || $resolvedEnvFile !== $requestedEnvFile) {
    fwrite(STDERR, "Refusing to run: the bootstrapped .env does not match <app-dir>/.env.\n");
    exit(1);
}

$db = config('database.connections.' . config('database.default'));

$envContent = @file_get_contents($envPath);
if ($envContent === false) {
    fwrite(STDERR, "Could not read {$envPath}.\n");
    exit(1);
}

$currentEnvPassword = rotate_extract_db_password($envContent);
if ($currentEnvPassword === null) {
    fwrite(STDERR, "Expected exactly one DB_PASSWORD= line in {$envPath}.\n");
    exit(1);
}

if (! hash_equals((string) $db['password'], $currentEnvPassword)) {
    fwrite(STDERR, "Refusing to run: the DB password Laravel loaded does not match the DB_PASSWORD line in "
        . "{$envPath} (a process environment variable may be overriding it).\n");
    exit(1);
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

// Step 4: back up the current .env next to the credentials file, byte for
// byte, before building the replacement.
$backupPath = dirname($credentialsFile) . '/env-before-db-rotation-' . $timestamp;
if (! @copy($envPath, $backupPath) || filesize($backupPath) !== strlen($envContent) || ! @chmod($backupPath, 0600)) {
    fwrite(STDERR, "Could not back up {$envPath} to {$backupPath}.\n");
    exit(1);
}

// Step 5: stage the replacement .env content without going live yet.
$originalMode = fileperms($envPath) & 0777;
[$newEnvContent, $replacements] = rotate_replace_db_password($envContent, $new);
if ($newEnvContent === null || $replacements !== 1) {
    fwrite(STDERR, "Could not build the replacement .env content.\n");
    exit(1);
}

$rotatingPath = $appDir . '/.env.rotating';
if (! rotate_write_staged_env($rotatingPath, $newEnvContent, $originalMode)) {
    fwrite(STDERR, "Could not stage {$rotatingPath}.\n");
    exit(1);
}

// Steps 6-7: change the DB password and flip .env live. Wrapped in
// try/finally so .env.rotating is always removed on any failure - including
// one nobody anticipated - except the two states below where the file must
// be kept as the only remaining recovery record. exit() is never called
// inside this block, precisely so the finally always runs.
$rotatingActive = true;
$exitCode = 0;
$exitMessage = null;
$proceed = false;

try {
    do {
        try {
            $pdo = new PDO(rotate_dsn($db), $db['username'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
        } catch (Throwable $e) {
            $exitCode = 1;
            $exitMessage = 'DB password NOT changed: ' . get_class($e) . "\n";
            break;
        }

        try {
            $pdo->prepare('SET PASSWORD = PASSWORD(?)')->execute([$new]);
        } catch (Throwable $e) {
            $oldWorks = rotate_can_connect($db, $db['password']);
            if ($oldWorks) {
                $exitCode = 1;
                $exitMessage = 'DB password NOT changed: ' . get_class($e) . "\n";
                break;
            }

            $newWorks = rotate_can_connect($db, $new);
            if (! $newWorks) {
                $rotatingActive = false; // keep it: the only remaining record.
                $exitCode = 4;
                $exitMessage = "UNKNOWN STATE: the SET PASSWORD reply was lost and neither the old nor the new "
                    . "password currently works. The new password is the last line of {$credentialsFile}; the "
                    . "staged .env is {$rotatingPath}; the original .env backup is {$backupPath}.\n";
                break;
            }
            // else: the change applied despite the exception (e.g. the
            // connection dropped after the server executed it). Fall
            // through to the rename below as if step 6 had succeeded.
        }

        if (! @rename($rotatingPath, $envPath)) {
            $rotatingActive = false; // keep it: .env still has the old password.
            $exitCode = 2;
            $exitMessage = "CRITICAL: the DB password was changed but .env.rotating could not be renamed over "
                . ".env. The new password is in {$credentialsFile}; {$envPath} still holds the old one. Fix this "
                . "by hand now.\n";
            break;
        }

        $rotatingActive = false; // renamed away; nothing left to clean up.
        $proceed = true;
    } while (false);
} finally {
    if ($rotatingActive && file_exists($rotatingPath)) {
        @unlink($rotatingPath);
    }
}

if (! $proceed) {
    fwrite(STDERR, $exitMessage);
    exit($exitCode);
}

// Step 8: verify the live .env now matches, then verify DB connectivity.
$liveEnvContent = @file_get_contents($envPath);
$liveEnvPassword = $liveEnvContent === false ? null : rotate_extract_db_password($liveEnvContent);

if ($liveEnvPassword === null || ! hash_equals($new, $liveEnvPassword)) {
    fwrite(STDERR, "CRITICAL: could not verify the new DB password (the live .env doesn't match it). Check the "
        . "backup at {$backupPath} and the credentials file {$credentialsFile}.\n");
    exit(3);
}

if (! rotate_can_connect($db, $new)) {
    fwrite(STDERR, "CRITICAL: could not verify the new DB password (a fresh DB connection failed). Check the "
        . "backup at {$backupPath} and the credentials file {$credentialsFile}.\n");
    exit(3);
}

// Step 9: confirm without naming a secret.
echo "DB password rotated for {$db['username']}; new value appended to {$credentialsFile}; .env backup at {$backupPath}.\n";
