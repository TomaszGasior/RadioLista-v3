<?php

namespace App\Twig;

use Twig\Compiler as BaseCompiler;
use Twig\Node\Node;
use Twig\Node\TextNode;

class Compiler extends BaseCompiler
{
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
        if (false !== strpos($node->getTemplateName(), '.csv.twig')) {
            return;
        }

        if ($node instanceof TextNode) {
            $data = $node->getAttribute('data');

            // Yep, this "minification" sucks! It breaks <pre>/<code> contents
            // and inline JavaScript without semicolons.
            // But it seems to be fine for this application purposes.
            $data = str_replace("\n", '', $data);
            $data = preg_replace('/ +/', ' ', $data);

            $node->setAttribute('data', $data);
        }

        foreach ($node as $childNode) {
            $this->{__FUNCTION__}($childNode);
        }
    }
}
