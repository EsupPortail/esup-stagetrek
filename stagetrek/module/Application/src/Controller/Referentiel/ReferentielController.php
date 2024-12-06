<?php


namespace Application\Controller\Referentiel;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Service\Referentiel\Traits\ReferentielServiceAwareTrait;
use Laminas\View\Model\JsonModel;

/**
 * @package Application\Controller
 */
class ReferentielController extends AbstractActionController
{
    use ReferentielServiceAwareTrait;

    const ROUTE_RECHERCHER_ETUDIANT = 'referentiel/rechercher/etudiant';
    const ACTION_RECHERCHER_ETUDIANT = "rechercher-etudiant";

    /**
     * @return \Laminas\View\Model\JsonModel
     */
    public function rechercherEtudiantAction(): JsonModel
    {
        $term = $this->params()->fromQuery('term');
        if (!$term || $term == '') return new JsonModel([]);
        $source = $this->params()->fromRoute('source');
        $res=[];
        $etudiantsResults=[];
        if(isset($source)){
            $sourceService = $this->getReferentielService()->getReferentielSourceService($source);
            if(isset($sourceService)){
                $etudiantsResults = $sourceService->findEtudiantsByName($term);
            }
        }
        else{
            $etudiantsResults = $this->getReferentielService()->findEtudiantByName($term);
        }
        foreach ($etudiantsResults as $etudiant) {
            $res[] = array(
                'id' => $etudiant->getId(),
                'label' => $etudiant->getDisplayName(),
                'extra' => sprintf("<span class='badge badge-%s' >%s</span>",$etudiant->getSource(), $etudiant->getMail()),
                'userName' => $etudiant->getUsername(),
                'displayName' => $etudiant->getDisplayName(),
                'email' => $etudiant->getMail(),
                "numEtu" => $etudiant->getNumEtu(),
                'nom' => $etudiant->getLastName(),
                'prenom' => $etudiant->getFirstName(),
                'dateNaissance' => $etudiant->getDateNaissance()->format('d/m/Y')
            );
        }
        return new JsonModel($res);
    }
}