<?php

use Rector\Config\RectorConfig;
use Rector\Php82\Rector\Encapsed\VariableInStringInterpolationFixerRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use RectorLaravel\Rector\Class_\AddMockConsoleOutputFalseToConsoleTestsRector;
use RectorLaravel\Rector\Class_\AppendsPropertyToAppendsAttributeRector;
use RectorLaravel\Rector\Class_\DescriptionPropertyToDescriptionAttributeRector;
use RectorLaravel\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\HiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector;
use RectorLaravel\Rector\Class_\SignaturePropertyToSignatureAttributeRector;
use RectorLaravel\Rector\Class_\TablePropertyToTableAttributeRector;
use RectorLaravel\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector;
use RectorLaravel\Rector\ClassMethod\MigrateToSimplifiedAttributeRector;
use RectorLaravel\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector;
use RectorLaravel\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/app', __DIR__.'/Modules', __DIR__.'/config', __DIR__.'/database', __DIR__.'/routes', __DIR__.'/tests'])
    ->withSkip([
        '*/Resources/views/*',
        '*.blade.php',
        __DIR__.'/Modules/Cms/Includes/date/I18N/Arabic/Examples',
        __DIR__.'/tests/e2e',
        TablePropertyToTableAttributeRector::class,                            // modernisation, not deprecated: removes the $table property that code and packages may read directly
        FillablePropertyToFillableAttributeRector::class,                      // modernisation, not deprecated: removes the $fillable property that code and packages may read directly
        WithoutTimestampsPropertyToWithoutTimestampsAttributeRector::class,    // modernisation, not deprecated: removes the public $timestamps = false override, so direct reads see the base default
        EmptyGuardedPropertyToUnguardedAttributeRector::class,                 // modernisation, not deprecated: removes the $guarded property that code and packages may read directly
        AppendsPropertyToAppendsAttributeRector::class,                        // modernisation, not deprecated: moves $appends, which shapes toArray()/JSON output
        HiddenPropertyToHiddenAttributeRector::class,                          // modernisation, not deprecated: moves $hidden, which shapes toArray()/JSON output
        ModelCastsPropertyToCastsMethodRector::class,                          // modernisation, not deprecated: $casts property still supported; direct reads of $casts would change
        MigrateToSimplifiedAttributeRector::class,                             // modernisation, not deprecated: rewrites get*/set*Attribute accessors and mutators, changing method names and return types callers see
        ScopeNamedClassMethodToScopeAttributedClassMethodRector::class,        // modernisation, not deprecated: renames scope* methods, breaking direct scope*() calls
        DescriptionPropertyToDescriptionAttributeRector::class,                // modernisation, not deprecated: console $description property still supported
        SignaturePropertyToSignatureAttributeRector::class,                    // modernisation, not deprecated: console $signature property still supported
        ContainerBindConcreteWithClosureOnlyRector::class,                     // modernisation, not deprecated: adds a closure return type (new TypeError path) in the Glide provider Task 5 owns
        AddMockConsoleOutputFalseToConsoleTestsRector::class,                  // changes test behaviour (disables console output mocking); not a deprecation, suite is green without it
    ])
    ->withRules([
        ExplicitNullableParamTypeRector::class,          // PHP 8.4: implicit nullable parameter types are deprecated
        VariableInStringInterpolationFixerRector::class, // PHP 8.2: "${var}" interpolation is deprecated
    ])
    ->withSets([LaravelLevelSetList::UP_TO_LARAVEL_130]);
