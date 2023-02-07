<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test;

use PhpProject\LivingDocumentation\Test\Document\DocumentedTestCase;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestScenario;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestSuite;
use PhpProject\LivingDocumentation\Test\Formatter\TestCaseFormatter;
use PhpProject\SourceCode\Classes\Filter\Filter;
use PhpProject\SourceCode\Classes\Manager\ClassManager;
use PhpProject\SourceCode\Classes\SourceClass;
use PhpProject\SourceCode\Classes\SourceMethod;
use PhpProject\SourceCode\Files\SourceFile;
use PhpProject\TestSuite\Configuration\TestSuiteConfig;
use PhpProject\TestSuite\Configuration\TestSuitesConfig;

final readonly class SimpleTestSuiteDocumentor implements TestSuiteDocumentor
{
    public function __construct(
        private ClassManager $classManager,
        private TestCaseFormatter $formatter
    ) {
    }

    /**
     * @return array<DocumentedTestSuite>
     */
    public function documentTestSuites(TestSuitesConfig $testSuites): array
    {
        return array_map(
            fn (TestSuiteConfig $testSuite): DocumentedTestSuite => $this->documentTestSuite($testSuite),
            $testSuites->asArray()
        );
    }

    public function documentTestSuite(TestSuiteConfig $testSuite): DocumentedTestSuite
    {
        return new DocumentedTestSuite(
            $testSuite->name,
            array_map(
                fn (SourceClass $class): DocumentedTestScenario => $this->documentTestClass($class),
                $this->classManager->getClasses($testSuite->source, $testSuite->filter)->asArray()
            )
        );
    }

    public function documentTestFile(
        SourceFile $sourceFile,
        Filter $filter = new Filter()
    ): ?DocumentedTestScenario {
        $class = $this->classManager->getClass($sourceFile, $filter);

        if (!$class instanceof SourceClass) {
            return null;
        }

        return $this->documentTestClass($class);
    }

    public function documentTestClass(
        SourceClass $class
    ): DocumentedTestScenario {
        $testCases = array_map(
            fn (SourceMethod $method): DocumentedTestCase => $this->documentTestMethod($method),
            $class->methods->asArray()
        );

        return new DocumentedTestScenario(
            $class->shortName(),
            '',
            array_filter($testCases)
        );
    }

    public function documentTestMethod(
        SourceMethod $method
    ): DocumentedTestCase {
        return $this->formatter->format($method->name(), $method->body);
    }
}
