<?php
// Dumps the application database with the credentials Laravel itself resolves,
// so the password never passes through a shell, a terminal or a log.
// Usage (on the server): php db-dump.php <app-dir> <output.sql.gz>

[, $appDir, $output] = $argv + [null, null, null];
if (! $appDir || ! $output) {
    fwrite(STDERR, "usage: php db-dump.php <app-dir> <output.sql.gz>\n");
    exit(2);
}

require $appDir . '/vendor/autoload.php';
$app = require $appDir . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = config('database.connections.' . config('database.default'));

$command = sprintf(
    'set -o pipefail; mysqldump --single-transaction --quick --routines --triggers --events --no-tablespaces --default-character-set=utf8mb4 -h %s -u %s %s | gzip > %s',
    escapeshellarg($db['host']),
    escapeshellarg($db['username']),
    escapeshellarg($db['database']),
    escapeshellarg($output)
);

$process = proc_open(['/bin/bash', '-c', $command], [2 => ['pipe', 'w']], $pipes, null, array_merge(getenv(), ['MYSQL_PWD' => $db['password']]));
$errors = stream_get_contents($pipes[2]);
$code = proc_close($process);

if ($code !== 0) {
    fwrite(STDERR, $errors);
    exit($code);
}

$tables = (int) Illuminate\Support\Facades\DB::selectOne('SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = DATABASE()')->n;
$dumped = (int) trim((string) shell_exec('gunzip -c ' . escapeshellarg($output) . ' | grep -c "^CREATE TABLE"'));

if ($tables !== $dumped) {
    fwrite(STDERR, "table count mismatch: database={$tables} dump={$dumped}\n");
    exit(1);
}

echo "ok tables={$tables} file={$output}\n";
