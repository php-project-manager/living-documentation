<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Document;

final readonly class DocumentedTestSuite
{
    /**
     * @param array<DocumentedTestScenario> $scenarii
     */
    public function __construct(
        public string $name,
        public array $scenarii
    ) {
    }
}
