<?php

namespace API\Controller;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Service\Referentiel\RechercheEtudiant\RechercheEtudiantLdapService;
use Exception;
use Laminas\Http\Response;
use Laminas\View\Model\JsonModel;

//Action de recherche des villes
class ReferentielEtudiantController extends AbstractActionController
{

    const GET_ETUDIANTS_ROUTE = 'api/etudiants';
    const GET_ETUDIANTS_ACTION = 'get-etudiants';
    //Route permettant de rechercher des étudiants par code de promo dans un annuaire LDAP
    //retourne le résultat en format JSON
    //
    public function getEtudiantsAction(): JsonModel
    {
        if($this->getLdapService()==null){
            $this->response->setStatusCode(Response::STATUS_CODE_503);
            return new JsonModel(array(
                'error' => Response::STATUS_CODE_503,
                'information' => "Le service n'est pas disponible",
            ));
        }
        if(false && isset($this->urlToken)){
            $request = $this->getRequest();
            $token = $request->getHeader('Authorization');
            if($token != $this->urlToken){
                $this->response->setStatusCode(Response::STATUS_CODE_403);
                return new JsonModel(array(
                    'error' => Response::STATUS_CODE_403,
                    'information' => "Accés non autorisée",
                ));
            }
        }
        error_reporting(E_ERROR);
        try {
            $result = null;

            if (($codePromo = $this->params()->fromQuery('code'))) {
                $annee = $this->params()->fromQuery('annee');
                if(!isset($annee)){
                    $annee = date('Y');
                }
                $etudiants = $this->getLdapService()->findEtudiantsByPromo($codePromo);
                foreach ($etudiants as $e) {
                    $result[] = [
                        "numEtu" =>$e->getNumEtu(),
                        "nom" =>$e->getLastName(),
                       'prenom' =>$e->getFirstName(),
                        'email' =>$e->getMail(),
                        'dateNaissance' =>$e->getDateNaissance(),
                        'codeVet' =>$codePromo,
                        'annee' => $annee,
                    ];
                }
            }
            return new JsonModel($result);
        }
        catch (Exception $e){
            $this->response->setStatusCode(520);
            return new JsonModel(array(
                'error' => 520,
                'information' => $e->getMessage(),
            ));
        }
    }

    protected ?RechercheEtudiantLdapService $ldapService = null;

    public function getLdapService(): RechercheEtudiantLdapService|null
    {
        return $this->ldapService;
    }

    public function setLdapService(RechercheEtudiantLdapService $ldapService): static
    {
        $this->ldapService = $ldapService;
        return $this;
    }



    protected ?string $urlToken = null;
    public function setUrlToken(string $urlToken) : static
    {
        $this->urlToken = $urlToken;
        return $this;
    }

    protected array $dataConfig = [];

    public function setDataConfig(array $dataConfig) : static
    {
        $this->dataConfig = $dataConfig;
        return $this;
    }
    public function getDataConfig() : array
    {
        return  $this->dataConfig;
    }


}