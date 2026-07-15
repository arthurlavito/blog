<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Mews\Purifier\Facades\Purifier;

class ContentSanitizer
{
    public function sanitize(string $html): string
    {
        $clean = Purifier::clean($html, 'anim24_post');

        return $this->addNoopenerToExternalLinks($clean);
    }

    /**
     * Use DOMDocument (not regex) to add rel="noopener" to every <a> that
     * points outside our own domain. Also sets target="_blank" on those links
     * so readers don't lose their place. Internal anchor links (#section) are
     * left untouched.
     */
    private function addNoopenerToExternalLinks(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        // loadHTML always adds <html><head><body> wrappers — that's intentional here.
        // We strip the wrapper back out by iterating body->childNodes below.
        // LIBXML_HTML_NODEFDTD suppresses the DOCTYPE declaration in output.
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? '';
        $xpath   = new DOMXPath($dom);

        foreach ($xpath->query('//a[@href]') as $anchor) {
            $href = $anchor->getAttribute('href');
            $host = parse_url($href, PHP_URL_HOST) ?? '';

            $isExternal = $host !== '' && $host !== $appHost;
            if (! $isExternal) {
                continue;
            }

            $rels = array_filter(explode(' ', $anchor->getAttribute('rel')));
            if (! in_array('noopener', $rels, true)) {
                $rels[] = 'noopener';
            }
            $anchor->setAttribute('rel', implode(' ', $rels));

            if (! $anchor->hasAttribute('target')) {
                $anchor->setAttribute('target', '_blank');
            }
        }

        $body   = $dom->getElementsByTagName('body')->item(0);
        $result = '';
        if ($body) {
            foreach ($body->childNodes as $node) {
                $result .= $dom->saveHTML($node);
            }
        }

        return $result !== '' ? $result : $html;
    }
}
