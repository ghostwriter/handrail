<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Modifier;

use Ghostwriter\Handrail\File\IncludedFileInterface;
use Ghostwriter\Handrail\File\ModifiedFile;
use Ghostwriter\Handrail\File\ModifiedFileInterface;
use PhpToken;

use PHPUnit\Framework\Assert;

use const T_FUNCTION;
use const T_IF;
use const T_STRING;

final class FileModifier implements ModifierInterface
{
    public function modify(IncludedFileInterface $file): ModifiedFileInterface
    {
        $code = $file->code();

        $modifiedCode = $this->wrapFunctionsWithExistsCheck($code);
        //        if ($code !== $modifiedContent) {
        //            $this->io->write('<info>Modifying file: ' . $filePath . '</info>');
        //
        //            \file_put_contents($filePath, $modifiedContent);
        //        } else {
        //            $this->io->write('<info>No modifications needed for file: ' . $filePath . '</info>');
        //        }

        //        $modifiedCode = $code;

        return ModifiedFile::new($file->path(), $modifiedCode);
    }

    private function isFunctionExistsCheck(array $tokens, int $index): bool
    {
        // Look ahead to see if we have a function_exists check
        for ($i = $index; $i < \count($tokens); ++$i) {

            /** @var array{int,string}|string $token */
            $token = $tokens[$i];

            if ($token === '{') {
                // If we reach an opening brace before finding function_exists, it's not a check
                break;
            }

            if (! \is_array($token)) {
                continue;
            }

            [$id, $text] = $token;

            if ($id !== T_STRING) {
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
        $output = '';
        $functionName = '';
        $insideFunctionCheck = false;
        $indent = '    ';
        $previousLine = $line = 1;
        $openIfBlock = false;

        for ($i = 0, $count = \count($tokens); $i < $count; $i++) {
            /** @var PhpToken $token */
            $token = $tokens[$i];

            $id = $token->id;
            $text = $token->text;
            $line = $token->line;

            if ($openIfBlock && $line > $previousLine) {
                $output .= $indent;
                $previousLine = $line;
            }

            if ($id >= 255) {

                // Detect the start of an if (!function_exists()) block
                if ($id === T_IF && $this->isFunctionExistsCheck($tokens, $i)) {
                    $insideFunctionCheck = true;
                }

                // Detect the start of a function declaration
                if ($id === T_FUNCTION) {
                    // If we are inside an if (!function_exists()) block, skip this function declaration
                    if ($insideFunctionCheck) {
                        $insideFunctionCheck = false; // Reset for the next function
                        $output .= $text;
                        continue;
                    }

                    // Get the function name
                    $functionNameToken = $tokens[$i + 2];
                    if ($functionNameToken->id !== T_STRING) {
                        continue;
                    }

                    $functionName = $functionNameToken->text;
                    // Wrap the function declaration with if (!function_exists())
                    $output .= "if (!function_exists('{$functionName}')) {\n    ";
                    $output .= $text;

                    $openIfBlock = true;
                    $previousLine = $line;
                    continue;
                }

                // Regular token
                $output .= $text;
            } else {
                // Handle single-character tokens
                $output .= $token;

                // Close the if block after the function body ends
                if ($text === '}') {
                    if ($insideFunctionCheck) {
                        $insideFunctionCheck = false;
                    } else {
                        $output .= "\n}";
                        $openIfBlock = false;
                    }
                }
            }
        }

        Assert::assertSame($output, $content);

        dd([$output, $content]);

        /**
         * TODO: Remove this assertion ^ and return $output
         *
         * The assertion is here to debug the output via PHPUnit's Diff output.
         *
         * We can remove this assertion and return $output once we are confident
         *
         * - [x] wrap function declarations with `if (!function_exists())`
         * - [] skip function declarations already wrapped with `if (!function_exists())`
         * - [] handle nested `if (!function_exists())` blocks
         */

        return $output;
    }
}
