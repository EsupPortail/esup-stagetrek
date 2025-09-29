<?php


namespace Application\Form\Annees\Hydrator;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Etudiant;
use Application\Form\Adresse\Fieldset\AdresseFieldset;
use Application\Form\Annees\Fieldset\AnneeUniversitaireFieldset;
use Application\Form\Etudiant\Fieldset\EtudiantFieldset;
use DateTime;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use Laminas\I18n\View\Helper\DateFormat;
use UnicaenTag\Entity\Db\Tag;
use UnicaenTag\Service\Tag\TagServiceAwareTrait;
use UnicaenUtilisateur\Entity\Db\User;

/**
 * Class EtudiantHydrator
 * @package Application\Form\Etudiant\Hydrator
 */
class AnneeUniversitaireHydrator extends AbstractHydrator implements HydratorInterface
{
    use TagServiceAwareTrait;

    /**
     * @param AnneeUniversitaire $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $object;

            foreach ($annee->getTags() as $t) {
                $tags[] = $t->getId();
            }

        return [
            AnneeUniversitaireFieldset::ID => $annee->getId(),
            AnneeUniversitaireFieldset::CODE => $annee->getCode(),
            AnneeUniversitaireFieldset::LIBELLE => $annee->getLibelle(),
            AnneeUniversitaireFieldset::DATE_DEBUT => $annee->getDateDebut()->format('Y-m-d'),
            AnneeUniversitaireFieldset::DATE_FIN => $annee->getDateFin()->format('Y-m-d'),
            AnneeUniversitaireFieldset::TAGS => ($tags) ?? [],
        ];
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param mixed $object
     * @return AnneeUniversitaire
     */
    public function hydrate(array $data, mixed $object): AnneeUniversitaire
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $object;
        $code = (isset($data[AnneeUniversitaireFieldset::CODE]) && $data[AnneeUniversitaireFieldset::CODE] != "") ? $data[AnneeUniversitaireFieldset::CODE] : null;
        $libelle = ($data[AnneeUniversitaireFieldset::LIBELLE]) ?? null;
        $dateDebut = (isset($data[AnneeUniversitaireFieldset::DATE_DEBUT]) && $data[AnneeUniversitaireFieldset::DATE_DEBUT] != "") ? DateTime::createFromFormat('Y-m-d', $data[AnneeUniversitaireFieldset::DATE_DEBUT]) : null;
        $dateFin = (isset($data[AnneeUniversitaireFieldset::DATE_FIN]) && $data[AnneeUniversitaireFieldset::DATE_FIN] != "") ? DateTime::createFromFormat('Y-m-d', $data[AnneeUniversitaireFieldset::DATE_FIN]) : null;
        $annee->setCode($code);
        $annee->setLibelle($libelle);
        $annee->setDateDebut($dateDebut);
        $annee->setDateFin($dateFin);

        if (isset($data[AnneeUniversitaireFieldset::TAGS])) {
            $tagsSelected = $data[AnneeUniversitaireFieldset::TAGS];
            /** @var Tag[] $tags */
            $tags = $this->getTagService()->getTags();
            $tags = array_filter($tags, function (Tag $t) use ($tagsSelected) {
                return in_array($t->getId(), $tagsSelected);
            });
            $annee->getTags()->clear();
            foreach ($tags as $t) {
                $annee->addTag($t);
            }
        } else {
            $annee->getTags()->clear();
        }
        return $annee;
    }
}