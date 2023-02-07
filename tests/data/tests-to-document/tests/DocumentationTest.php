<?php

declare(strict_types=1);

namespace Tests\ToDocument;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DocumentationTest extends TestCase
{
    #[Test]
    public function it_documents_the_tests_in_the_given_project(): void
    {
        $_this = $this; // #ignoreLine

        $_this->given_it_is_a_phpUnit_test();
        $_this->when_it_is_parsed_by_the_test_documentor();
        $_this->it_describes_a_test_that_is_documented();
    }

    private function given_it_is_a_phpUnit_test(): void
    {
    }

    private function when_it_is_parsed_by_the_test_documentor(): void
    {
    }

    private function it_describes_a_test_that_is_documented(): void
    {
    }
}
