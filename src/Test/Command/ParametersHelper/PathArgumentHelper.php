<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Command\ParametersHelper;

use Assert\Assert;
use PhpProject\SourceCode\Files\Path\AbsolutePath;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class PathArgumentHelper
{
    private const ARG_PATH     = 'path';

    /**
     * @return array<InputArgument>
     */
    public static function argumentsDefinition(): array
    {
        return [
            new InputArgument(
                self::ARG_PATH,
                InputArgument::OPTIONAL,
                'The path to the project to document',
                ''
            ),
        ];
    }

    public static function getPath(InputInterface $input): AbsolutePath
    {
        $givenPath = $input->getArgument(self::ARG_PATH);
        Assert::that($givenPath)->string();

        if (is_dir(getcwd().'/'.$givenPath)) {
            return AbsolutePath::raw((string) realpath(getcwd().'/'.$givenPath));
        }

        if (is_dir($givenPath)) {
            return AbsolutePath::raw((string) realpath($givenPath));
        }

        throw new \InvalidArgumentException('The given path is not a directory: .'.$givenPath);
    }
}
