<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureStaff;
use App\User;
use Illuminate\Http\Request;
use Tests\TestCase;

/** S8: a signed-in but denied caller that expects JSON gets a JSON 403; browsers keep the plain-text body. */
class EnsureStaffJsonTest extends TestCase
{
    private function deniedRequest(string $accept): Request
    {
        $request = Request::create('/en/admin/tinymce/uploader', 'POST', [], [], [], ['HTTP_ACCEPT' => $accept]);
        $user = new User();
        $user->disabled_at = now();
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    public function test_a_json_caller_gets_a_json_403(): void
    {
        $response = (new EnsureStaff)->handle($this->deniedRequest('application/json'), fn () => response('next'));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame(['success' => false, 'message' => 'Forbidden.'], json_decode($response->getContent(), true));
    }

    public function test_a_browser_still_gets_the_plain_text_403(): void
    {
        $response = (new EnsureStaff)->handle($this->deniedRequest('text/html'), fn () => response('next'));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('Forbidden.', $response->getContent());
    }
}
