<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Modules\Notification\Entities\FirebaseToken;
use Modules\Notification\Http\Controllers\NotificationController;
use Tests\TestCase;

/** S6: a storage failure in postWebToken never returns exception details to the client. */
class WebTokenErrorResponseTest extends TestCase
{
    use DatabaseTransactions;

    public function test_a_storage_failure_returns_a_generic_error(): void
    {
        FirebaseToken::saving(function () {
            throw new \RuntimeException('simulated failure in /var/www/html/secret-path.php');
        });
        $request = Request::create('/en/admin/notification/postWebToken', 'POST', ['data_token' => 'phpunit-s6-token'], [], [], [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            'HTTP_ACCEPT'           => 'application/json',
            'REMOTE_ADDR'           => '203.0.113.9',
        ]);
        $this->app->instance('request', $request);

        $response = (new NotificationController)->postWebToken($request)->toResponse($request);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse(str_contains($response->getContent(), 'simulated failure'), 'the exception message leaked');
        $this->assertFalse(str_contains($response->getContent(), '/var/www'), 'a server path leaked');
        $this->assertSame([], json_decode($response->getContent(), true)['errors']);
    }
}
