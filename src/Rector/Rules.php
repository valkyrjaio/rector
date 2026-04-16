<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja Rector package.
 *
 * (c) Melech Mizrachi <melechmizrachi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Rector;

use Rector\CodeQuality\Rector\Class_\ConvertStaticToSelfRector;
use Rector\CodingStyle\Rector\Stmt\RemoveUselessAliasInUseStatementRector;
use Rector\CodingStyle\Rector\Use_\SeparateMultiUseImportsRector;
use Rector\Config\RectorConfig;
use Rector\Configuration\RectorConfigBuilder;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector;
use Rector\Php55\Rector\ClassConstFetch\StaticToSelfOnFinalClassRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Php84\Rector\MethodCall\NewMethodCallWithoutParenthesesRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Valkyrja\Rector\CodingStyle\Rector\Stmt\RemoveNonConflictingAliasInUseStatementRector;

class Rules
{
    public static function getConfig(): RectorConfigBuilder
    {
        $rector = RectorConfig::configure();

        return $rector
            ->withParallel()
            ->withImportNames(removeUnusedImports: true)
            ->withRules([
                AddVoidReturnTypeWhereNoReturnRector::class,
                AddOverrideAttributeToOverriddenMethodsRector::class,
                ConvertStaticToSelfRector::class,
                ExplicitNullableParamTypeRector::class,
                NewMethodCallWithoutParenthesesRector::class,
                RemoveNonConflictingAliasInUseStatementRector::class,
                RemoveParentCallWithoutParentRector::class,
                RemoveUselessAliasInUseStatementRector::class,
                RemoveUselessParamTagRector::class,
                RemoveUselessReturnTagRector::class,
                SeparateMultiUseImportsRector::class,
                StaticToSelfOnFinalClassRector::class,
            ]);
    }
}
