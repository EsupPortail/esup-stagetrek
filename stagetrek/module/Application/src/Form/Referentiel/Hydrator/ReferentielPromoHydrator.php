<?php



namespace Application\Form\Referentiel\Hydrator;

use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Form\Referentiel\Fieldset\ReferentielPromoFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class ReferentielPromoHydrator
 * @package Application\Form\Hydrator
 */
class ReferentielPromoHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var ReferentielPromo $referentiel */
        $referentiel = $object;
        $data = [];
        $data[ReferentielPromoFieldset::CODE] = ($referentiel->getCode()) ?? null ;
        $data[ReferentielPromoFieldset::ID] = ($referentiel->getId()) ?? null;
        $data[ReferentielPromoFieldset::SOURCE] = ($referentiel->getSource()) ? $referentiel->getSource()->getId() : 0;
        $data[ReferentielPromoFieldset::LIBELLE] = ($referentiel->getLibelle()) ?? "";
        $data[ReferentielPromoFieldset::CODE_PROMO] = ($referentiel->getCodePromo()) ?? "";
        $data[ReferentielPromoFieldset::ORDRE] = ($referentiel->getOrdre()) ?? null;
        return $data;
    }

    /**
     * @param array $data
     * @param object $object
     * @return ReferentielPromo
     */
    public function hydrate(array $data, object $object ) : ReferentielPromo
    {
        /** @var ReferentielPromo $referentiel */
        $referentiel = $object;

        if(isset($data[ReferentielPromoFieldset::CODE])) $referentiel->setCode(trim($data[ReferentielPromoFieldset::CODE]));
        if(isset($data[ReferentielPromoFieldset::LIBELLE])) $referentiel->setLibelle(trim($data[ReferentielPromoFieldset::LIBELLE]));
        if(isset($data[ReferentielPromoFieldset::CODE_PROMO])) $referentiel->setCodePromo(trim($data[ReferentielPromoFieldset::CODE_PROMO]));
        if(isset($data[ReferentielPromoFieldset::SOURCE])){
            $sourceId = intval($data[ReferentielPromoFieldset::SOURCE]);
            /** @var Source $source */
            $source = $this->getObjectManager()->getRepository(Source::class)->find($sourceId);
            if($source){$referentiel->setSource($source);}
        }
        if(isset($data[ReferentielPromoFieldset::ORDRE])) $referentiel->setOrdre(trim($data[ReferentielPromoFieldset::ORDRE]));

        return $referentiel;
    }
}