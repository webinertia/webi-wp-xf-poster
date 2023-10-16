<?php

declare(strict_types=1);

namespace WebiXfBridge;

use Genert\BBCode\BBCode;
use WebiXfBridge\Settings;

use function array_merge;
use function array_unique;
use function get_the_post_thumbnail;
use function get_option;
use function get_permalink;
use function parse_url;
use function preg_replace;
use function preg_replace_callback;
use function explode;
use function trim;
use function rawurldecode;
use function rawurlencode;
use function strpos;
use function substr;
use function strlen;
use function strip_tags;

final class Formatter
{
    private bool $useFeatured = false;
    private ?string $featured;
    private string $scripturl;
    private ?string $formatted;
    private ?string $imageHeightOption = null;
    private ?string $imageWidthOption = null;
    public static $targetTags = ['a', 'strong', 's', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'img'];

    public function __invoke(string $content, array|string $targetTags = null): string
    {
        return $this->format($targetTags, $content);
    }

    public function format($post, string $content, $isExcerpt = false, array|string $targetTags = []): string
    {
        $bbCode            = new BBCode();
        $this->useFeatured = (bool) get_option(Settings::useFeaturedImageSetting->value);
        $this->featured    = get_the_post_thumbnail($post, 'large'); // may need to refactor this
        $this->scripturl   = get_option('siteurl');
        // add a check for $_POST values
        // stopped here
        $storedTags = get_option(Settings::targetTagsSetting->value);
        if ($storedTags) {
            $storedTags = explode(',', $storedTags);
        } else {
            $storedTags = static::$targetTags;
        }
        if ($targetTags !== null && is_string($targetTags)) {
            $targetTags = explode(',', $targetTags);
        }
        $targetTags = array_unique(array_merge($storedTags, $targetTags));
        if (! $isExcerpt) {
            $this->formatted = preg_replace('/(\s+){2,}/', "<br>", $content);
            $this->formatted = $bbCode->convertFromHtml(strip_tags($this->formatted, $targetTags));
            $this->formatted = $this->parseImageTags($this->formatted);
        } else {
            if ($this->useFeatured) {
                $img = $this->parseImagetags(get_the_post_thumbnail($post->ID, 'large'));
            } else {
                $img = $this->parseImageTags($post->post_content, true);
            }

            $this->formatted = $content . ' ... ' . $this->parseLinkTags('<a href="'.get_permalink($post).'">Read Full Article</a>') . $img;
        }

        return $this->formatted;
    }

    // credit SMF for main function, with custom changes
    private function parseImageTags(string $text, $returnTagOnly = false)
    {
        while (preg_match('~<img\s+([^<>]*)/*>~i', $text, $matches) === 1)
        {
            // Find the position of the image.
            $start_pos = strpos($text, $matches[0]);
            if ($start_pos === false)
                break;
            $end_pos = $start_pos + strlen($matches[0]);

            $params = '';
            $src = '';
            $attrs = $this->fetchTagAttributes($matches[1]);

            // if its not in the markup, but we have values then push them here
            if (! in_array($attrs, ['width', 'height'])) {
                $attrs['width']  = $this->imageWidthOption;
                $attrs['height'] = $this->imageHeightOption;
            }

            foreach ($attrs as $attrib => $value) {
                if (in_array($attrib, ['width', 'height'])) {
                    $params .= ' ' . $attrib . '="' . trim($value) . '"';
                } elseif ($attrib == 'alt' && trim($value) != '') {
                    $params .= ' alt=' . trim($value);
                } elseif ($attrib == 'src') {
                    $src = trim($value);
                }
            }

            $tag = '';
            if (! empty($src)) {
                // Attempt to fix the path in case it's not present.
                if (preg_match('~^https?://~i', $src) === 0 && is_array($parsedURL = $this->parse_iri($this->scripturl)) && isset($parsedURL['host']))
                {
                    $baseURL = (isset($parsedURL['scheme']) ? $parsedURL['scheme'] : 'http') . '://' . $parsedURL['host'] . (empty($parsedURL['port']) ? '' : ':' . $parsedURL['port']);

                    if (substr($src, 0, 1) === '/')
                        $src = $baseURL . $src;
                    else
                        $src = $baseURL . (empty($parsedURL['path']) ? '/' : preg_replace('~/(?:index\\.php)?$~', '', $parsedURL['path'])) . '/' . $src;
                }

                $tag = '[img' . $params . ']' . $src . '[/img]';
            }

            // Replace the tag
            $text = substr($text, 0, $start_pos) . $tag . substr($text, $end_pos);
        }
        if (! $returnTagOnly) {
            return $text;
        }
        return $tag;
    }

    // credit smf, with changes
    public function parseLinkTags($text)
    {
        	// What about URL's - the pain in the ass of the tag world.
        while (preg_match('~<a\s+([^<>]*)>([^<>]*)</a>~i', $text, $matches) === 1)
        {
            // Find the position of the URL.
            $start_pos = strpos($text, $matches[0]);
            if ($start_pos === false)
                break;
            $end_pos = $start_pos + strlen($matches[0]);

            $tag_type = 'url';
            $href = '';

            $attrs = $this->fetchTagAttributes($matches[1]);
            foreach ($attrs as $attrib => $value)
            {
                if ($attrib == 'href')
                {
                    $href = trim($value);

                    // Are we dealing with an FTP link?
                    if (preg_match('~^ftps?://~', $href) === 1)
                        $tag_type = 'ftp';

                    // Or is this a link to an email address?
                    elseif (substr($href, 0, 7) == 'mailto:')
                    {
                        $tag_type = 'email';
                        $href = substr($href, 7);
                    }

                    // No http(s), so attempt to fix this potential relative URL.
                    elseif (preg_match('~^https?://~i', $href) === 0 && is_array($parsedURL = $this->parse_iri($this->scripturl)) && isset($parsedURL['host']))
                    {
                        $baseURL = (isset($parsedURL['scheme']) ? $parsedURL['scheme'] : 'http') . '://' . $parsedURL['host'] . (empty($parsedURL['port']) ? '' : ':' . $parsedURL['port']);

                        if (substr($href, 0, 1) === '/')
                            $href = $baseURL . $href;
                        else
                            $href = $baseURL . (empty($parsedURL['path']) ? '/' : preg_replace('~/(?:index\\.php)?$~', '', $parsedURL['path'])) . '/' . $href;
                    }
                }

                // External URL?
                if ($attrib == 'target' && $tag_type == 'url')
                {
                    if (trim($value) == '_blank')
                        $tag_type == 'iurl';
                }
            }

            $tag = '';
            if ($href != '')
            {
                if ($matches[2] == $href)
                    $tag = '[' . $tag_type . ']' . $href . '[/' . $tag_type . ']';
                else
                    $tag = '[' . $tag_type . '=' . $href . ']' . $matches[2] . '[/' . $tag_type . ']';
            }

            // Replace the tag
            $text = substr($text, 0, $start_pos) . $tag . substr($text, $end_pos);
            return $text;
        }
    }

    /**
     * smf function
     * Returns an array of attributes associated with a tag.
     *
     * @param string $text A tag
     * @return array An array of attributes
     */
    private function fetchTagAttributes($text)
    {
        $attribs = [];
        $key = $value = '';
        $tag_state = 0; // 0 = key, 1 = attribute with no string, 2 = attribute with string
        for ($i = 0; $i < strlen($text); $i++)
        {
            // We're either moving from the key to the attribute or we're in a string and this is fine.
            if ($text[$i] == '=')
            {
                if ($tag_state == 0)
                    $tag_state = 1;
                elseif ($tag_state == 2)
                    $value .= '=';
            }
            // A space is either moving from an attribute back to a potential key or in a string is fine.
            elseif ($text[$i] == ' ')
            {
                if ($tag_state == 2)
                    $value .= ' ';
                elseif ($tag_state == 1)
                {
                    $attribs[$key] = $value;
                    $key = $value = '';
                    $tag_state = 0;
                }
            }
            // A quote?
            elseif ($text[$i] == '"')
            {
                // Must be either going into or out of a string.
                if ($tag_state == 1)
                    $tag_state = 2;
                else
                    $tag_state = 1;
            }
            // Otherwise it's fine.
            else
            {
                if ($tag_state == 0)
                    $key .= $text[$i];
                else
                    $value .= $text[$i];
            }
        }

        // Anything left?
        if ($key != '' && $value != '')
            $attribs[$key] = $value;

        return $attribs;
    }
    /**
     * smf function
     * A wrapper for `parse_url($url)` that can handle URLs with international
     * characters (a.k.a. IRIs)
     *
     * @param string $iri The IRI to parse.
     * @param int $component Optional parameter to pass to parse_url().
     * @return mixed Same as parse_url(), but with unmangled Unicode.
     */
    private function parse_iri($iri, $component = -1)
    {
        $iri = preg_replace_callback(
            '~[^\x00-\x7F\pZ\pC]|%~u',
            function($matches)
            {
                return rawurlencode($matches[0]);
            },
            $iri
        );

        $parsed = parse_url($iri, $component);

        if (is_array($parsed))
        {
            foreach ($parsed as &$part)
                $part = rawurldecode($part);
        }
        elseif (is_string($parsed))
            $parsed = rawurldecode($parsed);

        return $parsed;
    }

    public function setImageHeight(string $height): void
    {
        $this->imageHeightOption = $height;
    }

    public function setImageWidth(string $width): void
    {
        $this->imageWidthOption = $width;
    }
}
