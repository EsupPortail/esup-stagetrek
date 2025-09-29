<?php

namespace Application\Form\TerrainStage\Hydrator;

use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\Contact;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Db\NiveauEtude;
use Application\Entity\Db\TerrainStage;
use Application\Form\Contacts\Fieldset\ContactFieldset;
use Application\Form\TerrainStage\Fieldset\CategorieStageFieldset;
use Application\Form\TerrainStage\Fieldset\TerrainStageFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenTag\Entity\Db\Tag;
use UnicaenTag\Service\Tag\TagServiceAwareTrait;

/**
 * Class TerrainStageHydrator
 * @package Application\Form\TerrainStage\Hydrator
 */
class CategorieStageHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use TagServiceAwareTrait;

    /**
     * Extract values from an object
     *
     * @param object $object
     * @return array
     */
    public function extract(object $object): array
    {
        /** @var CategorieStage $categorieStage */
        $categorieStage = $object;

        $data = [];
        $data[CategorieStageFieldset::ID] = $categorieStage->getId();
        $data[CategorieStageFieldset::CODE] = $categorieStage->getCode();
        $data[CategorieStageFieldset::LIBELLE] = $categorieStage->getLibelle();
        $data[CategorieStageFieldset::ACRONYME] = $categorieStage->getAcronyme();
        $data[CategorieStageFieldset::ORDRE] = $categorieStage->getOrdre();
        $data[CategorieStageFieldset::CATEGORIE_PRINCIPALE] = $categorieStage->isCategoriePrincipale();

        foreach ($categorieStage->getTags() as $t) {
            $data[ContactFieldset::TAGS] [] = $t->getId();
        }
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param object $object
     * @return CategorieStage
     */
    public function hydrate(array $data, object $object): CategorieStage
    {
        /** @var CategorieStage $categorieStage */
        $categorieStage = $object;


        $code = ($data[CategorieStageFieldset::CODE]) ?? null;
        $libelle = ($data[CategorieStageFieldset::LIBELLE]) ?? null;
        $acronyme = ($data[CategorieStageFieldset::ACRONYME]) ?? null;
        $ordre = (isset($data[CategorieStageFieldset::ORDRE])) ? intval($data[CategorieStageFieldset::ORDRE]) : 0;
        $principale = (isset($data[CategorieStageFieldset::CATEGORIE_PRINCIPALE])) ? boolval($data[CategorieStageFieldset::CATEGORIE_PRINCIPALE]) :false;

        $categorieStage->setCode($code);
        $categorieStage->setLibelle($libelle);
        $categorieStage->setAcronyme($acronyme);
        $categorieStage->setOrdre($ordre);
        $categorieStage->setCategoriePrincipale($principale);


        if (isset($data[CategorieStageFieldset::TAGS])) {
            $tagsSelected = $data[CategorieStageFieldset::TAGS];
            /** @var Tag[] $tags */
            $tags = $this->getTagService()->getTags();
            $tags = array_filter($tags, function (Tag $t) use ($tagsSelected) {
                return in_array($t->getId(), $tagsSelected);
            });
            $categorieStage->getTags()->clear();
            foreach ($tags as $t) {
                $categorieStage->addTag($t);
            }
        } else {
            $categorieStage->getTags()->clear();
        }

        return $categorieStage;
    }
}