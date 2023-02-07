<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Tests\Test;

use Mockery\MockInterface;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestCase;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestScenario;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestSuite;
use PhpProject\LivingDocumentation\Test\Formatter\TestCaseFormatter;
use PhpProject\LivingDocumentation\Test\SimpleTestSuiteDocumentor;
use PhpProject\SourceCode\Classes\Filter\Filter;
use PhpProject\SourceCode\Classes\Identity\ClassIdentity;
use PhpProject\SourceCode\Classes\Identity\MethodIdentity;
use PhpProject\SourceCode\Classes\Manager\ClassManager;
use PhpProject\SourceCode\Classes\Reflection\Standard\StandardReflectionClass;
use PhpProject\SourceCode\Classes\SourceClass;
use PhpProject\SourceCode\Classes\SourceClasses;
use PhpProject\SourceCode\Classes\SourceMethod;
use PhpProject\SourceCode\Classes\SourceMethods;
use PhpProject\SourceCode\Files\Path\RelativePath;
use PhpProject\SourceCode\Files\SourceFile;
use PhpProject\SourceCode\Files\SourceFiles;
use PhpProject\TestSuite\Configuration\TestSuiteConfig;
use PhpProject\TestSuite\Configuration\TestSuitesConfig;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('string')]
#[Group('livingDocumentation')]
#[Group('tests')]
final class SimpleTestSuiteDocumentorTest extends TestCase
{
    private const SUITE_NAME  = 'Fake Suite';
    private const CLASS_NAME  = 'FakeClass';
    private const METHOD_NAME = 'fakeMethod';

    private SimpleTestSuiteDocumentor $documentor;

    private ClassManager&MockInterface $classManager;
    /**
     * @var string
     */
    private const TAB = '    ';

    protected function setUp(): void
    {
        $this->classManager = \Mockery::mock(ClassManager::class);
        $this->documentor   = new SimpleTestSuiteDocumentor(
            $this->classManager,
            new NullFormatter()
        );
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }

    #[Test]
    public function it_documents_a_test_method(): void
    {
        $_this = $this; // #ignoreLine

        $_method   = $_this->given_a_test_method();
        $_testCase = $_this->when_documenting_the_method($_method);
        $_this->the_documented_test_case_should_be_correctly_formatted($_testCase);
    }

    #[Test]
    public function it_documents_a_test_class(): void
    {
        $_this = $this; // #ignoreLine

        $_class    = $_this->given_a_test_class();
        $_scenario = $_this->when_documenting_the_class($_class);
        $_this->the_scenario_should_be_named_after_the_class($_scenario);
        $_testCase = $_this->there_should_be_one_documented_test_case($_scenario);
        $_this->the_documented_test_case_should_be_correctly_formatted($_testCase);
    }

    #[Test]
    public function it_documents_a_test_file(): void
    {
        $_this = $this; // #ignoreLine

        $_file     = $_this->given_a_test_file();
        $_this->given_the_file_contains_a_test_class();
        $_scenario = $_this->when_documenting_the_file($_file);
        $_scenario = $_this->the_scenario_must_be_created($_scenario);
        $_this->the_scenario_should_be_named_after_the_class($_scenario);
        $_testCase = $_this->there_should_be_one_documented_test_case($_scenario);
        $_this->the_documented_test_case_should_be_correctly_formatted($_testCase);
    }

    #[Test]
    public function it_fails_documenting_a_test_file_if_the_file_does_not_contain_a_test_class(): void
    {
        $_this = $this; // #ignoreLine

        $_file     = $_this->given_a_test_file();
        $_this->given_the_file_does_not_contain_a_test_class();
        $_scenario = $_this->when_documenting_the_file($_file);
        $_this->no_scenario_should_have_been_documented($_scenario);
    }

    #[Test]
    public function it_documents_a_test_suite(): void
    {
        $_this = $this; // #ignoreLine

        $_suite           = $_this->given_a_test_suite();
        $_this->given_the_suite_files_contain_a_test_class();
        $_documentedSuite = $_this->when_documenting_the_suite($_suite);
        $_scenario        = $_this->the_suite_should_contain_a_scenario($_documentedSuite);
        $_scenario        = $_this->the_scenario_must_be_created($_scenario);
        $_this->the_scenario_should_be_named_after_the_class($_scenario);
        $_testCase = $_this->there_should_be_one_documented_test_case($_scenario);
        $_this->the_documented_test_case_should_be_correctly_formatted($_testCase);
    }

    #[Test]
    public function it_documents_test_suites(): void
    {
        $_this = $this; // #ignoreLine

        $_suites           = $_this->given_test_suites();
        $_this->given_the_suite_files_contain_a_test_class();
        $_documentedSuites = $_this->when_documenting_the_suites($_suites);
        $_documentedSuite  = $_this->the_suites_should_contain_only_one_suite($_documentedSuites);
        $_scenario         = $_this->the_suite_should_contain_a_scenario($_documentedSuite);
        $_scenario         = $_this->the_scenario_must_be_created($_scenario);
        $_this->the_scenario_should_be_named_after_the_class($_scenario);
        $_testCase = $_this->there_should_be_one_documented_test_case($_scenario);
        $_this->the_documented_test_case_should_be_correctly_formatted($_testCase);
    }

    // 1. Arrange

    private function given_a_test_method(): SourceMethod
    {
        return SourceMethod::fromIdentity(
            MethodIdentity::fromReflectionMethod(
                StandardReflectionClass::createFromName(FakeClass::class)->getMethod(self::METHOD_NAME)
            )
        );
    }

    private function given_a_test_class(): SourceClass
    {
        return SourceClass::build(
            ClassIdentity::fromReflectionClass(StandardReflectionClass::createFromName(FakeClass::class)),
            $this->given_a_test_file(),
            new SourceMethods([$this->given_a_test_method()])
        );
    }

    private function given_a_test_file(): SourceFile
    {
        return SourceFile::fromPath(
            RelativePath::raw('lib-tests/LivingDocumentation/Test/TestSuiteDocumentorTest.php')
        );
    }

    private function given_a_test_suite(): TestSuiteConfig
    {
        return new TestSuiteConfig(
            self::SUITE_NAME,
            SourceFiles::fromSourceFile($this->given_a_test_file()),
            new Filter()
        );
    }

    private function given_test_suites(): TestSuitesConfig
    {
        return new TestSuitesConfig([
            $this->given_a_test_suite(),
        ]);
    }

    private function given_the_file_contains_a_test_class(): void
    {
        $this->classManager->shouldReceive('getClass')->andReturn($this->given_a_test_class());
    }

    private function given_the_file_does_not_contain_a_test_class(): void
    {
        $this->classManager->shouldReceive('getClass')->andReturn(null);
    }

    private function given_the_suite_files_contain_a_test_class(): void
    {
        $this->classManager->shouldReceive('getClasses')->andReturn(new SourceClasses([$this->given_a_test_class()]));
    }

    // 2. Act

    public function when_documenting_the_method(SourceMethod $method): DocumentedTestCase
    {
        return $this->documentor->documentTestMethod($method);
    }

    public function when_documenting_the_class(SourceClass $class): DocumentedTestScenario
    {
        return $this->documentor->documentTestClass($class);
    }

    private function when_documenting_the_file(SourceFile $file): ?DocumentedTestScenario
    {
        return $this->documentor->documentTestFile($file);
    }

    private function when_documenting_the_suite(TestSuiteConfig $suite): DocumentedTestSuite
    {
        return $this->documentor->documentTestSuite($suite);
    }

    /**
     * @return array<DocumentedTestSuite>
     */
    private function when_documenting_the_suites(TestSuitesConfig $suites): array
    {
        return $this->documentor->documentTestSuites($suites);
    }

    // 3. Assert

    public function the_documented_test_case_should_be_correctly_formatted(DocumentedTestCase $testCase): void
    {
        self::assertEquals(self::METHOD_NAME, $testCase->name);
        self::assertEquals(self::TAB.'public function '.self::METHOD_NAME.'(): void'.\PHP_EOL.self::TAB.'{'.\PHP_EOL.self::TAB.'}', $testCase->content);
    }

    public function there_should_be_one_documented_test_case(DocumentedTestScenario $scenario): DocumentedTestCase
    {
        self::assertCount(1, $scenario->testCases);

        return $scenario->testCases[0];
    }

    public function the_scenario_should_be_named_after_the_class(DocumentedTestScenario $scenario): void
    {
        self::assertEquals(self::CLASS_NAME, $scenario->name);
        self::assertEquals('', $scenario->description);
    }

    private function the_scenario_must_be_created(?DocumentedTestScenario $scenario): DocumentedTestScenario
    {
        self::assertNotNull($scenario);

        return $scenario;
    }

    private function no_scenario_should_have_been_documented(?DocumentedTestScenario $_scenario): void
    {
        self::assertNull($_scenario);
    }

    private function the_suite_should_contain_a_scenario(DocumentedTestSuite $documentedSuite): DocumentedTestScenario
    {
        self::assertCount(1, $documentedSuite->scenarii);

        return $documentedSuite->scenarii[0];
    }

    /**
     * @param array<DocumentedTestSuite> $documentedSuites
     */
    private function the_suites_should_contain_only_one_suite(array $documentedSuites): DocumentedTestSuite
    {
        self::assertCount(1, $documentedSuites);

        return $documentedSuites[0];
    }
}

final class NullFormatter implements TestCaseFormatter
{
    public function format(string $name, string $content): DocumentedTestCase
    {
        return new DocumentedTestCase($name, $content);
    }
}

final class FakeClass
{
    public function fakeMethod(): void
    {
    }
}
