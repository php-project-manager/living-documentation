<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Command\Display;

use PhpProject\LivingDocumentation\Test\Document\DocumentedTestCase;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestScenario;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestSuite;
use Symfony\Component\Console\Output\OutputInterface;

final class DocumentedTestSuiteConsoleDisplay
{
    /**
     * @param array<DocumentedTestSuite> $suites
     */
    public static function displayDocumentedTestSuites(array $suites, OutputInterface $output): void
    {
        foreach ($suites as $suite) {
            self::displayDocumentedTestSuite($suite, $output);
        }
    }

    public static function displayDocumentedTestSuite(DocumentedTestSuite $suite, OutputInterface $output): void
    {
        $output->writeln('<error>'.$suite->name.'</>');
        foreach ($suite->scenarii as $scenario) {
            self::displayDocumentedTestScenario($scenario, $output);
        }
    }

    public static function displayDocumentedTestScenario(DocumentedTestScenario $scenario, OutputInterface $output): void
    {
        $output->writeln('<info>'.$scenario->name.'</>');

        foreach ($scenario->testCases as $testCase) {
            self::displayDocumentedTestCase($testCase, $output);
        }
    }

    public static function displayDocumentedTestCase(DocumentedTestCase $testCase, OutputInterface $output): void
    {
        $output->writeln([
            '<question>'.$testCase->name.'</>',
            '<fg=yellow>'.$testCase->content.'</>',
        ]);
    }
}
