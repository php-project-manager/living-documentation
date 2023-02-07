<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Tests\Test\Command\ParametersHelper;

use PhpProject\LivingDocumentation\Test\Command\ParametersHelper\PathArgumentHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\StringInput;

final class PathArgumentHelperTest extends TestCase
{
    #[Test]
    public function it_returns_the_execution_path_by_default(): void
    {
        $input = new StringInput('');
        $input->bind(new InputDefinition(PathArgumentHelper::argumentsDefinition()));

        $path = PathArgumentHelper::getPath($input);
        self::assertEquals(getcwd(), (string) $path);
    }

    #[Test]
    public function it_returns_the_given_relative_path(): void
    {
        $input = new StringInput('tests/data');
        $input->bind(new InputDefinition(PathArgumentHelper::argumentsDefinition()));

        $path = PathArgumentHelper::getPath($input);
        self::assertEquals(getcwd().'/tests/data', (string) $path);
    }

    #[Test]
    public function it_returns_the_given_absolute_path(): void
    {
        $input = new StringInput((string) realpath(__DIR__.'/../../../../data'));
        $input->bind(new InputDefinition(PathArgumentHelper::argumentsDefinition()));

        $path = PathArgumentHelper::getPath($input);
        self::assertEquals(realpath(__DIR__.'/../../../../data'), (string) $path);
    }

    #[Test]
    public function it_fails_returning_a_non_existing_path(): void
    {
        $input = new StringInput('dummy');
        $input->bind(new InputDefinition(PathArgumentHelper::argumentsDefinition()));

        $this->expectException(\InvalidArgumentException::class);
        PathArgumentHelper::getPath($input);
    }
}
