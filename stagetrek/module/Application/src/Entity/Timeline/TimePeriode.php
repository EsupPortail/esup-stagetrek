<?php

namespace Application\Entity\Timeline;
//PErmet de gerer les stages ayant plusieurs période (donne le fond aux période de stage (a supprimer lors d'un passage en VueJs)
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use DateTime;
use Exception;

class TimePeriode
{
    /**
     * TimeFrame constructor.
     * @param int $id
     * @param \DateTime|null $start
     * @param \DateTime|null $end
     */
    public function __construct(int $id, DateTime $start=null, DateTime $end=null)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
    }

    use HasIdTrait;


    /**
     * @var DateTime|null $start
     *  @var DateTime|$end
     */
    protected ?DateTime $start = null;
    protected ?DateTime $end = null;

    /**
     * @return \DateTime|null
     */
    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    /**
     * @param DateTime $start
     * @return \Application\Entity\Timeline\TimeFrame
     */
    public function setStart(DateTime $start): static
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime
    {
        return $this->end;
    }

    /**
     * @param DateTime $end
     * @return \Application\Entity\Timeline\TimeFrame
     */
    public function setEnd(DateTime $end) : static
    {
        $this->end = $end;
        return $this;
    }

    //Code pour la comparaison basé sur l'algébre de Allen
    /**
     * @param DateTime $date
     * @return Bool
     */
    public function startBefore(DateTime $date): bool
    {
        return $this->getStart() < $date;
    }
    /**
     * @param DateTime $date
     * @return Bool
     */
    public function startAfter(DateTime $date): bool
    {
        return $this->getStart() > $date;
    }
    /**
     * @param DateTime $date
     * @return Bool
     */
    public function startAt(DateTime $date): bool
    {
        return $this->getStart() == $date;
    }
    /**
     * @param DateTime $date
     * @return Bool
     */
    public function endBefore(DateTime $date): bool
    {
        return $this->getEnd() < $date;
    }
    /**
     * @param DateTime $date
     * @return Bool
     */
    public function endAfter(DateTime $date): bool
    {
        return $this->getEnd() > $date;
    }
    /**
     * @param DateTime $date
     * @return Bool
     */
    public function endAt(DateTime $date): bool
    {
        return $this->getEnd() == $date;
    }

    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function before(TimePeriode $timeFrame): bool
    {
        return $this->endBefore($timeFrame->getStart());
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function after(TimePeriode $timeFrame): bool
    {
        return $this->startAfter($timeFrame->getEnd());
    }

    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function meets(TimePeriode $timeFrame): bool
    {
        return $this->endAt($timeFrame->getStart());
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function meetedBy(TimePeriode $timeFrame): bool
    {
        return $timeFrame->meets($this);
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function overlaps(TimePeriode $timeFrame): bool
    {
        return $this->startBefore($timeFrame->getStart())
            &&  $this->endAfter($timeFrame->getStart())
            &&  $this->endBefore($timeFrame->getEnd());
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function overlapedBy(TimePeriode $timeFrame): bool
    {
        return $timeFrame->overlaps($this);
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function contains(TimePeriode $timeFrame): bool
    {
        return $this->startBefore($timeFrame->getStart())
            && $this->endAfter($timeFrame->getEnd());
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function during(TimePeriode $timeFrame): bool
    {
        return $timeFrame->contains($this);
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function starts(TimePeriode $timeFrame): bool
    {
        return $this->startAt($timeFrame->getStart())
            && $this->endBefore($timeFrame->getEnd());
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function startedBy(TimePeriode $timeFrame): bool
    {
        return $timeFrame->starts($this);
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function finishes(TimePeriode $timeFrame): bool
    {
        return $this->endAt($timeFrame->getEnd())
            && $this->startAfter($timeFrame->getStart());
    }
    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function finishedBy(TimePeriode $timeFrame): bool
    {
        return $timeFrame->finishes($this);
    }

    /**
     * @param TimeFrame $timeFrame
     * @return Bool
     */
    public function equals(TimePeriode $timeFrame): bool
    {
        return $this->startAt($timeFrame->getStart())
            &&  $this->endAt($timeFrame->getEnd());
    }

    // Pour ordonner

    /**
     * @param TimeFrame $frame
     * int
     * @throws \Exception
     */
    public function commpare(TimePeriode $frame) : int
    {
        return match (true) {
            $this->before($frame) => -6,
            $this->meets($frame) => -5,
            $this->overlaps($frame) => -4,
            $this->finishedBy($frame) => -3,
            $this->contains($frame) => -2,
            $this->starts($frame) => -1,
            $this->equals($frame) => 0,
            $this->startedBy($frame) => 1,
            $this->during($frame) => 2,
            $this->finishes($frame) => 3,
            $this->overlapedBy($frame) => 4,
            $this->meetedBy($frame) => 5,
            $this->after($frame) => 6,
            default => throw new Exception("Cas non géré"),
        };
    }
}