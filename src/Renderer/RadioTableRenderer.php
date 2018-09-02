<?php

namespace App\Renderer;

use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;

class RadioTableRenderer
{
    public const OPTION_SHOW_EDIT_LINKS = 0b00001;
    public const OPTION_USE_CACHE       = 0b00010;

    private $templating;
    private $radioStationRepository;

    public function __construct(\Twig_Environment $templating, RadioStationRepository $radioStationRepository)
    {
        $this->templating = $templating;
        $this->radioStationRepository = $radioStationRepository;
    }

    public function render(RadioTable $radioTable, int $options = null)
    {
        if ($radioTable->getRadioStationsCount() == 0) {
            return '';
        }

        return $this->process($radioTable, $options);
    }

    private function process(RadioTable $radioTable, int $options = null)
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

        return $this->templating->render('renderer/radiotable.html.twig', [
            'radioTable' => $radioTable,
            'radioStations' => $radioStations,
            'showRadioStationEditLink' => ($options & self::OPTION_SHOW_EDIT_LINKS),
            'radioTableColumns' => $visibleColumns,
        ]);
    }
}