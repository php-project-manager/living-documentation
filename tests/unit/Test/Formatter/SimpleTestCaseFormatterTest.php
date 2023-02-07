<?php

declare(strict_types=1);

namespace PhpProject\LivingDocumentation\Tests\Test\Formatter;

use PhpProject\Fluent\LinkWordsFluentTrait;
use PhpProject\LivingDocumentation\Test\Document\DocumentedTestCase;
use PhpProject\LivingDocumentation\Test\Formatter\SimpleTestCaseFormatter;
use PhpProject\SourceCode\Classes\Reflection\Standard\StandardReflectionClass;
use PhpProject\SourceCode\Classes\SourceMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('string')]
#[Group('livingDocumentation')]
#[Group('tests')]
final class SimpleTestCaseFormatterTest extends TestCase
{
    private ?string $title;
    private ?string $content;
    private SimpleTestCaseFormatter $formatter;

    protected function setUp(): void
    {
        $this->title     = null;
        $this->content   = null;
        $this->formatter = new SimpleTestCaseFormatter();
    }

    #[DataProvider('withEmojis')]
    #[Test]
    public function it_should_make_the_unquoted_parts_of_the_test_method_human_readable(SourceMethod $_method, string $_expectedName, string $_expectedContent): void
    {
        $_this = $this; // #ignoreLine

        $_this->given_a_test_method($_method->name(), and: $_method->body);
        $_documentedTest = $_this->when_I_format_it($_this->keeping_emojis());
        $_this->the_content_must_be_readable_by_a_human($_documentedTest, $_expectedName, $_expectedContent);
    }

    #[DataProvider('withoutEmojis')]
    #[Test]
    public function it_should_make_the_unquoted_parts_of_the_test_method_human_readable_without_emojis(SourceMethod $_method, string $_expectedName, string $_expectedContent): void
    {
        $_this = $this; // #ignoreLine

        $_this->given_a_test_method($_method->name(), and: $_method->body);
        $_documentedTest = $_this->when_I_format_it($_this->deleting_emojis());
        $_this->the_content_must_be_readable_by_a_human($_documentedTest, $_expectedName, $_expectedContent);
    }

    // 1. Arrange

    private function given_a_test_method(string $title, string $and): void
    {
        $this->title   = $title;
        $this->content = $and;
    }

    // 2. Act

    private function when_I_format_it(bool $emojis = true): DocumentedTestCase
    {
        self::assertNotNull($this->title);
        self::assertNotNull($this->content);

        return $this->formatter->format($this->title, $this->content, $emojis);
    }

    private function keeping_emojis(): bool
    {
        return false;
    }

    private function deleting_emojis(): bool
    {
        return true;
    }

    // 3. Assert

    private function the_content_must_be_readable_by_a_human(DocumentedTestCase $documentedTest, string $expectedName, string $expectedContent): void
    {
        self::assertEquals($expectedName, $documentedTest->name);
        self::assertEquals($expectedContent, $documentedTest->content);
    }

    /**
     * @return array<string, array<SourceMethod|string|bool>>
     */
    public static function withEmojis(): array
    {
        $classToDocument = StandardReflectionClass::createFromName(TestsToDocument::class);

        $simpleMethod    = $classToDocument->getMethod('simple_test');

        return [
            'simple_test' => [
                SourceMethod::fromReflectionMethod($simpleMethod),
                'Simple test',
                'My tailor is a little \'rich\''.\PHP_EOL.
                'Given 4 6 7 3 random int 2 9'.\PHP_EOL.
                'And \'a\' little \'bunny\''.\PHP_EOL.
                'Makes my tailor is 1 4 5'.\PHP_EOL.
                'My static function makes \'no sense\''.\PHP_EOL.
                'But \'this\' is \'fine\' ⮕ \'🫠\''.\PHP_EOL.
                'If given 4 2 little \'carrots\''.\PHP_EOL.
                '\'$.,{}[]()|&*+=:-<>\/;_%!    is untouched because it is between quotes\''.\PHP_EOL.
                'But \'-.\' are replaced regardless\''.\PHP_EOL.
                'And \' \' the space before \' is kept here because it is between quotes\''.\PHP_EOL.
                'Comments might be a mess though'.\PHP_EOL. // we shouldn't touch them
                'Yeah this is a\'s word \'Yeah!\' Note that there will be no space between "a" and "\'s"'.\PHP_EOL.
                'Cheating-is.fun don\'t \'you think?\'',
            ],
        ];
    }

    /**
     * @return array<string, array<SourceMethod|string|bool>>
     */
    public static function withoutEmojis(): array
    {
        $classToDocument = StandardReflectionClass::createFromName(TestsToDocument::class);

        $simpleMethod    = $classToDocument->getMethod('simple_test');

        return [
            'simple_test' => [
                SourceMethod::fromReflectionMethod($simpleMethod),
                'Simple test',
                'My tailor is a little \'rich\''.\PHP_EOL.
                'Given 4 6 7 3 random int 2 9'.\PHP_EOL.
                'And \'a\' little \'bunny\''.\PHP_EOL.
                'Makes my tailor is 1 4 5'.\PHP_EOL.
                'My static function makes \'no sense\''.\PHP_EOL.
                'But \'this\' is \'fine\' ⮕ \'\''.\PHP_EOL.
                'If given 4 2 little \'carrots\''.\PHP_EOL.
                '\'$.,{}[]()|&*+=:-<>\/;_%!    is untouched because it is between quotes\''.\PHP_EOL.
                'But \'-.\' are replaced regardless\''.\PHP_EOL.
                'And \' \' the space before \' is kept here because it is between quotes\''.\PHP_EOL.
                'Comments might be a mess though'.\PHP_EOL. // we shouldn't touch them
                'Yeah this is a\'s word \'Yeah!\' Note that there will be no space between "a" and "\'s"'.\PHP_EOL.
                'Cheating-is.fun don\'t \'you think?\'',
            ],
        ];
    }
}

final class TestsToDocument
{
    use LinkWordsFluentTrait;

    /**
     * @throws \Exception
     */
    public function simple_test(): void
    {
        $_this = $this; // #ignoreLine

        $my      = $tailor = $is = $a = $little = 'rich';
        $given   = $_ = $_some = 4 + 6 / 7 * 3 % random_int(2, 9);
        $and     = 'a'.$little.'bunny';
        $_hidden = $_this->makes($my, $tailor)->_($is, 1 & 4 | 5);
        $my      = static function () { $makes = 'no sense'; };
        $but     = ['this', $is, 'fine' => '🫠'];
        if ($given > 4 || ($_ < 2 && $_some.$little !== 'carrots')) {
            $_   = '$.,{}[]()|&*+=:-<>\/;_%!    is untouched because it is between quotes';
            $but = '‑․’ are replaced regardless';
            $and = ' ’ the space before ’ is kept here because it is between quotes';
        } // Comments might be a mess though...
        $yeah                  = $this->is()->a()->’s()->_hidden(word: 'Yeah!'); // Note that there will be no space between "a" and "'s"
        $cheating‑is․fun don’t = 'you think?';
    }

    private function _hidden(string $word): string
    {
        return $word;
    }
}
