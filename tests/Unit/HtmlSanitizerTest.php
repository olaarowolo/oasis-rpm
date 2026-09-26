<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    private HtmlSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new HtmlSanitizer();
    }

    public function test_strips_script_tags_and_event_handlers(): void
    {
        $dirty = '<p onclick="stealCookies()">Hello</p><script>alert(1)</script>';
        $clean = $this->sanitizer->clean($dirty);

        $this->assertStringContainsString('Hello', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('<p ', $clean);
    }

    public function test_allows_safe_inline_styles(): void
    {
        $dirty = '<p style="color: red; background: blue;">Text</p>';
        $clean = $this->sanitizer->clean($dirty);

        $this->assertStringContainsString('Text', $clean);
        $this->assertStringNotContainsString('style=', $clean);
    }

    public function test_strips_iframe_tags_but_preserves_inner_text(): void
    {
        $dirty = '<p>Before</p><iframe src="https://evil.com"></iframe><p>After</p>';
        $clean = $this->sanitizer->clean($dirty);

        $this->assertStringContainsString('Before', $clean);
        $this->assertStringContainsString('After', $clean);
        $this->assertStringNotContainsString('<iframe', $clean);
    }

    public function test_preserves_headings_and_lists(): void
    {
        $html = '<h2>Heading</h2><ul><li>Item 1</li><li>Item 2</li></ul>';
        $clean = $this->sanitizer->clean($html);

        $this->assertStringContainsString('<h2>Heading</h2>', $clean);
        $this->assertStringContainsString('<ul>', $clean);
        $this->assertStringContainsString('<li>Item 1</li>', $clean);
        $this->assertStringContainsString('<li>Item 2</li>', $clean);
    }

    public function test_unwraps_table_elements_but_preserves_text(): void
    {
        $html = '<table><thead><tr><th>Col</th></tr></thead><tbody><tr><td>Data</td></tr></tbody></table>';
        $clean = $this->sanitizer->clean($html);

        $this->assertStringContainsString('Col', $clean);
        $this->assertStringContainsString('Data', $clean);
        $this->assertStringNotContainsString('<table>', $clean);
        $this->assertStringNotContainsString('<th>', $clean);
    }

    public function test_removes_data_attributes(): void
    {
        $dirty = '<p data-bind="evil()">Safe text</p>';
        $clean = $this->sanitizer->clean($dirty);

        $this->assertStringContainsString('Safe text', $clean);
        $this->assertStringNotContainsString('data-bind', $clean);
    }

    public function test_returns_empty_string_for_empty_input(): void
    {
        $this->assertSame('', $this->sanitizer->clean(''));
    }

    public function test_neutralizes_javascript_protocol_in_href(): void
    {
        $dirty = '<a href="javascript:alert(1)">Click</a>';
        $clean = $this->sanitizer->clean($dirty);

        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringContainsString('Click', $clean);
    }

    public function test_allows_https_links(): void
    {
        $html = '<a href="https://example.com">Link</a>';
        $clean = $this->sanitizer->clean($html);

        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringContainsString('Link', $clean);
    }

    public function test_strips_onload_event_handlers(): void
    {
        $dirty = '<p onload="evil()">Text</p>';
        $clean = $this->sanitizer->clean($dirty);

        $this->assertStringContainsString('Text', $clean);
        $this->assertStringNotContainsString('onload', $clean);
    }
}
