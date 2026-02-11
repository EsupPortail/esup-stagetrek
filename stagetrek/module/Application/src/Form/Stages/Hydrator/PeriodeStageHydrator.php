<?php


namespace Application\Form\Stages\Hydrator;

use Application\Entity\Db\Groupe;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\SessionStage;
use Application\Form\Stages\Fieldset\PeriodeStageFieldset;
use Application\Form\Stages\Fieldset\SessionStageFieldset;
use Application\Provider\Tag\TagProvider;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use DateInterval;
use DateTime;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenCalendrier\Entity\Db\Calendrier;
use UnicaenCalendrier\Entity\Db\CalendrierType;
use UnicaenCalendrier\Entity\Db\Date;
use UnicaenCalendrier\Entity\Db\DateType;
use UnicaenCalendrier\Service\CalendrierType\CalendrierTypeServiceAwareTrait;
use UnicaenCalendrier\Service\DateType\DateTypeServiceAwareTrait;
use UnicaenTag\Entity\Db\Tag;
use UnicaenTag\Service\Tag\TagServiceAwareTrait;

/**
 * Class SessionStageHydrator
 * @package Application\Form\SessionsStages\Hydrator
 */
class PeriodeStageHydrator extends AbstractHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var Date $periode */
        $periode = $object;

        return [
            PeriodeStageFieldset::ID => $periode->getId(),
            PeriodeStageFieldset::DATE_DEBUT => ($periode->getDebut()) ?? null,
            PeriodeStageFieldset::DATE_FIN => ($periode->getFin()) ?? null,
        ];
    }

    /**
     * Hydrate $object with the provided $data.
     * @param array $data
     * @param $object
     * @return SessionStage
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \DateMalformedIntervalStringException
     */
    public function hydrate(array $data, $object): Date
    {
        /** @var Date $periode */
        $periode = $object;
        $debut = ($data[PeriodeStageFieldset::DATE_DEBUT]) ?? null;
        $fin = ($data[PeriodeStageFieldset::DATE_FIN]) ?? null;
        $debut = DateTime::createFromFormat('Y-m-d', $debut);
        $fin = DateTime::createFromFormat('Y-m-d', $fin);
        if($debut && $fin){
            $periode->setDebut($debut);
            $periode->setFin($fin);
        }
        else{
            throw new \Exception("Impossible de déterminer la date de début ou de fin de la période");
        }
        return $periode;
    }

}