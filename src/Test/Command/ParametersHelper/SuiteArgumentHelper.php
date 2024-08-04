<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Command\ParametersHelper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

final class SuiteArgumentHelper
{
    private const OPT_SUITE       = 'suite';
    private const OPT_SUITE_SHORT = 's';

    /**
     * @return array<InputOption>
     */
    public static function optionsDefinition(): array
    {
        return [
            new InputOption(
                self::OPT_SUITE,
                self::OPT_SUITE_SHORT,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'The test suite(s) to document',
                []
            ),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getSuites(InputInterface $input): array
    {
        $suites = $input->getOption(self::OPT_SUITE);

        if (!\is_array($suites)) {
            throw new \InvalidArgumentException('Bad test suite option given');
        }

        return $suites;
    }
}
