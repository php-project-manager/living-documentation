<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Document;

final readonly class DocumentedTestCase
{
    public function __construct(
        public string $name,
        public string $content
    ) {
    }
}
