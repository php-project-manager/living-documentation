<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Formatter;

use PhpProject\LivingDocumentation\Test\Document\DocumentedTestCase;

interface TestCaseFormatter
{
    public function format(string $name, string $content): DocumentedTestCase;
}
