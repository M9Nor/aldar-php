<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * S2 production readiness, checked in source. The production .env values themselves (APP_DEBUG=false,
 * SESSION_SECURE_COOKIE=true) are Phase 3 work; the code must default safely and run under --no-dev.
 *
 * The session.serialization move to "json" (also part of S2) is NOT covered here: an audit of every
 * session write found a reachable path that flashes an Eloquent model into the session (see the Task 9
 * report), so the controller ruling ("if you find one, stop and report rather than breaking sessions")
 * applies and that part of the change was withheld pending a decision.
 */
class ProductionConfigTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public function test_debug_mode_is_off_unless_the_environment_turns_it_on(): void
    {
        $source = file_get_contents($this->root() . '/config/app.php');

        $this->assertTrue(str_contains($source, "'debug' => env('APP_DEBUG', false),"), 'config/app.php must default app.debug to false');
    }

    public function test_application_code_references_no_require_dev_package(): void
    {
        // Legacy factory closures that only type-hint Faker; no request loads them.
        $allowed = ['database/factories/UserFactory.php', 'Modules/Permissions/Database/factories/UserFactory.php'];
        $pattern = '/\b(Barryvdh|Fruitcake\\\\LaravelDebugbar|Debugbar::|Mockery|PHPUnit\\\\|RectorLaravel|Rector\\\\|Larastan|NunoMaduro|Faker\\\\)/';
        $offenders = [];
        foreach (['app', 'bootstrap', 'config', 'database', 'Modules', 'routes'] as $dir) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root() . '/' . $dir, RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($files as $file) {
                $relative = substr($file->getPathname(), strlen($this->root()) + 1);
                if (! str_ends_with($relative, '.php')
                    || str_contains($relative, '/Resources/assets/')
                    || str_contains($relative, '/Tests/')
                    || str_starts_with($relative, 'bootstrap/cache/')
                    || in_array($relative, $allowed, true)) {
                    continue;
                }
                if (preg_match($pattern, file_get_contents($file->getPathname()))) {
                    $offenders[] = $relative;
                }
            }
        }

        $this->assertSame([], $offenders, 'These files reference a require-dev package, which composer install --no-dev removes.');
    }
}
