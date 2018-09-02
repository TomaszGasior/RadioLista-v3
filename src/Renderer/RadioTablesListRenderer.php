<?php

namespace App\Renderer;

use App\Entity\RadioTable;

class RadioTablesListRenderer
{
    public const OPTION_USE_CACHE       = 0b00001;
    public const OPTION_SHOW_OWNER      = 0b00010;
    public const OPTION_SHOW_VISIBILITY = 0b00100;
    public const OPTION_SHOW_ACTIONS    = 0b01000;

    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(iterable $radioTables, ?int $options = self::OPTION_SHOW_OWNER)
    {
        if (empty($radioTables)) {
            return '';
        }

        return $this->process($radioTables, $options);
    }

    private function process(iterable $radioTables, ?int $options)
    {
        return $this->twig->render('renderer/radiotables_list.html.twig', [
            'radiotables' => $radioTables,

            'show_owner'      => ($options & self::OPTION_SHOW_OWNER),
            'show_actions'    => ($options & self::OPTION_SHOW_VISIBILITY),
            'show_visibility' => ($options & self::OPTION_SHOW_ACTIONS),
        ]);
    }
}
