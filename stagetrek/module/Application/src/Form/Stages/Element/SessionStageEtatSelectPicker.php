<?php


namespace Application\Form\Stages\Element;

use Application\Form\Misc\Element\EtatTypeSelectPicker;
use Application\Provider\EtatType\SessionEtatTypeProvider;
use UnicaenEtat\Entity\Db\EtatCategorie;
use UnicaenEtat\Entity\Db\EtatType;

class SessionStageEtatSelectPicker extends EtatTypeSelectPicker
{

    public function setDefaultData() : static
    {
        $categorieEtat = $this->getObjectManager()->getRepository(EtatCategorie::class)->findOneBy(["code" => SessionEtatTypeProvider::CODE_CATEGORIE]);
        $etatsTypes =  $this->getObjectManager()->getRepository(EtatType::class)->findBy(["categorie" => $categorieEtat], ["ordre" => "asc", 'id'=>'asc']);
        $this->setEtatsTypes($etatsTypes);
        return $this;
    }
}