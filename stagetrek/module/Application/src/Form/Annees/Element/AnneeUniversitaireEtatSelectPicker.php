<?php


namespace Application\Form\Annees\Element;

use Application\Form\Misc\Element\EtatTypeSelectPicker;
use Application\Provider\EtatType\AnneeEtatTypeProvider;
use UnicaenEtat\Entity\Db\EtatCategorie;
use UnicaenEtat\Entity\Db\EtatType;

class AnneeUniversitaireEtatSelectPicker extends EtatTypeSelectPicker
{

    public function setDefaultData() : static
    {
        $categorieEtat = $this->getObjectManager()->getRepository(EtatCategorie::class)->findOneBy(["code" => AnneeEtatTypeProvider::CODE_CATEGORIE]);
        $etatsTypes =  $this->getObjectManager()->getRepository(EtatType::class)->findBy(["categorie" => $categorieEtat], ["ordre" => "asc", 'id'=>'asc']);
        $this->setEtatsTypes($etatsTypes);
        return $this;
    }
}