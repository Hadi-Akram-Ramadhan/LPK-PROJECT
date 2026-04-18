<?php

namespace App\Helpers;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'b', 'i', 'u', 'strong', 'em', 'br', 'p', 'span',
        'ul', 'ol', 'li', 'sub', 'sup', 'hr',
    ];

    /**
     * Sanitize HTML - allow only safe formatting tags.
     * Strips scripts, event handlers, dangerous attributes.
     */
    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Remove <script> and <style> tags with content
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);

        // 2. Remove dangerous tags
        $dangerous = ['iframe', 'object', 'embed', 'form', 'input', 'textarea', 'select', 'button', 'link', 'meta', 'base', 'applet', 'svg', 'math'];
        foreach ($dangerous as $tag) {
            $html = preg_replace('/<' . $tag . '\b[^>]*>.*?<\/' . $tag . '>/is', '', $html);
            $html = preg_replace('/<' . $tag . '\b[^>]*\/?>/is', '', $html);
        }

        // 3. Remove all on* event handlers and javascript: protocols
        $html = preg_replace('/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $html);
        $html = preg_replace('/\b(href|src|action|formaction)\s*=\s*["\']?\s*(?:javascript|vbscript|data)\s*:[^"\'>\s]*/i', '', $html);

        // 4. Strip to allowed tags only
        $allowed = implode('', array_map(fn($t) => "<{$t}>", self::ALLOWED_TAGS));
        $html = strip_tags($html, $allowed);

        // 5. CRITICAL: Remove all attributes from the remaining tags to prevent attribute breakout/XSS
        // Since we only allow formatting tags like b, i, p, etc., they don't NEED attributes.
        // Regex looks for any attribute in a tag like <p class="foo"> and converts it to <p>
        $html = preg_replace_callback('/<([a-z1-6]+)\s+[^>]*>/i', function($matches) {
            $tag = strtolower($matches[1]);
            if (in_array($tag, self::ALLOWED_TAGS)) {
                return "<{$tag}>";
            }
            return $matches[0];
        }, $html);

        return trim($html);
    }

    /**
     * Strip ALL HTML and encode entities. For plain-text contexts.
     */
    public static function stripAll(?string $html): string
    {
        if (empty($html)) {
            return '';
        }
        return htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8');
    }
}
