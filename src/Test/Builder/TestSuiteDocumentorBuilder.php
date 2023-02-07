<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Builder;

use Assert\Assert;
use PhpProject\LivingDocumentation\Test\Formatter\SimpleTestCaseFormatter;
use PhpProject\LivingDocumentation\Test\Formatter\TestCaseFormatter;
use PhpProject\LivingDocumentation\Test\SimpleTestSuiteDocumentor;
use PhpProject\LivingDocumentation\Test\TestSuiteDocumentor;
use PhpProject\SourceCode\Classes\Builder\ClassManagerBuilder;
use PhpProject\SourceCode\Classes\Manager\ClassManager;
use PhpProject\SourceCode\Files\Manager\FileManager;
use PhpProject\SourceCode\Files\Path\RelativePath;

final class TestSuiteDocumentorBuilder
{
    private ?ClassManager $classManager = null;

    private function __construct(
        private readonly FileManager $projectFileManager,
        private readonly TestCaseFormatter $formatter = new SimpleTestCaseFormatter()
    ) {
    }

    public static function for(FileManager $projectFileManager): self
    {
        return new self($projectFileManager);
    }

    public function usingComposer(?RelativePath $autoloadFile = null): self
    {
        $this->classManager = ClassManagerBuilder::for($this->projectFileManager)
            ->usingComposer($autoloadFile)
            ->build();

        return $this;
    }

    public function build(): TestSuiteDocumentor
    {
        Assert::lazy()
            ->that($this->classManager)->notNull('You have to configure a class manager.')
            ->verifyNow();

        return new SimpleTestSuiteDocumentor(
            $this->classManager, // @phpstan-ignore-line
            $this->formatter
        );
    }
}
