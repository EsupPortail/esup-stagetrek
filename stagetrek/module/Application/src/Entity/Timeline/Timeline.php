<?php

namespace Application\Entity\Timeline;

use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use DateInterval;
use DateTime;

class Timeline
{
    /**
     * Timeline constructor.
     * @param int $id
     * @param String $libelle
     * @param DateTime|null $start
     * @param DateTime|null $end
     */
    public function __construct(int $id, string $libelle, DateTime $start=null, DateTime $end=null)
    {
        $this->id = $id;
        $this->libelle = $libelle;
        if($start) {
            $this->start = $start;
            $this->defaultStart = $start;
        }
        else{
            $this->start = new DateTime();
            $this->defaultStart = new DateTime();
        }
        if($end){
            $this->end = $end;
            $this->defaultEnd = $end;
        }
        else{
            $date = new DateTime();
            $date->add(new DateInterval("P1M"));
            $this->end = $date;
            $this->defaultEnd = $date;
        }
        $this->timeFrames = [];
        $this->timesPeriodes = [];
    }

    use HasIdTrait;
    use HasLibelleTrait;


    protected ?DateTime $defaultStart = null;
    protected ?DateTime $defaultEnd= null;
    protected ?DateTime $start= null;
    protected ?DateTime $end= null;

    /**
     * @return \DateTime|null
     */
    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    /**
     * @param DateTime $start
     * @return \Application\Entity\Timeline\Timeline
     */
    public function setStart(DateTime $start) : static
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    /**
     * @param DateTime $end
     * @return \Application\Entity\Timeline\Timeline
     */
    public function setEnd(DateTime $end): static
    {
        $this->end = $end;
        return $this;
    }

    /** @var TimeFrame[] $timeFrames */
    protected array $timeFrames;

    /**
     * @return TimeFrame[]
     */
    public function getTimeFrames(): array
    {
        return $this->timeFrames;
    }

    /**
     * @param TimeFrame $frame
     * @return \Application\Entity\Timeline\Timeline
     * @throws \Exception
     */
    public function addTimeFrame(TimeFrame $frame): static
    {
        /* Changement éventuelle des dates aux limites */
        if($this->start > $frame->getStart()){ $this->start = $frame->getStart();}
        if($this->end < $frame->getEnd()){ $this->end = $frame->getEnd();}
        //On insére en réordonnat les timeframe
        $res = [];
        $i=0;
        while($i<sizeof($this->timeFrames)){
            if($frame->commpare($this->timeFrames[$i])>0){
                $res[] = $this->timeFrames[$i];
                $i++;
            }
            else{break;}
        }
        $res[] = $frame;
        while($i<sizeof($this->timeFrames)){
            $res[] = $this->timeFrames[$i];
            $i++;
        }
        $this->timeFrames= $res;
        return $this;
    }


    /** @var TimePeriode[] $timesPeriodes */
    protected array $timesPeriodes;
    /**
     * @return TimePeriode[]
     */
    public function getTimesPeriodes(): array
    {
        return $this->timesPeriodes;
    }


    /**
     * @param TimePeriode $periode
     * @return \Application\Entity\Timeline\Timeline
     * @throws \Exception
     */
    public function addTimePeriode(TimePeriode $periode): static
    {
        /* Changement éventuelle des dates aux limites */
        if($this->start > $periode->getStart()){ $this->start = $periode->getStart();}
        if($this->end < $periode->getEnd()){ $this->end = $periode->getEnd();}
        //On insére en réordonnat les timeframe
        $res = [];
        $i=0;
        while($i<sizeof($this->timesPeriodes)){
            if($periode->commpare($this->timesPeriodes[$i])>0){
                $res[] = $this->timesPeriodes[$i];
                $i++;
            }
            else{break;}
        }
        $res[] = $periode;
        while($i<sizeof($this->timesPeriodes)){
            $res[] = $this->timesPeriodes[$i];
            $i++;
        }
        $this->timesPeriodes= $res;
        return $this;
    }

}