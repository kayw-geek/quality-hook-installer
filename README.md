# Quality Hook Installer

Install an execute script of specify quality tools to your git pre-commit hook, and it executes only for changed files
## Install

```BASH
composer global kayw/quality-hook-installer
```

## Usage

1. `quality run install --phpstan --php-cs-fixer`
2. Execute `git add .` in your project.
3. `git commit -m 'xxx'`
4. The pre-commit hook will be triggered and the PHPStan and PHPCsFixer will execute only for changed files.

## Commands

The following command will execute quality inspection only for changed files

`quality run --phpstan --php-cs-fixer`

The following command will write in your pre-commit of git hook

`quality run install --phpstan --php-cs-fixer`

The following command will remove your pre-commit of git hook

`quality run uninstall`
