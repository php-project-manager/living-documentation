<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Tests\Test\Command;

use PhpProject\LivingDocumentation\Test\Command\TestDocumentationCommand;
use PhpProject\LivingDocumentation\Tests\Helper\Cli\InMemoryOutput;
use PhpProject\LivingDocumentation\Tests\Test\Command\Display\DocumentedTestSuiteConsoleDisplayTest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\StringInput;

final class TestDocumentationCommandTest extends TestCase
{
    private InMemoryOutput $output;

    private TestDocumentationCommand $command;

    protected function setUp(): void
    {
        $this->output = new InMemoryOutput();

        $this->command = new TestDocumentationCommand();
    }

    /**
     * @throws ExceptionInterface
     */
    #[Test]
    public function it_documents_the_tests_in_the_given_project(): void
    {
        $return = $this->command->run(new StringInput('tests/data/tests-to-document'), $this->output);

        self::assertEquals(0, $return);
        self::assertMatchesRegularExpression('/Documenting test suites for project: .*tests\/data\/tests-to-document/', $this->output->lines[0]);

        DocumentedTestSuiteConsoleDisplayTest::checkTestSuitesAreDisplayedProperly(
            $this->output->lines,
            [
                [
                    'title'     => 'Documentation',
                    'scenarios' => [
                        [
                            'title'     => 'DocumentationTest',
                            'testCases' => [
                                [
                                    'title'   => 'It documents the tests in the given project',
                                    'content' => 'Given it is a phpUnit test'.\PHP_EOL.'When it is parsed by the test documentor'.\PHP_EOL.'It describes a test that is documented',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            1
        );
    }
}
