<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Modules\Cms\Entities\Content;
use Modules\Frontend\Http\Controllers\FrontendController;
use Tests\TestCase;

/** S14: with no currconv key configured, the refresh makes no API call and keeps the stored rates. */
class CurrencyApiKeyTest extends TestCase
{
    use DatabaseTransactions;

    /** @var array<string, string|false> */
    private $proxies = [];

    protected function setUp(): void
    {
        parent::setUp();
        // Belt and braces: even if the guard regressed, curl could not leave the container.
        foreach (['http_proxy', 'https_proxy', 'HTTPS_PROXY'] as $name) {
            $this->proxies[$name] = getenv($name);
            putenv("{$name}=http://127.0.0.1:9");
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->proxies as $name => $value) {
            putenv($value === false ? $name : "{$name}={$value}");
        }
        parent::tearDown();
    }

    private function storedRates(): array
    {
        return Content::withDisabled()->where('type', 'currencies')->orderBy('id')->pluck('currency_value', 'id')->all();
    }

    public function test_an_empty_key_keeps_the_stored_rates_and_logs_why(): void
    {
        config(['services.currconv.key' => '']);
        $before = $this->storedRates();
        Log::spy();

        (new FrontendController)->storeCurrencies();

        $this->assertNotSame([], $before);
        $this->assertSame($before, $this->storedRates());
        Log::shouldHaveReceived('warning')->once()->with('Currency rates not refreshed: CURRCONV_API_KEY is not set.');
    }
}
