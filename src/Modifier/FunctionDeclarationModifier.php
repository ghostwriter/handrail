<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Modifier;

use Ghostwriter\Handrail\Exception\ShouldNotHappenException;
use Ghostwriter\Handrail\Value\File\ModifiedFile;
use Ghostwriter\Handrail\Value\File\ModifiedFileInterface;
use Ghostwriter\Handrail\Value\File\OriginalFileInterface;
use Override;
use PhpToken;

use const T_CLASS;
use const T_FUNCTION;
use const T_IF;
use const T_STRING;

final readonly class FunctionDeclarationModifier implements ModifierInterface
{
    #[Override]
    public function modify(OriginalFileInterface $originalFile): ModifiedFileInterface
    {
        return ModifiedFile::new(
            $originalFile->path(),
            $this->wrapFunctionsWithExistsCheck($originalFile->code()),
        );
    }

    private function isFunctionExistsCheck(array $tokens, int $index): bool
    {
        $counter = \count($tokens);
        for ($i = $index; $i < $counter; ++$i) {
            $token = $tokens[$i];

            if (! $token instanceof PhpToken) {
                throw new ShouldNotHappenException('Invalid token');
            }

            $text = $token->text;

            if ($text === '{') {
                // If we reach an opening brace before finding function_exists, it's not a check
                break;
            }

            if ($token->id !== T_STRING) {
                continue;
            }

            if (\mb_strtolower($text) === 'function_exists') {
                return true;
            }
        }

        return false;
    }

    private function wrapFunctionsWithExistsCheck(string $content): string
    {
        $tokens = PhpToken::tokenize($content);
        $indent = \str_repeat(' ', 4);
        $openIfBlock = false;
        $insideFunctionCheck = false;
        $insideClass = false;
        $output = '';
        $previousLine = 0;
        $functionLevel = 0;
        $classLevel = 0;

        $previousToken = null;
        for ($i = 0, $count = \count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];

            if (! $token instanceof PhpToken) {
                throw new ShouldNotHappenException('Invalid token');
            }

            $line = $token->line;
            if ($openIfBlock && $line > $previousLine) {
                $output .= $indent;
                $previousLine = $line;
            }

            $id = $token->id;

            // Detect the start of an if (!function_exists()) block
            if ($id === T_IF && $this->isFunctionExistsCheck($tokens, $i)) {
                $insideFunctionCheck = true;
            }

            if ($id === T_CLASS) {
                $insideClass = true;
            }

            $text = $token->text;

            // Detect the start of a function declaration
            if ($id === T_FUNCTION) {
                if ($insideClass) {
                    $output .= $text;

                    continue;
                }

                // If we are inside an if (!function_exists()) block, skip this function declaration
                if ($insideFunctionCheck) {
                    $insideFunctionCheck = false; // Reset for the next function
                    $output .= $text;

                    continue;
                }

                $functionNameToken = $tokens[$i + 2]
                    ?? throw new ShouldNotHappenException('Invalid token: missing function name');

                if ($functionNameToken->id !== T_STRING) {
                    $output .= $text;

                    continue;
                }

                $output .= \sprintf("if (!function_exists('%s')) {\n%s%s", $functionNameToken->text, $indent, $text);

                $openIfBlock = true;
                $previousLine = $line;

                continue;
            }

            // Handle single-character tokens
            $output .= $text;

            $isOpeningBrace = $text === '{';
            if ($isOpeningBrace) {
                ++$functionLevel;

                if ($insideClass) {
                    ++$classLevel;
                }
            }

            $isClosingBrace = $text === '}';
            if (! $isClosingBrace) {
                continue;
            }

            --$functionLevel;

            if ($insideClass) {
                --$classLevel;

                if ($classLevel === 0) {
                    $insideClass = false;
                }
            }

            if (! $openIfBlock) {
                continue;
            }

            if ($functionLevel !== 0) {
                continue;
            }

            $output .= "\n}";

            $openIfBlock = false;
        }

        return $output;
    }
}
