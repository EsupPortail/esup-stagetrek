<?php

namespace API\Controller;

use API\Service\Traits\VilleApiServiceAwareTrait;
use Application\Controller\Misc\Interfaces\AbstractActionController;
use Laminas\View\Model\JsonModel;

//Action de recherche des villes
class LocalisationController extends AbstractActionController
{
    use VilleApiServiceAwareTrait;

    const RECHERCHER_VILLE_ROUTE = 'api/ville';
    const RECHERCHER_VILLE_ACTION = 'get-ville';
    /**
     * Récupération des villes
     */
    public function getVilleAction(): JsonModel
    {
        $result = null;


        if (($term = $this->params()->fromQuery('term'))) {
            $villes = is_numeric($term[0])
                ? $this->getVilleApiService()->findByCodePostal($term, ['nom', 'code', 'codeDepartement', 'codesPostaux'])
                : $this->getVilleApiService()->findByName($term, ['nom', 'code', 'codeDepartement', 'codesPostaux']);

            foreach ($villes as $v) {
                $result[] = [
                    'id' => $v->code,
                    'label' => $v->nom,
                    'extra' => sprintf('(%s)', implode(', ', $v->codesPostaux)),
                    'codepostal' => $v->codesPostaux[0],
                    'codedepartement' => sprintf('%03s', $v->codeDepartement),
                ];
            }
        }

        return new JsonModel($result);
    }
}