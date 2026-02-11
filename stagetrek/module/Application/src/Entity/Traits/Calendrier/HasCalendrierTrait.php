<?php

namespace Application\Entity\Traits\Calendrier;

use UnicaenCalendrier\Entity\Db\Calendrier;
use UnicaenCalendrier\Entity\Db\Date;

trait HasCalendrierTrait
{

    protected ?Calendrier $calendrier = null;
    public function getCalendrier(): ?Calendrier
    {
        return $this->calendrier;
    }

    public function setCalendrier(?Calendrier $calendrier): static
    {
        $this->calendrier = $calendrier;
        return $this;
    }

    public function getDates(string $order='asc'): array
    {
        if(!isset($this->calendrier)){return [];}
        $dates =  $this->calendrier->getDates();
        usort($dates, function (Date $d1, Date $d2) use ($order){
            $order = ($order=='desc') ? -1 : 1;
            $debut1 = $d1->getDebut();
            $debut2 = $d2->getDebut();
            if(!isset($debut1) && !isset($debut2)){return $d1->getId()-$d2->getId();}
            if(!isset($debut1) && isset($debut2)){return 1*$order;}
            if(isset($debut1) && !isset($debut2)){return -1*$order;}
            return $order*($d1->getDebut()->getTimestamp()-$d2->getDebut()->getTimestamp());
        });
        return $dates;
    }

    public function addDate(Date $date): static
    {
        if(!isset($this->calendrier)){return $this;}
        $this->calendrier->addDate($date);
        return $this;
    }

    public function removeDate(Date $date): static
    {
        if(!isset($this->calendrier)){return $this;}
        $this->calendrier->removeDate($date);
        return $this;
    }

    /** !!! Retourne la premier date du type demandé, a ne pas utiliser pour les calendrier ayant plusieurs dates d'un même types */
    public function getDateByCode(string $dateTypeCode) : ?Date
    {
        if(!isset($this->calendrier)){return null;}
        foreach ($this->calendrier->getDates() as $d){
            $t = $d->getType();
            if(isset($t) && $t->getCode() == $dateTypeCode){
                return $d;
            }
        }
        return null;
    }
}