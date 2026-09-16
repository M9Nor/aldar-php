<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/** Phase 3: config:cache makes env() outside config/ return null, so no app code may call it. */
class ConfigCacheSafetyTest extends TestCase
{
    private const ROOTS = ['app', 'Modules', 'routes', 'resources'];

    public function test_no_application_code_calls_env(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        foreach (self::ROOTS as $dir) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("$root/$dir"));
            foreach ($files as $file) {
                if (! $file->isFile() || ! preg_match('/\.(php|blade\.php)$/', $file->getFilename())) {
                    continue;
                }
                $relative = str_replace("$root/", '', $file->getPathname());
                // Compiled assets and vendored libraries are not application code.
                if (str_contains($relative, '/Resources/assets/') || str_contains($relative, '/Includes/')) {
                    continue;
                }
                // Module config, e.g. Modules/Cms/Config/config.php, is merged via mergeConfigFrom()
                // into Laravel's config array at the same point top-level config/*.php files are,
                // so env() there is baked in by config:cache exactly like env() inside config/.
                if (str_contains($relative, '/Config/')) {
                    continue;
                }
                if (preg_match('/\benv\s*\(/', (string) file_get_contents($file->getPathname()))) {
                    $offenders[] = $relative;
                }
            }
        }

        sort($offenders);
        $this->assertSame([], array_values(array_unique($offenders)), "env() outside config/ breaks config:cache in:\n" . implode("\n", $offenders));
    }
}
