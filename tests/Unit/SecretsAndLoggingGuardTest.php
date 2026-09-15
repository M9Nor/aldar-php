<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Source guards for S14 (currconv key), S15 (IP-gated debug switch) and S20 (request bodies in logs).
 * They read files only, and their failure messages never print file contents, so no secret is echoed.
 */
class SecretsAndLoggingGuardTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public function test_the_currconv_key_is_read_from_config(): void
    {
        $source = file_get_contents($this->root() . '/Modules/Frontend/Http/Controllers/FrontendController.php');

        $this->assertTrue(str_contains($source, "config('services.currconv.key')"), 'FrontendController must read services.currconv.key');
        $this->assertFalse(str_contains($source, '$apiKey'), 'FrontendController still declares the $apiKey property');
        $this->assertSame(0, preg_match("/['\"][0-9a-f]{20,}['\"]/", $source), 'FrontendController contains a key-shaped hex literal');
    }

    public function test_the_currconv_key_comes_from_the_environment(): void
    {
        $services = file_get_contents($this->root() . '/config/services.php');
        $example = file_get_contents($this->root() . '/.env.example');

        $this->assertTrue(str_contains($services, "env('CURRCONV_API_KEY')"), 'config/services.php must read CURRCONV_API_KEY');
        $this->assertSame(1, preg_match('/^CURRCONV_API_KEY=$/m', $example), '.env.example must list an empty CURRCONV_API_KEY');
    }

    public function test_no_ip_address_turns_on_debug_mode(): void
    {
        $source = file_get_contents($this->root() . '/app/Providers/AppServiceProvider.php');

        $this->assertFalse(str_contains($source, 'ipAddresses'), 'AppServiceProvider still has an IP allow-list');
        $this->assertFalse(str_contains($source, 'Debugbar'), 'AppServiceProvider still references Debugbar');
        $this->assertFalse(str_contains($source, "'app.debug'"), 'AppServiceProvider still changes app.debug');
    }

    public function test_request_bodies_are_never_logged(): void
    {
        $offenders = [];
        foreach (['app', 'Modules'] as $dir) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root() . '/' . $dir, RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($files as $file) {
                $path = $file->getPathname();
                if (! str_ends_with($path, '.php') || str_contains($path, '/Resources/assets/')) {
                    continue;
                }
                if (preg_match('/(Log::\w+|logger)\s*\(\s*\$request->(all|input|post|except|only)\s*\(/', file_get_contents($path))) {
                    $offenders[] = substr($path, strlen($this->root()) + 1);
                }
            }
        }

        $this->assertSame([], $offenders, 'Request bodies can hold passwords: never log them (S20).');
    }
}
