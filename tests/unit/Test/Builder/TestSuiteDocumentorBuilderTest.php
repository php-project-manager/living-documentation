<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Tests\Test\Builder;

use Assert\InvalidArgumentException;
use PhpProject\LivingDocumentation\Test\Builder\TestSuiteDocumentorBuilder;
use PhpProject\LivingDocumentation\Test\SimpleTestSuiteDocumentor;
use PhpProject\SourceCode\Files\Manager\FileManager;
use PhpProject\SourceCode\Files\Path\AbsolutePath;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TestSuiteDocumentorBuilderTest extends TestCase
{
    private TestSuiteDocumentorBuilder $builder;

    protected function setUp(): void
    {
        $fileManager   = FileManager::build(AbsolutePath::clean(__DIR__.'/../../../..'));
        $this->builder = TestSuiteDocumentorBuilder::for($fileManager);
    }

    #[Test]
    public function it_builds_a_test_suite_documentor_using_composer(): void
    {
        $documentor = $this->builder
            ->usingComposer()
            ->build();

        self::assertInstanceOf(SimpleTestSuiteDocumentor::class, $documentor);
    }

    #[Test]
    public function it_cannot_build_a_test_suite_documentor_if_not_given_a_class_manager_strategy(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->builder->build();
    }
}
