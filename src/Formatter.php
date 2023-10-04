<?php

declare(strict_types=1);

namespace WebiXfBridge;

use Genert\BBCode\BBCode;
use WebiXfBridge\Settings;

use function array_merge;
use function array_unique;
use function get_option;
use function preg_replace;
use function strip_tags;

final class Formatter
{
    private ?string $formatted;
    public static $targetTags = ['a', 'strong', 's', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'img'];

    public function __invoke(string $content, array|string $targetTags = null): string
    {
        return $this->format($targetTags, $content);
    }

    public function format(string $content, array|string $targetTags = []): string
    {
        $bbCode = new BBCode();
        $storedTags = get_option(Settings::targetTagsSetting->value);
        if ($storedTags) {
            $storedTags = explode(',', $storedTags);
        } else {
            $storedTags = static::$targetTags;
        }
        if ($targetTags !== null && is_string($targetTags)) {
            $targetTags = explode(',', $targetTags);
        }
        $targetTags      = array_unique(array_merge($storedTags, $targetTags));
        // todo: improve this to remove the resulting double breaks
        $this->formatted = preg_replace('/(\s+){2,}/', "<br>", $content);
        $this->formatted = $bbCode->convertFromHtml(strip_tags($this->formatted, $targetTags));

        $this->formatted = $this->parseImageTags($this->formatted);
        return $this->formatted;
    }

    private function parseImageTags($text)
    {
                // I love my own image...
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
            foreach ($attrs as $attrib => $value)
            {
                if (in_array($attrib, array('width', 'height')))
                    $params .= ' ' . $attrib . '=' . (int) $value;
                elseif ($attrib == 'alt' && trim($value) != '')
                    $params .= ' alt=' . trim($value);
                elseif ($attrib == 'src')
                    $src = trim($value);
            }

            $tag = '';
            if (!empty($src))
            {
                // Attempt to fix the path in case it's not present.
                if (preg_match('~^https?://~i', $src) === 0 && is_array($parsedURL = parse_iri($scripturl)) && isset($parsedURL['host']))
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
            return $text;
        }
    }

    /**
     * Returns an array of attributes associated with a tag.
     *
     * @param string $text A tag
     * @return array An array of attributes
     */
    private function fetchTagAttributes($text)
    {
        $attribs = array();
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
}
