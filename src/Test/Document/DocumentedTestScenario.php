<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Document;

final readonly class DocumentedTestScenario
{
    /**
     * @param array<DocumentedTestCase> $testCases
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public array $testCases
    ) {
    }
}
