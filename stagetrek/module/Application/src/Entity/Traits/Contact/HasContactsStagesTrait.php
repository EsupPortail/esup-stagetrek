<?php

namespace Application\Entity\Traits\Contact;

use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Stage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait HasContactsStagesTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $contactsStages;

    /**
     * @return void
     */
    protected function initContactsStagesCollection(): void
    {
        $this->contactsStages = new ArrayCollection();
    }

    /**
     * @param ContactStage $contactsStages
     */
    public function addContactStage(ContactStage $contactsStages) : static
    {
        if(!$this->contactsStages->contains($contactsStages)){
            $this->contactsStages->add($contactsStages);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param ContactStage $contactsStages
     */
    public function removeContactStage(ContactStage $contactsStages) : static
    {
        $this->contactsStages->removeElement($contactsStages);
        return $this;
    }
    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContactsStages() : Collection
    {
        return $this->contactsStages;
    }

    public function getContactForStage(Stage $stage) : ?ContactStage
    {
        /** @var \Application\Entity\Db\ContactStage $cs */
        foreach ($this->getContactsStages() as $cs){
            if($cs->getStage()->getId() == $stage->getId()){
                return $cs;
            }
        }
        return null;
    }


    /**
     * TODO : a revoir
     * @param string $code
     * @return ContactStage|null
     */
    public function getContactStageWithCode(string $code): ?ContactStage
    {
        /** @var ContactStage $contact */
        foreach ($this->contactsStages as $contact) {
            if ($contact->getCode() == $code) {
                return $contact;
            }
        }
        return null;
    }

    /**
 * Get contactsStages.
 *
 * @return ContactStage[]
 */
    public function getContactsStagesVisibleParEtudiants($onlyActif = false)
    {
        $res = $this->contactsStages->toArray();
        $res = array_filter($res, function (ContactStage $contact) use ($onlyActif) {
            if ($onlyActif && !$contact->isActif()) {
                return false;
            }
            return $contact->isVisibleParEtudiant();
        });
        return $res;
    }

    /**
     * Get contactsStages.
     *
     * @return ContactStage[]
     */
    public function getResponsablesStages($onlyVisible = false, $onlyActif = false)
    {
        $res = $this->contactsStages->toArray();
        $res = array_filter($res, function (ContactStage $contact) use ($onlyVisible, $onlyActif) {
            if (!$contact->getIsResponsableStage()) {
                return false;
            }
            if ($onlyActif && !$contact->isActif()) {
                return false;
            }
            if ($onlyVisible && !$contact->isVisibleParEtudiant()) {
                return false;
            }
            return true;
        });
        return $res;
    }

}