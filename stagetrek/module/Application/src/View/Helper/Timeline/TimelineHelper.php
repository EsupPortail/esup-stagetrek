<?php


namespace Application\View\Helper\Timeline;

use Application\Entity\Timeline\TimeFrame;
use Application\Entity\Timeline\Timeline;
use Application\Entity\Timeline\TimePeriode;
use DateInterval;
use DateTime;
use Exception;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class TimelineHelper
 * @package Application\View\Helper\Timeline
 */
class TimelineHelper extends AbstractHelper
{
    /**
     * Forte dépendance avec le css; a voir comment faire pour s'en passer
     * Voir comment gérer proprement les cas ou les timeFrame se superpose car pose des bugs graphiques
     */

    /** @var Timeline $timeline */
    protected $timeline;

    /**
     * @param Timeline|null $timeline
     * @return string
     * @throws Exception
     */
    public function __invoke(Timeline $timeline = null)
    {
        $this->timeline = $timeline;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function render()
    {
        if(!$this->timeline) return "";
        $html = $this->indent(0) . "<section id='timeline_" . $this->timeline->getId() . "-link' class='timeline'>";
        if ($this->timeline->getStart() != null && $this->timeline->getEnd() != null) {
            $html .= $this->printTimelineHeader($this->timeline->getStart(), $this->timeline->getEnd());
            $html .= $this->printTimeLine($this->timeline, $this->timeline->getStart(), $this->timeline->getEnd());
        }
        $html .= $this->indent(0) . "</section >";
        return $html;
    }

    /**
     * @param Integer $depth
     * @return string
     */
    protected function indent($depth)
    {
        $html = PHP_EOL . "";
        for ($i = 0; $i < $depth; $i++)
            $html .= "\t";
        return $html;
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return string
     * @throws Exception
     */
    protected function printTimelineHeader(DateTime $start, DateTime $end)
    {
        $start = clone($start);
        $end = clone($end);
        $start->setDate($start->format('Y'), $start->format('m'), 1);
        $end->setDate($end->format('Y'), $end->format('m'), $end->format('t'));
        $depth = 1;
        $html = $this->indent($depth++) . "<div class='tl_header'>";
        $html .= $this->printTimelineHeaderYears($start, $end);
        $html .= $this->printTimelineHeaderMonths($start, $end);
        $html .= $this->indent(--$depth) . "</div>";
        return $html;
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return string
     * @throws Exception
     */
    protected function printTimelineHeaderYears(DateTime $start, DateTime $end)
    {
        $timelineDuration = $start->diff($end)->days + 1; //+1 pour prendre en compte le fait que si date de début et date de fin sont les même, on a quand même 1 jours
        $depth = 2;
//        $html = $this->indent($depth++) . "<ol class='tl_years' style='grid-template-columns: repeat(" . $timelineDuration . ", 1fr);'>";
        $html = $this->indent($depth++) . "<ol class='tl_years' style='grid-template-columns: repeat(" . $timelineDuration . ", minmax(0.1rem, 1fr));'>";

        //Cas 1 : le début et la fin sont la même année
        $years = [];
        if (intval($start->format('Y')) == intval($end->format('Y'))) {
            $years[intval($start->format('Y'))] = $timelineDuration;
        } else {
            $currentYearValue = intval($start->format('Y')) + 1;
            $currentYear = new DateTime();
            $currentYear->setDate($currentYearValue, 1, 1);
            $currentYear->setTime(0, 0, 0);
            //Fin de la premiére année
            $years[intval($start->format('Y'))] = $start->diff($currentYear)->days;
            //Toutes les années pleines
            while ($currentYearValue < intval($end->format('Y'))) {
                $currentYearValue++;
                $currentYear->setDate($currentYearValue, 1, 1);
                $years[$currentYearValue - 1] = 365 + intval($currentYear->format('L')) + 1; // format 'L' : 1 si l'année est bissextile
            }
            //Derniére années
            $years[intval($end->format('Y'))] = $currentYear->diff($end)->days + 1;
        }
        $start_position = 1;
        foreach ($years as $y => $d) {
            $html .= $this->indent($depth) . "<li class='tl_year' style='";
            $html .= "grid-column-start:" . $start_position . ";";
            $html .= "grid-column-end: span " . $d . ";";
            $html .= "'>" . $y . '</li>';
            $start_position += $d;
        }
        $html .= $this->indent(--$depth) . "</ol>";
        return $html;
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return string
     * @throws Exception
     */
    protected function printTimelineHeaderMonths(DateTime $start, DateTime $end)
    {
        $currentDate = new DateTime();
        $currentDate->setDate($start->format('Y'), $start->format('m'), 1);
        $timelineDuration = $start->diff($end)->days + 1; //+1 pour prendre en compte le fait que si date de début et date de fin sont les même, on a quand même 1 jours

        $depth = 2;
//        $html = $this->indent($depth++) . "<ol class='tl_months' style='grid-template-columns: repeat(" . $timelineDuration . ", 1fr);'>";
        $html = $this->indent($depth++) . "<ol class='tl_months' style='grid-template-columns: repeat(" . $timelineDuration . ", minmax(0.1rem, 1fr));'>";

        $start_position = 1;
        while ($currentDate < $end) {
            $d = intval($currentDate->format('t'));
            $html .= $this->indent($depth) . "<li class='tl_month' style='";
            $html .= "grid-column-start:" . $start_position . ";";
            $html .= "grid-column-end: span " . $d . ";";
            $html .= "'>" . $currentDate->format('M') . '</li>';
            $start_position += $d;
            $currentDate->add(new DateInterval("P1M"));
        }
        $html .= $this->indent(--$depth) . "</ol>";

        return $html;
    }

    /**
     * @param Timeline $timeline
     * @param DateTime $start
     * @param DateTime $end
     * @return string
     */
    protected function printTimeLine(Timeline $timeline, DateTime $start, DateTime $end)
    {
        $depth = 1;
        $start = clone($start);
        $end = clone($end);
        $start->setDate($start->format('Y'), $start->format('m'), 1);
        $end->setDate($end->format('Y'), $end->format('m'), $end->format('t'));

        $html = $this->indent($depth++) . "<div class='tl_row'>";
        $html .= $this->indent($depth) . "<div class='tl_title'>" . $timeline->getLibelle() . "</div>";
        if ($timeline->getTimeFrames() && $timeline->getTimeFrames() != []) {
            $timelineDuration = $start->diff($end)->days + 1; //+1 pour prendre en compte le fait que si date de début et date de fin sont les même, on a quand même 1 jours
//            $html .= $this->indent($depth++) . "<ol class='tl_timeframes' style='grid-template-columns: repeat(" . $timelineDuration . ", 1fr);'>";
            $html .= $this->indent($depth++) . "<ol class='tl_timeframes' style='grid-template-columns: repeat(" . $timelineDuration . ", minmax(0.1rem, 1fr));'>";

//            TODO : modifier cest pseudoclass pour qu'elle couvre les zone de péride ou non période"
//            Modifier le css pour que ce soit de la vrais transparance.
            /** @var TimePeriode $timePeriode */
            foreach ($timeline->getTimesPeriodes() as $timePeriode) {
                $start_position = $start->diff($timePeriode->getStart())->days + 1;
                $duration = $timePeriode->getStart()->diff($timePeriode->getEnd())->days +1;
                $html .= $this->indent($depth++) . "<li class='tl_periode' ";
                $html .= "data-id='" . $timePeriode->getId() . "' ";
                $html.= "style='";
                $html .= "grid-column-start: " . $start_position . ";";
                $html .= "grid-column-end: span " . $duration . ";'>";
                $html .= $this->indent($depth--) . "</li>";
            }
            /** @var TimeFrame $timeFrame */
            foreach ($timeline->getTimeFrames() as $timeFrame) {
                $start_position = $start->diff($timeFrame->getStart())->days + 1;
                $duration = $timeFrame->getStart()->diff($timeFrame->getEnd())->days + 1;
                $html .= $this->indent($depth++) . "<li id='timeFrame_" . $timeFrame->getId() . "' class='tl_timeframe' ";
                $html .= "data-id='" . $timeFrame->getId() . "' ";
                 $html.= "style='";
                $html .= "cursor: pointer;";
                $html .= "grid-column-start: " . $start_position . ";";
                $html .= "grid-column-end: span " . $duration . ";'>";
                $html .= $this->indent($depth) . $timeFrame->getLibelle();
//                if ($timeFrame->getTextInfos() && $timeFrame->getTextInfos() != "") {
//                    $html .= $this->indent($depth++) . "<div class='tf_info' id='tf_info_" . $timeFrame->getId() . "'>";
//                    $html .= $this->indent($depth) . $timeFrame->getTextInfos();
//                    $html .= $this->indent(--$depth) . "</div>";
//                }
                $html .= $this->indent($depth--) . "</li>";
            }
            $html .= $this->indent(--$depth) . "</ol>";
        }
        $html .= $this->indent(--$depth) . "</div>";


        return $html;
    }

    /**
     * @param Timeline[] $timelines
     * @return string
     * @throws Exception
     */
    public function multipleTimeline($timelines)
    {
        if (!$timelines || $timelines == []) return "";
        $html = $this->indent(0) . "<section";
        $html .= " class='timeline multiple_timeline'>";
        //On met toutes les timelines sur les même dates
        $start = current($timelines)->getStart();
        $end = current($timelines)->getEnd();
        foreach ($timelines as $timeline) {
            if ($start > $timeline->getStart()) $start = $timeline->getStart();
            if ($end < $timeline->getEnd()) $end = $timeline->getEnd();
        }

        $html .= $this->printTimelineHeader($start, $end);
        foreach ($timelines as $timeline) {
            $html .= $this->printTimeLine($timeline, $start, $end);
        }
        $html .= $this->indent(0) . "</section >";
        return $html;
    }
}



