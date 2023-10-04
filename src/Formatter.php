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
    public static $targetTags = ['a', 'strong', 's', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

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
        $this->formatted = preg_replace('/\n(\s*\n){2,}/', "\n\n", $content);
        $this->formatted = $bbCode->convertFromHtml(strip_tags($this->formatted, $targetTags));
        return $this->formatted;
    }
}
