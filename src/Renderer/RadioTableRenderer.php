<?php

namespace App\Renderer;

use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;

class RadioTableRenderer
{
    public const OPTION_USE_CACHE       = 0b00001;
    public const OPTION_SHOW_EDIT_LINKS = 0b00010;

    private $twig;
    private $radioStationRepository;

    public function __construct(\Twig_Environment $twig, RadioStationRepository $radioStationRepository)
    {
        $this->twig = $twig;
        $this->radioStationRepository = $radioStationRepository;
    }

    public function render(RadioTable $radioTable, ?int $options = null): string
    {
        if ($radioTable->getRadioStationsCount() == 0) {
            return '';
        }

        return $this->process($radioTable, $options);
    }

    private function process(RadioTable $radioTable, ?int $options): string
    {
        // RadioTable::$columns defines order and visibility of radiotable columns. This is
        // an array with columns names as keys and position numbers as values. Columns will be
        // sorted by position number. Columns with negative position number won't be visible.
        $visibleColumns = array_filter(
            $radioTable->getColumns(),
            function($position){ return $position > 0; }
        );
        asort($visibleColumns);
        $visibleColumns = array_keys($visibleColumns);

        $radioStations = $this->radioStationRepository->findForRadioTable($radioTable);

        return $this->twig->render('renderer/radiotable.html.twig', [
            'radiotable'      => $radioTable,
            'radiostations'   => $radioStations,
            'visible_columns' => $visibleColumns,
            'show_edit_links' => ($options & self::OPTION_SHOW_EDIT_LINKS),
        ]);
    }
}
