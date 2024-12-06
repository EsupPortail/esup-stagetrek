<?php

namespace API\Controller;

use API\Service\Traits\VilleApiServiceAwareTrait;
use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Service\Referentiel\RechercheEtudiant\RechercheEtudiantLdapService;
use Exception;
use Laminas\Http\Response;
use Laminas\View\Model\JsonModel;
use Throwable;

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
        if(isset($this->urlToken)){
            $token = $this->params()->fromRoute('token', '');
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

            $dataConfig = $this->getDataConfig();
            $keyNumEtu = ($dataConfig['num_etu']) ?? "num_etu";
            $keyNom = ($dataConfig['nom']) ?? "nom";
            $keyPrenom = ($dataConfig['prenom']) ?? "prenom";
            $keyEmail = ($dataConfig['email']) ?? "email";
            $keyDateNaissance = ($dataConfig['date_naissance']) ?? "date_naissance";
            $result = null;

            if (($codePromo = $this->params()->fromQuery('code'))) {
                $etudiants = $this->getLdapService()->findEtudiantsByPromo($codePromo);
                foreach ($etudiants as $e) {
                    $result[] = [
                    $keyNumEtu =>$e->getNumEtu(),
                    $keyNom =>$e->getLastName(),
                        $keyPrenom =>$e->getFirstName(),
                        $keyEmail =>$e->getMail(),
                        $keyDateNaissance =>$e->getDateNaissance(),
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