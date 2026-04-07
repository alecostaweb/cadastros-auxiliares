<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class MensagemHtmlSanitizer
{
    /**
     * @var array<string, bool>
     */
    private const ALLOWED_TAGS = [
        'a' => true,
        'strong' => true,
        'br' => true,
        'p' => true,
        'em' => true,
    ];

    /**
     * @var array<string, bool>
     */
    private const ALLOWED_A_PROTOCOLS = [
        'http' => true,
        'https' => true,
        'mailto' => true,
    ];

    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $fragment = '<div>' . $html . '</div>';

        $previousUseInternalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $fragment,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousUseInternalErrors);

        /** @var DOMElement|null $wrapper */
        $wrapper = $dom->getElementsByTagName('div')->item(0);
        if (!$wrapper instanceof DOMElement) {
            return '';
        }

        self::sanitizeNode($wrapper);

        $sanitized = self::innerHtml($wrapper);

        return trim($sanitized);
    }

    private static function sanitizeNode(DOMNode $parent): void
    {
        // Snapshot because we may replace/remove nodes while iterating.
        $children = [];
        foreach ($parent->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if (!$child instanceof DOMElement) {
                continue;
            }

            $tag = mb_strtolower($child->tagName);
            if (!isset(self::ALLOWED_TAGS[$tag])) {
                if (in_array($tag, ['script', 'style'], true)) {
                    $parent->removeChild($child);
                    continue;
                }

                self::unwrapNode($child);
                continue;
            }

            if ($tag === 'a') {
                self::sanitizeAnchor($child);
            } else {
                self::removeAllAttributes($child);
            }

            self::sanitizeNode($child);
        }
    }

    private static function sanitizeAnchor(DOMElement $anchor): void
    {
        $href = trim($anchor->getAttribute('href'));

        self::removeAllAttributes($anchor);

        if (self::isSafeHref($href)) {
            $anchor->setAttribute('href', $href);
            $anchor->setAttribute('target', '_blank');
            $anchor->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private static function isSafeHref(string $href): bool
    {
        if ($href === '') {
            return false;
        }

        if (str_starts_with($href, '#') || str_starts_with($href, '/')) {
            return true;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);
        if ($scheme === null || $scheme === false) {
            return true;
        }

        $scheme = mb_strtolower($scheme);

        return isset(self::ALLOWED_A_PROTOCOLS[$scheme]);
    }

    private static function removeAllAttributes(DOMElement $element): void
    {
        while ($element->attributes->length > 0) {
            /** @var DOMNode $attr */
            $attr = $element->attributes->item(0);
            $element->removeAttributeNode($attr);
        }
    }

    private static function unwrapNode(DOMElement $node): void
    {
        $parent = $node->parentNode;
        if ($parent === null) {
            return;
        }

        while ($node->firstChild !== null) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }

    private static function innerHtml(DOMElement $element): string
    {
        $html = '';
        foreach ($element->childNodes as $child) {
            $html .= $element->ownerDocument->saveHTML($child);
        }

        return $html;
    }
}
