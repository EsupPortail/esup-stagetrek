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

}