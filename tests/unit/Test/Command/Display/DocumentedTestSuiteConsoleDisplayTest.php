<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Tests\Test\Command\Display;

use PhpProject\LivingDocumentation\Test\Command\Display\DocumentedTestSuiteConsoleDisplay;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestCase;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestScenario;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestSuite;
use PhpProject\LivingDocumentation\Tests\Helper\Cli\InMemoryOutput;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DocumentedTestSuiteConsoleDisplayTest extends TestCase
{
    private InMemoryOutput $output;

    protected function setUp(): void
    {
        $this->output = new InMemoryOutput();
    }

    #[Test]
    public function it_should_display_a_documented_test_case(): void
    {
        $testCase = $this->aDocumentedTestCase('My test case', 'First Line'.\PHP_EOL.'Second Line');
        DocumentedTestSuiteConsoleDisplay::displayDocumentedTestCase($testCase, $this->output);

        self::assertCount(1, $this->output->lines);
        self::checkTestCaseIsDisplayedProperly($this->output->lines[0], 'My test case', 'First Line'.\PHP_EOL.'Second Line');
    }

    #[Test]
    public function it_should_display_a_documented_test_scenario(): void
    {
        $testScenario = $this->aDocumentedTestScenario(
            'My test scenario',
            'My test scenario description',
            $this->aDocumentedTestCase('My test case', 'First Line'.\PHP_EOL.'Second Line'),
            $this->aDocumentedTestCase('My test case 2', 'First Line #2'.\PHP_EOL.'Second Line #2')
        );
        DocumentedTestSuiteConsoleDisplay::displayDocumentedTestScenario($testScenario, $this->output);

        self::checkTestScenarioIsDisplayedProperly(
            $this->output->lines,
            'My test scenario',
            [
                ['title' => 'My test case', 'content' => 'First Line'.\PHP_EOL.'Second Line'],
                ['title' => 'My test case 2', 'content' => 'First Line #2'.\PHP_EOL.'Second Line #2'],
            ]
        );
    }

    #[Test]
    public function it_should_display_a_documented_test_suite(): void
    {
        $testSuite = $this->aDocumentedTestSuite(
            'My test suite',
            $this->aDocumentedTestScenario(
                'My test scenario',
                'My test scenario description',
                $this->aDocumentedTestCase('My test case', 'First Line'.\PHP_EOL.'Second Line'),
                $this->aDocumentedTestCase('My test case 2', 'First Line #2'.\PHP_EOL.'Second Line #2')
            ),
            $this->aDocumentedTestScenario(
                'My test scenario 2',
                'My test scenario description 2',
                $this->aDocumentedTestCase('My test case 3', 'First Line #3'.\PHP_EOL.'Second Line #3'),
                $this->aDocumentedTestCase('My test case 4', 'First Line #4'.\PHP_EOL.'Second Line #4')
            )
        );
        DocumentedTestSuiteConsoleDisplay::displayDocumentedTestSuite($testSuite, $this->output);

        self::checkTestSuiteIsDisplayedProperly(
            $this->output->lines,
            'My test suite',
            [
                [
                    'title'     => 'My test scenario',
                    'testCases' => [
                        ['title' => 'My test case', 'content' => 'First Line'.\PHP_EOL.'Second Line'],
                        ['title' => 'My test case 2', 'content' => 'First Line #2'.\PHP_EOL.'Second Line #2'],
                    ],
                ],
                [
                    'title'     => 'My test scenario 2',
                    'testCases' => [
                        ['title' => 'My test case 3', 'content' => 'First Line #3'.\PHP_EOL.'Second Line #3'],
                        ['title' => 'My test case 4', 'content' => 'First Line #4'.\PHP_EOL.'Second Line #4'],
                    ],
                ],
            ]
        );
    }

    #[Test]
    public function it_should_display_documented_test_suites(): void
    {
        $testSuite1 = $this->aDocumentedTestSuite(
            'My test suite',
            $this->aDocumentedTestScenario(
                'My test scenario',
                'My test scenario description',
                $this->aDocumentedTestCase('My test case', 'First Line'.\PHP_EOL.'Second Line'),
                $this->aDocumentedTestCase('My test case 2', 'First Line #2'.\PHP_EOL.'Second Line #2')
            ),
            $this->aDocumentedTestScenario(
                'My test scenario 2',
                'My test scenario description 2',
                $this->aDocumentedTestCase('My test case 3', 'First Line #3'.\PHP_EOL.'Second Line #3'),
                $this->aDocumentedTestCase('My test case 4', 'First Line #4'.\PHP_EOL.'Second Line #4')
            )
        );
        $testSuite2 = $this->aDocumentedTestSuite(
            'My test suite 2',
            $this->aDocumentedTestScenario(
                'My test scenario 3',
                'My test scenario description 3',
                $this->aDocumentedTestCase('My test case 5', 'First Line #5'.\PHP_EOL.'Second Line #5'),
                $this->aDocumentedTestCase('My test case 6', 'First Line #6'.\PHP_EOL.'Second Line #6')
            ),
            $this->aDocumentedTestScenario(
                'My test scenario 4',
                'My test scenario description 4',
                $this->aDocumentedTestCase('My test case 7', 'First Line #7'.\PHP_EOL.'Second Line #7'),
                $this->aDocumentedTestCase('My test case 8', 'First Line #8'.\PHP_EOL.'Second Line #8')
            )
        );
        $testSuites = [$testSuite1, $testSuite2];
        DocumentedTestSuiteConsoleDisplay::displayDocumentedTestSuites($testSuites, $this->output);

        self::checkTestSuitesAreDisplayedProperly(
            $this->output->lines,
            [
                [
                    'title'     => 'My test suite',
                    'scenarios' => [
                        [
                            'title'     => 'My test scenario',
                            'testCases' => [
                                ['title' => 'My test case', 'content' => 'First Line'.\PHP_EOL.'Second Line'],
                                ['title' => 'My test case 2', 'content' => 'First Line #2'.\PHP_EOL.'Second Line #2'],
                            ],
                        ],
                        [
                            'title'     => 'My test scenario 2',
                            'testCases' => [
                                ['title' => 'My test case 3', 'content' => 'First Line #3'.\PHP_EOL.'Second Line #3'],
                                ['title' => 'My test case 4', 'content' => 'First Line #4'.\PHP_EOL.'Second Line #4'],
                            ],
                        ],
                    ],
                ],
                [
                    'title'     => 'My test suite 2',
                    'scenarios' => [
                        [
                            'title'     => 'My test scenario 3',
                            'testCases' => [
                                ['title' => 'My test case 5', 'content' => 'First Line #5'.\PHP_EOL.'Second Line #5'],
                                ['title' => 'My test case 6', 'content' => 'First Line #6'.\PHP_EOL.'Second Line #6'],
                            ],
                        ],
                        [
                            'title'     => 'My test scenario 4',
                            'testCases' => [
                                ['title' => 'My test case 7', 'content' => 'First Line #7'.\PHP_EOL.'Second Line #7'],
                                ['title' => 'My test case 8', 'content' => 'First Line #8'.\PHP_EOL.'Second Line #8'],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public static function checkTestCaseIsDisplayedProperly(string $line, string $title, string $content): void
    {
        self::assertEquals('<question>'.$title.'</>'.\PHP_EOL.'<fg=yellow>'.$content.'</>'.\PHP_EOL, $line);
    }

    /**
     * @param array<string>                                $lines
     * @param array<array{title: string, content: string}> $testCases
     */
    public static function checkTestScenarioIsDisplayedProperly(array $lines, string $title, array $testCases, int $offset = 0): int
    {
        self::assertEquals('<info>'.$title.'</>'.\PHP_EOL, $lines[$offset]);

        ++$offset;
        foreach ($testCases as $testCase) {
            self::checkTestCaseIsDisplayedProperly($lines[$offset], ...$testCase);
            ++$offset;
        }

        return $offset;
    }

    /**
     * @param array<string>                                                                        $lines
     * @param array<array{title: string, testCases: array<array{title: string, content: string}>}> $scenarios
     */
    public static function checkTestSuiteIsDisplayedProperly(array $lines, string $title, array $scenarios, int $offset = 0): int
    {
        self::assertEquals('<error>'.$title.'</>'.\PHP_EOL, $lines[$offset]);

        ++$offset;
        foreach ($scenarios as $scenario) {
            $offset = self::checkTestScenarioIsDisplayedProperly(
                $lines,
                $scenario['title'],
                $scenario['testCases'],
                $offset
            );
        }

        return $offset;
    }

    /**
     * @param array<string>                                                                                                                $lines
     * @param array<array{title: string, scenarios: array<array{title: string, testCases: array<array{title: string, content: string}>}>}> $suites
     */
    public static function checkTestSuitesAreDisplayedProperly(array $lines, array $suites, int $offset = 0): int
    {
        foreach ($suites as $suite) {
            $offset = self::checkTestSuiteIsDisplayedProperly(
                $lines,
                $suite['title'],
                $suite['scenarios'],
                $offset
            );
        }

        return $offset;
    }

    public function aDocumentedTestCase(string $title, string $content): DocumentedTestCase
    {
        return new DocumentedTestCase($title, $content);
    }

    public function aDocumentedTestScenario(string $title, string $description, DocumentedTestCase ...$testCases): DocumentedTestScenario
    {
        return new DocumentedTestScenario($title, $description, $testCases);
    }

    private function aDocumentedTestSuite(string $title, DocumentedTestScenario ...$testScenarios): DocumentedTestSuite
    {
        return new DocumentedTestSuite($title, $testScenarios);
    }
}
