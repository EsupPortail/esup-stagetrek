<?php


namespace Application\Form\Etudiant\Element;

use Application\Form\Misc\Element\EtatTypeSelectPicker;
use Application\Provider\EtatType\EtudiantEtatTypeProvider;
use UnicaenEtat\Entity\Db\EtatCategorie;
use UnicaenEtat\Entity\Db\EtatType;

class EtudiantEtatSelectPicker extends EtatTypeSelectPicker
{

    public function setDefaultData() : static
    {
        $categorieEtat = $this->getObjectManager()->getRepository(EtatCategorie::class)->findOneBy(["code" => EtudiantEtatTypeProvider::CODE_CATEGORIE]);
        $etatsTypes =  $this->getObjectManager()->getRepository(EtatType::class)->findBy(["categorie" => $categorieEtat], ["ordre" => "asc", 'id'=>'asc']);
        $this->setEtatsTypes($etatsTypes);
        return $this;
    }
}