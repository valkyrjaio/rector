<p align="center"><a href="https://valkyrja.io" target="_blank">
    <img src="https://raw.githubusercontent.com/valkyrjaio/art/refs/heads/master/full-logo/orange/php.png" width="400">
</a></p>

# Valkyrja Rector

Rector configuration and custom rules for the Valkyrja project.

## Overview

This repository contains two things:

1. **`Valkyrja\Rector\Rules`** — a reusable `RectorConfigBuilder` factory that
   wires up all rules used across the Valkyrja monorepo. Drop it into any
   `rector.php` config and set your paths on the returned builder.

2. **Custom Rector rules** — project-specific refactoring rules that fill gaps
   in the built-in Rector library.

## Requirements

- PHP >= 8.4
- [`rector/rector`](https://github.com/rectorphp/rector) ^2.3.9

## Installation

```bash
composer require valkyrja/rector
```

## Usage

Call `Rules::getConfig()` from your `rector.php` configuration file and set the
source paths on the returned builder:

```php
// rector.php
use Valkyrja\Rector\Rules;

return Rules::getConfig()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
```

`getConfig()` takes no arguments and returns a `RectorConfigBuilder`. Chain any
additional Rector configuration — paths, skip rules, PHP version sets, etc. —
directly on the returned builder before returning it.

Run Rector as normal:

```bash
vendor/bin/rector process
```

Or in dry-run mode to preview changes without writing them:

```bash
vendor/bin/rector process --dry-run
```

## Configuration Details

### Parallel Execution

`withParallel()` is enabled, allowing Rector to process files concurrently
across multiple worker processes.

### Import Names

`withImportNames(removeUnusedImports: true)` is set, so Rector will:

- Add fully-qualified `use` imports for any names that aren't already imported
- Remove `use` statements that are no longer referenced

### Rules

#### Built-in Rules

| Rule                                            | Description                                                                                      |
|-------------------------------------------------|--------------------------------------------------------------------------------------------------|
| `AddVoidReturnTypeWhereNoReturnRector`          | Adds `: void` return type to methods that have no `return` statement                             |
| `AddOverrideAttributeToOverriddenMethodsRector` | Adds `#[Override]` attribute to methods that override a parent                                   |
| `ConvertStaticToSelfRector`                     | Converts `static::` to `self::` inside non-final classes where late static binding is not needed |
| `ExplicitNullableParamTypeRector`               | Converts `?Type` param declarations to `Type\|null` union syntax                                 |
| `NewMethodCallWithoutParenthesesRector`         | Removes unnecessary parentheses from `new` expressions used directly in method chains (PHP 8.4)  |
| `RemoveParentCallWithoutParentRector`           | Removes `parent::method()` calls when no parent class exists                                     |
| `RemoveUselessAliasInUseStatementRector`        | Removes aliases in `use` statements where the alias matches the class's own short name           |
| `RemoveUselessParamTagRector`                   | Removes `@param` PHPDoc tags whose type is already expressed in the signature                    |
| `RemoveUselessReturnTagRector`                  | Removes `@return` PHPDoc tags whose type is already expressed in the signature                   |
| `SeparateMultiUseImportsRector`                 | Splits `use A, B;` into individual `use A; use B;` statements                                    |
| `StaticToSelfOnFinalClassRector`                | Converts `static::` to `self::` inside final classes                                             |

#### Custom Rules

### `RemoveNonConflictingAliasInUseStatementRector`

`Valkyrja\Rector\CodingStyle\Rector\Stmt\RemoveNonConflictingAliasInUseStatementRector`

Removes aliases from `use` statements when the alias serves no purpose — i.e.
when nothing in the file would conflict if the alias were dropped.

The built-in `RemoveUselessAliasInUseStatementRector` only removes an alias when
it is identical to the imported class's short name. This rule goes further: it
removes the alias whenever keeping it is unnecessary, checking for conflicts
against:

- Other `use` statements in the file (by class name and by alias)
- The file's own class, interface, trait, or enum name

When the alias is removed, all references to it throughout the file — including
PHPDoc comments, type declarations, `extends`, `implements`, and `use` (traits)
— are rewritten to the unaliased short name.

**Before:**

```php
use App\Bar as AppBar;

class Foo
{
    public function baz(AppBar $bar): void {}
}
```

**After:**

```php
use App\Bar;

class Foo
{
    public function baz(Bar $bar): void {}
}
```

An alias is preserved whenever it would cause a naming conflict — for example
when two imports share the same short name, or when the short name clashes with
the file's own class name.
