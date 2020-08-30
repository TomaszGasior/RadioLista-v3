<?php

namespace App\Twig;

use Twig\Compiler as BaseCompiler;
use Twig\Node\Node;
use Twig\Node\TextNode;

/**
 * @codeCoverageIgnore
 */
class Compiler extends BaseCompiler
{
    private $ignoredTemplates = [];

    public function compile(Node $node, $indentation = 0): self
    {
        $this->compressTextNodes($node);

        return parent::{__FUNCTION__}(...func_get_args());
    }

    public function subcompile(Node $node, $raw = true): self
    {
        $this->compressTextNodes($node);

        return parent::{__FUNCTION__}(...func_get_args());
    }

    private function compressTextNodes(Node $node): void
    {
        if (in_array($node->getTemplateName(), $this->ignoredTemplates)) {
            return;
        }

        // Don't try to minify CSV files.
        if (false !== strpos($node->getTemplateName(), '.csv.twig')) {
            return;
        }

        foreach ($node as $childNode) {
            $this->{__FUNCTION__}($childNode);
        }

        if ($node instanceof TextNode) {
            $data = $node->getAttribute('data');

            // Don't minify pages with <pre> tags.
            if (false !== mb_strpos($data, '<pre')) {
                $this->ignoredTemplates[] = $node->getTemplateName();
                return;
            }

            // Only remove empty lines and repeated spaces in templates of external bundles.
            if ('@' === $node->getTemplateName()[0]) {
                $data = preg_replace(['/^\n+/m', '/ +/'], ['', ' '], $data);
            }
            // Replace all whitespace character sequences with just one space. This kind
            // of "minification" might break inline JS scripts but it seems to be fine
            // for this application. Do it only for application's own templates.
            else {
                $data = preg_replace('/\s+/', ' ', str_replace("\n", '', $data));
            }

            $node->setAttribute('data', $data);
        }
    }
}
