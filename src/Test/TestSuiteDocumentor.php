<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test;

use PhpProject\LivingDocumentation\Test\Document\DocumentedTestCase;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestScenario;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestSuite;
use PhpProject\SourceCode\Classes\Filter\Filter;
use PhpProject\SourceCode\Classes\SourceClass;
use PhpProject\SourceCode\Classes\SourceMethod;
use PhpProject\SourceCode\Files\SourceFile;
use PhpProject\TestSuite\Configuration\TestSuiteConfig;
use PhpProject\TestSuite\Configuration\TestSuitesConfig;

interface TestSuiteDocumentor
{
    /**
     * @return array<DocumentedTestSuite>
     */
    public function documentTestSuites(TestSuitesConfig $testSuites): array;

    public function documentTestSuite(TestSuiteConfig $testSuite): DocumentedTestSuite;

    public function documentTestFile(SourceFile $sourceFile, Filter $filter = new Filter()): ?DocumentedTestScenario;

    public function documentTestClass(SourceClass $class): DocumentedTestScenario;

    public function documentTestMethod(SourceMethod $method): DocumentedTestCase;
}
