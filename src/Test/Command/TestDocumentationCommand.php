<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Command;

use PhpProject\LivingDocumentation\Test\Builder\TestSuiteDocumentorBuilder;
use PhpProject\LivingDocumentation\Test\Command\Display\DocumentedTestSuiteConsoleDisplay;
use PhpProject\LivingDocumentation\Test\Command\ParametersHelper\PathArgumentHelper;
use PhpProject\LivingDocumentation\Test\Command\ParametersHelper\SuiteArgumentHelper;
use PhpProject\SourceCode\Files\Manager\FileManager;
use PhpProject\TestSuite\Builder\TestSuiteConfigurationResolverBuilder;
use PhpProject\TestSuite\Configuration\TestSuitesConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: self::NAME)]
final class TestDocumentationCommand extends Command
{
    public const NAME = 'test:doc';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectPath = PathArgumentHelper::getPath($input);
        $output->writeln('Documenting test suites for project: '.$projectPath);

        $projectFileManager        = FileManager::build($projectPath);
        $testConfigurationResolver = TestSuiteConfigurationResolverBuilder::for($projectFileManager)->usingPhpUnit()->build();
        $testDocumentor            = TestSuiteDocumentorBuilder::for($projectFileManager)->usingComposer()->build();

        $suites = $testConfigurationResolver->getTestSuites();

        $testSuites = SuiteArgumentHelper::getSuites($input);
        if ($testSuites !== []) {
            $config = [];
            foreach ($suites as $suite) {
                if (\in_array($suite->name, $testSuites, true)) {
                    $config[] = $suite;
                }
            }
            $suites = new TestSuitesConfig($config);
        }

        $documentedTestSuites = $testDocumentor->documentTestSuites($suites);

        DocumentedTestSuiteConsoleDisplay::displayDocumentedTestSuites($documentedTestSuites, $output);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->getDefinition()
            ->addArgument(...PathArgumentHelper::argumentsDefinition());
        $this->getDefinition()
            ->addOption(...SuiteArgumentHelper::optionsDefinition());
    }
}
