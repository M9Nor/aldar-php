<?php

namespace Tests\Unit;

use App\DataTables\DataProcessor;
use App\DataTables\EloquentDataTable;
use App\Glide\Encoder;
use App\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Modules\Cms\Http\Controllers\Auth\ConfirmPasswordController;
use Modules\Cms\Http\Controllers\Auth\ForgotPasswordController;
use Modules\Cms\Http\Controllers\Auth\LoginController;
use Modules\Cms\Http\Controllers\Auth\RegisterController;
use Modules\Cms\Http\Controllers\Auth\ResetPasswordController;
use Modules\Cms\Http\Controllers\Auth\VerificationController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * S18: the Laravel 7 behaviour restorations fail loudly when a package update inside the composer
 * constraints removes or renames what they override.
 */
class RestorationOverrideGuardTest extends TestCase
{
    /** Class-method overrides: #[\Override] makes PHP refuse to load the class if the parent method goes away. */
    private const CLASS_OVERRIDES = [
        [LengthAwarePaginator::class, 'elements'],
        [EloquentDataTable::class, 'processResults'],
        [DataProcessor::class, 'escapeRow'],
        [Encoder::class, 'getQuality'],
    ];

    /**
     * Trait-method overrides in the laravel/ui auth controllers: controller => [method => trait declaring it].
     * #[\Override] cannot guard these (PHP fatals when the only same-named method comes from a trait).
     */
    public const TRAIT_OVERRIDES = [
        LoginController::class => [
            'showLoginForm'           => AuthenticatesUsers::class,
            'login'                   => AuthenticatesUsers::class,
            'attemptLogin'            => AuthenticatesUsers::class,
            'sendLoginResponse'       => AuthenticatesUsers::class,
            'sendFailedLoginResponse' => AuthenticatesUsers::class,
            'username'                => AuthenticatesUsers::class,
            'sendLockoutResponse'     => ThrottlesLogins::class,
        ],
        RegisterController::class => [
            'showRegistrationForm' => RegistersUsers::class,
            'register'             => RegistersUsers::class,
        ],
        ResetPasswordController::class => [
            'showResetForm'           => ResetsPasswords::class,
            'reset'                   => ResetsPasswords::class,
            'rules'                   => ResetsPasswords::class,
            'sendResetResponse'       => ResetsPasswords::class,
            'sendResetFailedResponse' => ResetsPasswords::class,
        ],
        ForgotPasswordController::class => [
            'showLinkRequestForm'         => SendsPasswordResetEmails::class,
            'validateEmail'               => SendsPasswordResetEmails::class,
            'sendResetLinkResponse'       => SendsPasswordResetEmails::class,
            'sendResetLinkFailedResponse' => SendsPasswordResetEmails::class,
        ],
        VerificationController::class    => [],
        ConfirmPasswordController::class => [],
    ];

    public function test_class_method_overrides_carry_the_override_attribute(): void
    {
        foreach (self::CLASS_OVERRIDES as [$class, $method]) {
            $reflection = new ReflectionMethod($class, $method);
            $this->assertSame($class, $reflection->getDeclaringClass()->getName(), "{$class}::{$method} must be declared in the class");
            $this->assertCount(1, $reflection->getAttributes(\Override::class), "{$class}::{$method} needs #[\\Override]");
        }
    }

    public function test_the_auth_controllers_trait_overrides_are_complete_and_still_match_their_traits(): void
    {
        foreach (self::TRAIT_OVERRIDES as $controller => $expected) {
            $controllerFile = (new ReflectionClass($controller))->getFileName();
            $overridden = [];
            foreach (class_uses_recursive($controller) as $trait) {
                foreach ((new ReflectionClass($trait))->getMethods() as $traitMethod) {
                    if ((new ReflectionMethod($controller, $traitMethod->getName()))->getFileName() === $controllerFile) {
                        $overridden[$traitMethod->getName()] = true;
                    }
                }
            }
            $actualNames = array_keys($overridden);
            sort($actualNames);
            $expectedNames = array_keys($expected);
            sort($expectedNames);
            $this->assertSame($expectedNames, $actualNames, "{$controller}: its trait overrides changed; update TRAIT_OVERRIDES after checking each one");

            foreach ($expected as $method => $trait) {
                $inTrait = new ReflectionMethod($trait, $method);
                $inController = new ReflectionMethod($controller, $method);
                $this->assertSame($inTrait->isPublic(), $inController->isPublic(), "{$controller}::{$method} visibility differs from {$trait}");
                $this->assertSame($inTrait->isProtected(), $inController->isProtected(), "{$controller}::{$method} visibility differs from {$trait}");
                $this->assertCount(0, $inController->getAttributes(\Override::class), "{$controller}::{$method} overrides a trait method: #[\\Override] would be fatal");
            }
        }
    }

    public function test_redirect_path_still_reads_the_controllers_redirect_to_method(): void
    {
        $method = new ReflectionMethod(RedirectsUsers::class, 'redirectPath');
        $lines = array_slice(file($method->getFileName()), $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);

        $this->assertStringContainsString("method_exists(\$this, 'redirectTo')", implode('', $lines));
    }
}
