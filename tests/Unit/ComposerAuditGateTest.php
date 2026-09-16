<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/** S4: the composer audit gate exits non-zero on any advisory and on an unreadable report. */
class ComposerAuditGateTest extends TestCase
{
    /** @return array{0: int, 1: string} the exit code and the combined output */
    private function runGate(string $report): array
    {
        $file = tempnam(sys_get_temp_dir(), 'composer-audit-');
        file_put_contents($file, $report);
        exec('bash ' . escapeshellarg(dirname(__DIR__, 2) . '/scripts/check-composer-audit.sh') . ' ' . escapeshellarg($file) . ' 2>&1', $output, $code);
        unlink($file);

        return [$code, implode("\n", $output)];
    }

    public function test_a_report_without_advisories_passes(): void
    {
        [$code, $output] = $this->runGate(json_encode(['advisories' => [], 'abandoned' => []]));

        $this->assertSame(0, $code, $output);
        $this->assertStringContainsString('composer audit: no known advisories', $output);
    }

    public function test_one_advisory_fails_the_gate_and_names_the_package(): void
    {
        [$code, $output] = $this->runGate(json_encode(['advisories' => ['vendor/package' => [[
            'advisoryId' => 'PKSA-test-0001', 'packageName' => 'vendor/package', 'title' => 'Test advisory', 'cve' => 'CVE-0000-0000',
        ]]]]));

        $this->assertSame(1, $code, $output);
        $this->assertStringContainsString('vendor/package', $output);
    }

    public function test_an_unreadable_report_fails_closed(): void
    {
        [$code] = $this->runGate('not json');

        $this->assertSame(2, $code);
    }
}
