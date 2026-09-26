<?php

namespace App\Support;

class WordCounter
{
    /**
     * Count words in HTML content (strips tags first).
     * Hyphenated words are counted as one word.
     * Accented characters are treated as letters.
     */
    public function count(string $html): int
    {
        $text = strip_tags($html);
        $text = $this->normaliseWhitespace($text);

        if (trim($text) === '') {
            return 0;
        }

        $matches = [];
        preg_match_all(
            '/[\p{L}\p{N}]+(?:[\'\x{2019}-][\p{L}\p{N}]+)*/u',
            $text,
            $matches
        );

        return count($matches[0]);
    }

    protected function normaliseWhitespace(string $text): string
    {
        $text = str_replace(["\t", "\r\n", "\r", "\n"], ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}
