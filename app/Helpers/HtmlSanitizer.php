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

        // Remove <script> and <style> tags with content
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);

        // Remove dangerous tags
        $dangerous = ['iframe', 'object', 'embed', 'form', 'input', 'textarea', 'select', 'button', 'link', 'meta', 'base', 'applet', 'svg', 'math'];
        foreach ($dangerous as $tag) {
            $html = preg_replace('/<' . $tag . '\b[^>]*>.*?<\/' . $tag . '>/is', '', $html);
            $html = preg_replace('/<' . $tag . '\b[^>]*\/?>/is', '', $html);
        }

        // Remove all on* event handlers
        $html = preg_replace('/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $html);

        // Remove javascript:/vbscript:/data: in href/src
        $html = preg_replace('/\b(href|src|action|formaction)\s*=\s*["\']?\s*(?:javascript|vbscript|data)\s*:[^"\'>\s]*/i', '', $html);

        // Sanitize style attributes - remove expression/url
        $html = preg_replace_callback('/style\s*=\s*"([^"]*)"/i', function ($m) {
            $s = preg_replace('/expression\s*\(/i', '', $m[1]);
            $s = preg_replace('/url\s*\(/i', '', $s);
            $s = preg_replace('/javascript\s*:/i', '', $s);
            return 'style="' . $s . '"';
        }, $html);

        // Strip to allowed tags only
        $allowed = implode('', array_map(fn($t) => "<{$t}>", self::ALLOWED_TAGS));
        $html = strip_tags($html, $allowed);

        // Final pass: remove any remaining on* handlers
        $html = preg_replace('/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $html);

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
