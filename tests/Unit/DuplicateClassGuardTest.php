<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * F1: composer.json sets optimize-autoloader (true), so `composer install`/`dump-autoload -o` builds a
 * classmap by scanning the PSR-4 roots. If two PHP files under app/ or Modules/ declare the same
 * `namespace + class` pair, Composer emits "Ambiguous class resolution ... the first will be used" and the
 * winner depends on filesystem scan order, which can differ between machines. This guard scans file headers
 * with a regex only (no autoloading, no Composer), so it stays fast and cannot itself trigger the ambiguity.
 * It must print only the offending class names and paths, never file contents.
 */
class DuplicateClassGuardTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public function test_no_two_files_declare_the_same_namespace_and_class(): void
    {
        $seenAt = [];
        $offenders = [];

        foreach (['app', 'Modules'] as $dir) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root() . '/' . $dir, RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($files as $file) {
                $path = $file->getPathname();
                if (! str_ends_with($path, '.php')) {
                    continue;
                }

                $source = file_get_contents($path);
                if (! preg_match('/^\s*namespace\s+([^;]+);/m', $source, $namespaceMatch)) {
                    continue;
                }
                if (! preg_match('/^\s*(?:abstract\s+|final\s+|readonly\s+)*(?:class|interface|trait|enum)\s+(\w+)/m', $source, $classMatch)) {
                    continue;
                }

                $fqcn = trim($namespaceMatch[1]) . '\\' . $classMatch[1];
                $relativePath = substr($path, strlen($this->root()) + 1);

                if (isset($seenAt[$fqcn])) {
                    $offenders[] = "{$fqcn}: {$seenAt[$fqcn]} and {$relativePath}";
                } else {
                    $seenAt[$fqcn] = $relativePath;
                }
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "Two files declare the same namespace + class. Composer's optimize-autoloader classmap "
                . 'would pick one at random on the next install (F1).'
        );
    }
}
