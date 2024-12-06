<?php

namespace Application\Service\Preference;

use Application\Entity\Db\Preference;
use Application\Entity\Db\Stage;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Doctrine\ORM\OptimisticLockException;
use Exception;

/**
 * Class PreferenceService
 * @package Application\Service\Preference
 */
class PreferenceService extends CommonEntityService
{
    use SessionStageServiceAwareTrait;
    use AffectationStageServiceAwareTrait;

    /** @return string */
    public function getEntityClass(): string
    {
        return Preference::class;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function add(mixed $entity, ?string $serviceEntityClass = null) : mixed
    {
        $preference = $entity;
        $stage = $preference->getStage();

        //Recalcul des rangs
        $unitOfWork = $this->getObjectManager()->getUnitOfWork();
        $unitOfWork->computeChangeSets();
        $changeData = $unitOfWork->getEntityChangeSet($preference);
        //Recalcul des rang en provoquant éventuellement un décallage
        $newRang = $preference->getRang();
        $preferences = $stage->getPreferences()->toArray();
        $newRang = min($newRang, sizeof($preferences)+1);
        $newRang = max($newRang, 1);
        $preference->setRang($newRang);
        $preferences = Preference::sortPreferences($preferences);
        foreach ($preferences as $pref) {
            if($preference->getId() == $pref->getId()){continue;}
            $r = $pref->getRang();
            if($newRang <= $r){$r++;}
            $pref->setRang($r);
        }
        $this->getObjectManager()->persist($preference);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($preferences);
            $session = $stage->getSessionStage();
            $this->getSessionStageService()->recomputeDemandeTerrains($session);
            $this->updatePreferenceSatisfaction($stage);
        }
        return $preference;
    }

    /**
     * Ajoute/Met à jour une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        /** @var Preference $preference */
        $preference = $entity;
        $stage = $preference->getStage();
        //Recalcul des rangs
        $unitOfWork = $this->getObjectManager()->getUnitOfWork();
        $unitOfWork->computeChangeSets();
        $changeData = $unitOfWork->getEntityChangeSet($preference);
        //Recalcul des rang en provoquant éventuellement un décallage
        $newRang = $preference->getRang();
        $oldRang = ($changeData != null &&
            isset($changeData['rang'])
        ) ? $changeData['rang'][0] : $preference->getRang();
        $preferences = $stage->getPreferences()->toArray();
        $newRang = min($newRang, sizeof($preferences));
        $newRang = max($newRang, 1);
        $preference->setRang($newRang);
        $preferences = Preference::sortPreferences($preferences);
        foreach ($preferences as $pref) {
            if($preference->getId() == $pref->getId()){continue;}
            $r = $pref->getRang();
            if($oldRang <= $r && $r <= $newRang){$r--;}
            if($newRang <= $r && $r <= $oldRang){$r++;}
            $pref->setRang($r);
        }
        //On retire le tableaux selon les nouveaux ordres et on redéfinie les ordres en partant de 1
        //Celà évite s'il y a un trou dans les préférences suite un problème de conserver se troue
        $preferences = Preference::sortPreferences($preferences);
        $r=1;
        foreach ($preferences as $pref) {
            $pref->setRang($r++);
        }
        $this->getObjectManager()->flush($preferences);
        $this->updatePreferenceSatisfaction($stage);
        $session = $stage->getSessionStage();
        $this->getSessionStageService()->recomputeDemandeTerrains($session);
        return $entity;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     * @throws OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {
        /** @var Preference $preference */
        $preference = $entity;
        $stage = $preference->getStage();
        $this->getObjectManager()->remove($preference);

        $preferences = $stage->getPreferences()->toArray();
        $preferences = array_filter($preferences, function (Preference $pref) use($preference){
            return $pref->getId() != $preference->getId();
        });
        $preferences = Preference::sortPreferences($preferences);
        $r=1;
        foreach ($preferences as $pref) {
            $pref->setRang($r++);
        }
        $this->getObjectManager()->flush($preferences);
        if ($this->hasUnitOfWorksChange()) {
            $session = $stage->getSessionStage();
            $this->getSessionStageService()->recomputeDemandeTerrains($session);
            $this->updatePreferenceSatisfaction($stage);
        }
        return $this;
    }
    /**
     * @throws Exception
     */
    public function updatePreferenceSatisfaction(Stage $stage) : static
    {
        $affectation = $stage->getAffectationStage();
        $terrainA = ($affectation) ? $affectation->getTerrainStage() : null;
        $preferences = $stage->getPreferences();
        foreach ($preferences as $preference) {
            $terrainP = $preference->getTerrainStage();
            $isSat = (isset($affectation) &&
                isset($terrainA) && isset($terrainP) && $terrainA->getId() == $terrainP->getId()
            );
            $preference->setSat($isSat);
            $this->getObjectManager()->flush($preference);
            if(isset($isSat)){
                $affectation->setRangPreference($preference->getRang());
                $this->getObjectManager()->flush($affectation);
            }
        }
        return $this;
    }

    /**
     * @desc : creer une nouvelle préférence pour le stage sans la sauvegrader en bdd ni lui donner de terrain
     * @throws Exception
     */
    public function getNewPrefence(Stage $stage) : Preference
    {
        //Pour être sur de travailler sur le bon stage
        $this->getObjectManager()->refresh($stage);
        $preference = new Preference();
        $preference->setStage($stage);
        $rang = $stage->getPreferences()->count() + 1;
        $preference->setRang($rang);
        return $preference;
    }

}