<?php

declare(strict_types=1);

namespace Kayw\QualityHook\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(name: 'run')]
class RunCommand extends Command
{
    private const OPTION_PHPSTAN          = 'phpstan';
    private const OPTION_PHP_FIXER        = 'php-cs-fixer';
    private const ARGUMENT_INSTALL        = 'install';
    private const ARGUMENT_UNINSTALL      = 'uninstall';
    private const PHPCSFIXER_CONFIG_FILES = [
        '.php-cs-fixer.php',
        '.php-cs-fixer.dist.php',
    ];
    private string $phpCsFixerConfig;

    protected function configure(): void
    {
        $this->setDescription('Put code quality inspection to git hook');
        $this->setHelp('Install an execute script of specify quality tools to your git pre-commit hook, and it executes only for changed files');
        $this->addArgument(self::ARGUMENT_INSTALL, InputArgument::OPTIONAL, 'Install a execute script of specify quality tools to your git per-commit hook');
        $this->addArgument(self::ARGUMENT_UNINSTALL, InputArgument::OPTIONAL, 'Uninstall a execute script of specify quality tools to your git per-commit hook');
        $this->addOption(self::OPTION_PHPSTAN, '', InputOption::VALUE_NONE);
        $this->addOption(self::OPTION_PHP_FIXER, '', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $active_options = array_keys(array_filter($input->getOptions(), fn ($option) => $option));

        return match ($input->getArguments() !== []) {
            $input->getArgument(self::ARGUMENT_INSTALL) !== null   => $this->executeInstall($active_options, $input, $output),
            $input->getArgument(self::ARGUMENT_UNINSTALL) !== null => $this->executeUninstall($input, $output),
            default                                                => $this->executeRun($active_options, $input, $output)
        };
    }

    private function checkPhpCsFixerConfigFileExist(): bool
    {
        $base_path = $this->getPath();

        foreach (self::PHPCSFIXER_CONFIG_FILES as $config_file) {
            if (file_exists($base_path . '/' . $config_file)) {
                $this->phpCsFixerConfig = $config_file;

                return true;
            }
        }

        return false;
    }

    private function executeRun(array $active_options, InputInterface $input, OutputInterface $output): int
    {
        exec('git status -v', $exec_output);

        if ($exec_output === '') {
            return self::SUCCESS;
        }

        $change_files_string = implode(' ', array_map(function (string $output_line): string {
            return trim(substr($output_line, 2));
        }, array_filter($exec_output, fn (string $output_line) => !str_starts_with($output_line, 'D'))));

        if ($change_files_string === '') {
            return self::SUCCESS;
        }

        $option_text = implode(' and ', array_map(fn ($option) => strtoupper($option), $active_options));

        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $answer = $helper->ask($input, $output, new ConfirmationQuestion(PHP_EOL . '<info> Do you want to use ' . $option_text . ' to fix these files? [ Y or N ] </info>'));

        if (!$answer) {
            return self::FAILURE;
        }

        $result_code = 0;

        foreach ($active_options as $active_option) {
            $result_code += match ($active_option) {
                self::OPTION_PHP_FIXER => $this->runPhpCsFixerFix($output, $change_files_string),
                self::OPTION_PHPSTAN   => $this->runPhpStanFix($output, $change_files_string),
            };
        }

        if ($result_code > 0) {
            $output->writeln('<error> Exist some problems, please retry after fixed them</error>');

            return self::FAILURE;
        }

        $output->writeln('Quality tools has completed');

        return self::SUCCESS;
    }

    private function getPath(): string
    {
        exec('pwd', $base_path);

        return $base_path[0];
    }

    private function runPhpCsFixerFix(OutputInterface $output, string $change_files_string): int
    {
        if (!$this->checkPhpCsFixerConfigFileExist()) {
            $output->writeln('<error> The PHPCSFixer config file not found on your project</error>');

            return self::FAILURE;
        }

        $output->writeln('<info> PHP CS Fixer Fixing ...</info>');
        $base_path = $this->getPath();
        exec("$base_path/vendor/bin/php-cs-fixer fix --config $base_path/$this->phpCsFixerConfig $change_files_string", result_code: $result_code);

        return $result_code;
    }

    private function runPhpStanFix(OutputInterface $output, string $change_files_string): int
    {
        $output->writeln('<info> PHPStan analysing ...</info>');
        $base_path = $this->getPath();
        exec("$base_path/vendor/bin/phpstan analyse --debug -v --memory-limit 2048M $change_files_string", $phpstan_outputs, $result_code);

        if ($result_code === 0) {
            $output->writeln('<info> [OK] No errors</info>');

            return $result_code;
        }

        foreach ($phpstan_outputs as $phpstan_output) {
            $output->writeln('<info>' . $phpstan_output . '</info>');
        }

        return $result_code;
    }

    private function executeInstall(array $active_options, InputInterface $input, OutputInterface $output): int
    {
        $option_text = implode(' ', array_map(fn (string $active_option): string => '--' . $active_option, $active_options));
        $execute_command = <<<EOF
            #!/usr/bin/env php

            quality run $option_text
            EOF;
        file_put_contents($this->getPath() . '/.git/hooks/pre-commit', $execute_command);
        $output->writeln('<info> The hook install success!</info>');

        return self::SUCCESS;
    }

    private function executeUninstall(InputInterface $input, OutputInterface $output): int
    {
        file_put_contents($this->getPath() . '/.git/hooks/pre-commit', '');
        $output->writeln('<info> The hook uninstall success!</info>');

        return self::SUCCESS;
    }
}