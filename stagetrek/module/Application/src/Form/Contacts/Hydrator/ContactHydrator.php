<?php


namespace Application\Form\Contacts\Hydrator;

use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\TerrainStage;
use Application\Form\Affectation\Fieldset\AffectationStageFieldset;
use Application\Form\Contacts\Fieldset\ContactFieldset;
use Application\Form\Contacts\Fieldset\ContactTerrainFieldset;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenTag\Entity\Db\Tag;
use UnicaenTag\Service\Tag\TagServiceAwareTrait;

class ContactHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use TagServiceAwareTrait;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var Contact $contact */
        $contact = $object;
        $data = [];
        $data[ContactFieldset::ID] = $contact->getId();
        $data[ContactFieldset::CODE] = $contact->getCode();
        $data[ContactFieldset::LIBELLE] = $contact->getLibelle();
        $data[ContactFieldset::DISPLAY_NAME] = $contact->getDisplayName();
        $data[ContactFieldset::MAIL] = $contact->getEmail();
        $data[ContactFieldset::TELEPHONE] = $contact->getTelephone();
        $data[ContactFieldset::ACTIF] = $contact->isActif();

        foreach ($contact->getTags() as $t) {
            $data[ContactFieldset::TAGS][] = $t->getId();
        }
        return $data;
    }

    /**
     * @param array $data
     * @param $object
     * @return object
     */
    public function hydrate(array $data, $object): object
    {
        /** @var Contact $contact */
        $contact = $object;

        $code = ($data[ContactFieldset::CODE]) ?? null;
        $displayName = ($data[ContactFieldset::DISPLAY_NAME]) ?? null;
        $libelle = ($data[ContactFieldset::LIBELLE]) ?? null;
        $mail = ($data[ContactFieldset::MAIL]) ?? null;
        $telephone = ($data[ContactFieldset::TELEPHONE]) ?? null;
        $actif = (isset($data[ContactFieldset::ACTIF])) ? boolval($data[ContactFieldset::ACTIF]) :false;

        $contact->setCode($code);
        $contact->setDisplayName($displayName);
        $contact->setLibelle($libelle);
        $contact->setEmail($mail);
        $contact->setTelephone($telephone);
        $contact->setActif($actif);

        if (isset($data[ContactFieldset::TAGS])) {
            $tagsSelected = $data[ContactFieldset::TAGS];
            /** @var Tag[] $tags */
            $tags = $this->getTagService()->getTags();
            $tags = array_filter($tags, function (Tag $t) use ($tagsSelected) {
                return in_array($t->getId(), $tagsSelected);
            });
            $contact->getTags()->clear();
            foreach ($tags as $t) {
                $contact->addTag($t);
            }
        } else {
            $contact->getTags()->clear();
        }
        return $contact;
    }
}