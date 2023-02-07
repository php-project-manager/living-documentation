<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Test\Formatter;

use PhpProject\LivingDocumentation\Test\Document\DocumentedTestCase;

final class SimpleTestCaseFormatter implements TestCaseFormatter
{
    // The rule to match text between '', "" and ``
    private const IN_QUOTES_RULE = '(("([^"]|\\\\")*")|(\'([^\']|\\\\\')*\')|(`([^`]|\\\\`)*`))';

    /**
     * @var array<string, string>
     */
    private const EMOJI_REGEX = [
        'regex_alphanumeric' => '[\x{1F100}-\x{1F1FF}]',
        'regex_symbols'      => '[\x{1F300}-\x{1F5FF}]',
        'regex_emoticons'    => '[\x{1F600}-\x{1F64F}]',
        'regex_transport'    => '[\x{1F680}-\x{1F6FF}]',
        'regex_supplemental' => '[\x{1F900}-\x{1F9FF}]',
        'regex_new'          => '[\x{1FA00}-\x{1FAFF}]',
        'regex_misc'         => '[\x{2600}-\x{26FF}]',
        'regex_dingbats'     => '[\x{2700}-\x{27BF}]',
    ];

    public function format(string $name, string $content, bool $removeEmojis = false): DocumentedTestCase
    {
        $start           = (int) strpos($content, '{') + 1;
        $end             = (int) strrpos($content, '}');
        $strippedContent = substr($content, $start, $end - $start);
        $cleanedContent  = trim($this->replaceSymbols($strippedContent, $removeEmojis));

        return new DocumentedTestCase(
            ucfirst($this->replaceSymbols($name, $removeEmojis)),
            $this->cleanLines($cleanedContent)
        );
    }

    private function cleanLines(string $content): string
    {
        $contentLines = explode(\PHP_EOL, $content);
        $cleanedLines = array_map(static fn (string $line): string => ucfirst(trim($line)), $contentLines);

        return implode(\PHP_EOL, $cleanedLines);
    }

    private function replaceSymbols(string $text, bool $removeEmojis): string
    {
        $regexes = [
            // Removes lines with #ignore
            '/^(.*#ignoreLine.*)$/m',
            // Array arrows
            '/(=>)/m',
            // Removes PHP vars and methods outside of quotes beginning by _
            '/(?:'.self::IN_QUOTES_RULE.')\K|((\$|>|\s|=)_([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*))/m',
            // Replaces symbols outside quotes : $.,{}[]()|&*+=:-<>\/;_%! by a single space
            '/(?:'.self::IN_QUOTES_RULE.')\K|([\$.,{}[\]()|&*+=:\-<>\/;_%!^~?@#])/m',
            // Replace "cheating" chars by legit ones (space, dash, dot)
            "/(\u{a0})/m", '/(‑)/m', '/(․)/m',
            // Replace multiple spaces not in quotes
            '/(?:'.self::IN_QUOTES_RULE.')\K| \K( +)/m',
            // Replace space before "cheating" apostrophe when not in quotes, then replace cheating apostrophe everywhere
            '/(?:'.self::IN_QUOTES_RULE.')\K|( *)(?=’)/m', '/(’)/m',
            $removeEmojis ? $this->emojiRegex() : '/(.*)/m',
        ];

        $replacements = [
            '',
            '⮕',
            '',
            ' ',
            ' ', '-', '.',
            '',
            '', '\'',
            $removeEmojis ? '' : '$1',
        ];

        return (string) preg_replace($regexes, $replacements, $text);
    }

    private function emojiRegex(): string
    {
        return '/'.implode('|', self::EMOJI_REGEX).'/um';
    }
}
