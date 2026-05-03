<?php

namespace App\Domains\Marketing\Services;

class TemplateRenderer
{
    /**
     * @param array<string, mixed> $variables
     */
    public function render(string $template, array $variables): string
    {
        return preg_replace_callback('/{{\s*([a-zA-Z0-9_\.]+)\s*}}/', function (array $matches) use ($variables): string {
            $value = data_get($variables, $matches[1]);

            return is_scalar($value) ? (string) $value : '';
        }, $template) ?? $template;
    }
}
