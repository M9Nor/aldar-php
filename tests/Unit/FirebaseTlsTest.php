<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FirebaseTlsTest extends TestCase
{
    public function test_fcm_request_verifies_the_tls_certificate(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/Modules/Notification/Entities/FirebaseNotification.php');

        $this->assertStringContainsString('CURLOPT_SSL_VERIFYHOST , 2', $source);
        $this->assertStringContainsString('CURLOPT_SSL_VERIFYPEER , true', $source);
        $this->assertStringNotContainsString('CURLOPT_SSL_VERIFYPEER , 0', $source);
    }
}
