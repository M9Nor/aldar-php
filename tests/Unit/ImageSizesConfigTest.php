<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImageSizesConfigTest extends TestCase
{
    public function test_every_size_literal_in_code_is_allowed(): void
    {
        $root = dirname(__DIR__, 2);
        $this->assertFileExists("{$root}/config/image_sizes.php");
        $allowed = (require "{$root}/config/image_sizes.php")['allowed'];

        $found = [];
        foreach (['Modules', 'app', 'config', 'resources'] as $dir) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("{$root}/{$dir}", RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($files as $file) {
                if (substr($file->getFilename(), -4) !== '.php') {
                    continue;
                }
                preg_match_all('/[\'"]((?:\d{2,4}|auto)x(?:\d{2,4}|auto))[\'"]/', file_get_contents($file->getPathname()), $matches);
                foreach ($matches[1] as $size) {
                    $found[$size] = true;
                }
            }
        }

        $this->assertSame([], array_values(array_diff(array_keys($found), $allowed)), 'Add these sizes to config/image_sizes.php');
    }
}
