<?php

namespace Application\Service\Referentiel;

use Application\Entity\Db\ReferentielPromo;
use Application\Exceptions\ImportException;
use Application\Service\Referentiel\Interfaces\AbstractImportEtudiantsService;
use Application\Service\Referentiel\Interfaces\ImportEtudiantsServiceInterface;
use Application\Service\Referentiel\Interfaces\ReferentielEtudiantInterface;
use Application\Service\Referentiel\Interfaces\RechercheEtudiantServiceInterface;
use Application\Service\Referentiel\Traits\ReferentielsEtudiantsServicesAwareTrait;

/** Permet d'agréger plusieurs référentiels en 1 (fait les actions pour chacun) */
class MultipleReferentielEtudiantsService extends AbstractImportEtudiantsService implements RechercheEtudiantServiceInterface, ImportEtudiantsServiceInterface
{
    public function getKey(): string
    {
        $key = implode("_",$this->getReferentielsEtudiantsServices());
        return (strlen($key)>0) ? $key : "n/a";
    }

    use ReferentielsEtudiantsServicesAwareTrait;

    public function getReferentielSourceService(string $sourceCode): ?RechercheEtudiantServiceInterface
    {
        return ($this->referentielsSourceServices[$sourceCode]) ?? null;
    }

    /**
     * @param string $term
     * @return ReferentielEtudiantInterface[]
     */
    public function findEtudiantsByName(string $name, int $limit=-1) : array
    {
        $etudiantsResults=[];
        foreach ($this->getReferentielsEtudiantsServices() as $service) {
            $sourceEtudiantsResults = $service->findEtudiantsByName($name, $limit);
            foreach ($sourceEtudiantsResults as $e){
                $etudiantsResults[$e->getNumEtu()] = $e;
            }
        }
        usort($etudiantsResults, function (ReferentielEtudiantInterface $e1, ReferentielEtudiantInterface $e2) {
            return strcmp($e1->getDisplayName(), $e2->getDisplayName());
        });
        return array_slice($etudiantsResults, 0, $limit);
    }

    /**
     * @param string|int $numeroEtu
     * @return ReferentielEtudiantInterface[]
     */
    public function findEtudiantsByNumero(string|int $numero, int $limit=-1) : array
    {
        $etudiantsResults=[];
        foreach ($this->getReferentielsEtudiantsServices() as $service) {
            $sourceEtudiantsResults = $service->findEtudiantsByNumero($numero, $limit);
            foreach ($sourceEtudiantsResults as $e){
                $etudiantsResults[$e->getNumEtu()] = $e;
            }
        }
        usort($etudiantsResults, function (ReferentielEtudiantInterface $e1, ReferentielEtudiantInterface $e2) {
            return strcmp($e1->getDisplayName(), $e2->getDisplayName());
        });
        return array_slice($etudiantsResults, 0, $limit);
    }

    /**
     * @param ReferentielPromo $referentielPromo ;
     * @return ReferentielEtudiantInterface[]
     * @throws \Exception
     */
    public function findEtudiantsByPromo(string $codePromo, string $codeAnnee, int $limit = -1) : array
    {
        $etudiantsResults=[];
        foreach ($this->getReferentielsEtudiantsServices() as $service) {
            $sourceEtudiantsResults = $service->findEtudiantByPromo($codePromo, $codeAnnee, $limit);
            foreach ($sourceEtudiantsResults as $e){
                $etudiantsResults[$e->getNumEtu()] = $e;
            }
        }
        usort($etudiantsResults, function (ReferentielEtudiantInterface $e1, ReferentielEtudiantInterface $e2) {
            return strcmp($e1->getDisplayName(), $e2->getDisplayName());
        });
        return $etudiantsResults;
    }

    public function findEtudiantByMail(string $email): ?ReferentielEtudiantInterface
    {
        foreach ($this->getReferentielsEtudiantsServices() as $service) {
            $etudiant = $service->findEtudiantByMail($email);
            if(isset($etudiant)){ return $etudiant; }
        }
        return null;
    }

    protected function assertImportData(array $data): bool
    {
        foreach ($this->getReferentielsEtudiantsServices() as $key => $service) {
            if(!$service instanceof ImportEtudiantsServiceInterface){continue;}
           if(!$service->assertImportData($data)){
               throw new ImportException(sprintf("Impossible d'importer depuis le service %s", $key));
           }
        }
        return true;
    }

    protected function importerEtudiants(): array
    {
        $etudiantsResults=[];
        foreach ($this->getReferentielsEtudiantsServices() as $service) {
            if(!$service instanceof ImportEtudiantsServiceInterface){continue;}
            $sourceResult = $service->importerEtudiants();
            foreach ($sourceResult as $e){
                $etudiantsResults[$e->getNumEtu()] = $e;
            }
        }
        usort($etudiantsResults, function (ReferentielEtudiantInterface $e1, ReferentielEtudiantInterface $e2) {
            return strcmp($e1->getDisplayName(), $e2->getDisplayName());
        });
        return $etudiantsResults;
    }

    protected function addEtudiantsInGroupes(): static
    {
        foreach ($this->getReferentielsEtudiantsServices() as $key => $service) {
            if(!$service instanceof ImportEtudiantsServiceInterface){continue;}
            $service->addEtudiantsInGroupes();
        }
        return $this;
    }
}