<?php

namespace Tests\Unit;

use App\Support\WordCounter;
use PHPUnit\Framework\TestCase;

class WordCounterTest extends TestCase
{
    private WordCounter $counter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->counter = new WordCounter();
    }

    public function test_counts_simple_words(): void
    {
        $this->assertSame(5, $this->counter->count('one two three four five'));
    }

    public function test_strips_html_before_counting(): void
    {
        $html = '<p>Hello <strong>world</strong> this is text.</p>';
        $count = $this->counter->count($html);

        $this->assertSame(5, $count);
    }

    public function test_handles_multiple_spaces_and_newlines(): void
    {
        $text = "word1\n\n\nword2\tword3";
        $this->assertSame(3, $this->counter->count($text));
    }

    public function test_strips_empty_tags_and_extra_markup(): void
    {
        $html = '<div><span></span><p>Hello    world</p></div>';
        $count = $this->counter->count($html);

        $this->assertSame(2, $count);
    }

    public function test_returns_zero_for_empty_input(): void
    {
        $this->assertSame(0, $this->counter->count(''));
        $this->assertSame(0, $this->counter->count('   '));
    }

    public function test_ignores_punctuation_boundaries(): void
    {
        $this->assertSame(3, $this->counter->count('word1, word2; word3.'));
    }

    public function test_counts_words_in_lists(): void
    {
        $html = '<p>First item here</p> <p>Second item now</p>';
        $this->assertSame(6, $this->counter->count($html));
    }
}
