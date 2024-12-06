<?php

namespace Application\Service\Referentiel;

use Application\Entity\Db\ReferentielPromo;
use Application\Service\Referentiel\RechercheEtudiant\Interfaces\RechercheEtudiantResultatInterface;
use Application\Service\Referentiel\RechercheEtudiant\Interfaces\RechercheEtudiantServiceInterface;
use Exception;

class ReferentielService
{
    /*******************
     * RechercheEtudiants
     ********************/
    protected int $limit = 10;

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @var RechercheEtudiantServiceInterface[] $referentielsSourceServices
     */
    protected array $referentielsSourceServices = [];

    public function getReferentielSourcesServices(): array
    {
        return $this->referentielsSourceServices;
    }

    public function setReferentielsSourcesServices(array $referentielsSourceServices): void
    {
        $this->referentielsSourceServices = $referentielsSourceServices;
    }

    public function addReferentielSourceService(string $sourceCode, RechercheEtudiantServiceInterface $referentielsSourceServices): void
    {
        $this->referentielsSourceServices[$sourceCode] = $referentielsSourceServices;
    }

    public function getReferentielSourceService(string $sourceCode): ?RechercheEtudiantServiceInterface
    {
        return ($this->referentielsSourceServices[$sourceCode]) ?? null;
    }

    /**
     * @param string $term
     * @return RechercheEtudiantResultatInterface[]
     */
    public function findEtudiantByName(string $term) : array
    {
        $etudiantsResults=[];
        foreach ($this->getReferentielSourcesServices() as $service) {
            $sourceEtudiantsResults = $service->findEtudiantsByName($term, $this->limit);
            foreach ($sourceEtudiantsResults as $e){
                $etudiantsResults[$e->getNumEtu()] = $e;
            }
        }
        usort($etudiantsResults, function (RechercheEtudiantResultatInterface $e1, RechercheEtudiantResultatInterface $e2) {
            return strcmp($e1->getDisplayName(), $e2->getDisplayName());
        });
        return array_slice($etudiantsResults, 0, $this->limit);
    }

    /**
     * @param string|int $numeroEtu
     * @return RechercheEtudiantResultatInterface[]
     */
    public function findEtudiantByNumero(string|int $numeroEtu) : array
    {
        foreach ($this->getReferentielSourcesServices() as $service) {
            $sourceEtudiantsResults = $service->findEtudiantsByNumero($numeroEtu, $this->limit);
            foreach ($sourceEtudiantsResults as $e){
                $etudiantsResults[$e->getNumEtu()] = $e;
            }
        }
        usort($etudiantsResults, function (RechercheEtudiantResultatInterface $e1, RechercheEtudiantResultatInterface $e2) {
            return strcmp($e1->getDisplayName(), $e2->getDisplayName());
        });
        return array_slice($etudiantsResults, 0, $this->limit);
    }

    /**
     * TODO : a revoir pour soit faire depuis toutes les sources ou en spécifiant laquel
     * @param ReferentielPromo $referentielPromo ;
     * @return RechercheEtudiantResultatInterface[]
     * @throws \Exception
     */
    public function findEtudiantByPromo(ReferentielPromo $referentielPromo) : array
    {
        /** @var RechercheEtudiantServiceInterface $service */
        $service = $this->getReferentielSourceService($referentielPromo->getSource()->getCode());
        if($service==null){
            throw new Exception(sprintf("Le service de recherche d'étudiant pour la source %s n'est pas correctement configuré",$referentielPromo->getCode()));
        }
        $sourceEtudiantsResults = $service->findEtudiantsByPromo($referentielPromo->getCodePromo());
        $etudiantsResults=[];
        foreach ($sourceEtudiantsResults as $e){
            $etudiantsResults[$e->getNumEtu()] = $e;
        }
        usort($etudiantsResults, function (RechercheEtudiantResultatInterface $e1, RechercheEtudiantResultatInterface $e2) {
            return strcmp($e1->getDisplayName(), $e2->getDisplayName());
        });
        return $etudiantsResults;
    }
}