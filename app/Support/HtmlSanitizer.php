<?php

namespace App\Support;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Throwable;

class HtmlSanitizer
{
    public const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
        'h2', 'h3', 'h4',
        'ul', 'ol', 'li',
        'blockquote', 'code', 'pre', 'a', 'span',
    ];

    public const ALLOWED_ATTRIBUTES = [
        'a' => ['href'],
        'span' => ['class'],
    ];

    public const ALLOWED_URL_SCHEMES = ['http', 'https', 'mailto'];

    public const ALLOWED_SPAN_CLASSES = [
        'editor-highlight',
        'editor-underline',
    ];

    public function clean(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        try {
            return $this->cleanWithDom($html);
        } catch (Throwable) {
            return $this->cleanFallback($html);
        }
    }

    protected function cleanWithDom(string $html): string
    {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);

        // Wrap in a minimal HTML document so libxml parses fragments correctly.
        $dom->loadHTML(
            '<?xml encoding="UTF-8"><div id="sanitizer-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $xpath = new DOMXPath($dom);
        $root = $xpath->query('//*[@id="sanitizer-root"]')->item(0) ?? $dom->documentElement;

        $this->walk($dom, $root);

        // Strip wrapper, return cleaned inner HTML.
        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return $result;
    }

    protected function walk(DOMDocument $dom, DOMNode $node): void
    {
        $remove = [];
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                continue;
            }

            if ($child->nodeType !== XML_ELEMENT_NODE) {
                $remove[] = $child;
                continue;
            }

            $tag = strtolower($child->nodeName);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                // Replace with text content + children walk (unwrap).
                $fragment = $dom->createDocumentFragment();
                foreach ($child->childNodes as $grandchild) {
                    $fragment->appendChild($grandchild->cloneNode(true, true));
                }
                $node->replaceChild($fragment, $child);
                continue;
            }

            // Recursively clean children first.
            $this->walk($dom, $child);

            // Validate attributes.
            if ($child->hasAttributes()) {
                $attrs = [];
                foreach ($child->attributes as $attr) {
                    $attrs[] = $attr;
                }
                foreach ($attrs as $attr) {
                    $attrName = strtolower($attr->nodeName);
                    $allowedForTag = self::ALLOWED_ATTRIBUTES[$tag] ?? [];

                    if (! in_array($attrName, $allowedForTag, true)) {
                        $child->removeAttribute($attrName);
                    } elseif ($attrName === 'href') {
                        if (! $this->isValidUrl($attr->nodeValue)) {
                            $child->removeAttribute($attrName);
                        }
                    } elseif ($attrName === 'class') {
                        $classes = explode(' ', $attr->nodeValue);
                        $safe = array_intersect($classes, self::ALLOWED_SPAN_CLASSES);
                        if (empty($safe)) {
                            $child->removeAttribute($attrName);
                        } else {
                            $child->setAttribute('class', implode(' ', $safe));
                        }
                    }
                }
            }
        }
    }

    protected function isValidUrl(string $url): bool
    {
        $parsed = parse_url($url);

        if ($parsed === false || ! isset($parsed['scheme'])) {
            // Relative URLs without scheme are allowed (anchors, same-path links).
            return true;
        }

        return in_array(strtolower($parsed['scheme']), self::ALLOWED_URL_SCHEMES, true);
    }

    protected function cleanFallback(string $html): string
    {
        $allowed = implode(',', self::ALLOWED_TAGS);
        $stripped = strip_tags($html, '<' . $allowed . '>');

        // Remove all attributes via regex: match any tag followed by attributes
        // that are not href on <a> or class on <span>.
        $stripped = preg_replace_callback(
            '/<(a|span)\b([^>]*)>/i',
            function ($m) {
                $tag = strtolower($m[1]);
                $attrs = $m[2];

                if ($tag === 'a') {
                    preg_match('/\shref\s*=\s*"([^"]*)"/i', $attrs, $href);
                    preg_match("/\shref\s*=\s*'([^']*)'/i", $attrs, $href2);
                    $href = $href[1] ?? ($href2[1] ?? null);

                    if ($href !== null && $this->isValidUrl($href)) {
                        return '<a href="' . htmlspecialchars($href, ENT_QUOTES) . '">';
                    }
                    return '<a>';
                }

                // span — keep only class from the allowed set
                preg_match('/\sclass\s*=\s*"([^"]*)"/i', $attrs, $class);
                $classes = $class[1] ?? '';
                $safe = array_intersect(explode(' ', $classes), self::ALLOWED_SPAN_CLASSES);

                if (empty($safe)) {
                    return '<span>';
                }
                return '<span class="' . implode(' ', $safe) . '">';
            },
            $stripped
        );

        // Remove any leftover on* attributes and style attributes from all tags.
        $stripped = preg_replace('/\s*on\w+\s*=\s*"[^"]*"/i', '', $stripped);
        $stripped = preg_replace("/\s*on\w+\s*=\s*'[^']*'/i", '', $stripped);
        $stripped = preg_replace('/\s*style\s*=\s*"[^"]*"/i', '', $stripped);
        $stripped = preg_replace("/\s*style\s*=\s*'[^']*'/i", '', $stripped);
        $stripped = preg_replace('/\s*data-\w+\s*=\s*"[^"]*"/i', '', $stripped);
        $stripped = preg_replace("/\s*data-\w+\s*=\s*'[^']*'/i", '', $stripped);

        return $stripped;
    }
}
