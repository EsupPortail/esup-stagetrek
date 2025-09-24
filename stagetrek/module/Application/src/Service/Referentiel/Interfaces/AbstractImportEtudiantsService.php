<?php

namespace Application\Service\Referentiel\Interfaces;


use Application\Entity\Db\Etudiant;
use Application\Entity\Traits\Etudiant\HasEtudiantsTrait;
use Application\Entity\Traits\Groupe\HasGroupesTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use AWS\CRT\Log;
use UnicaenApp\View\Helper\Messenger;

abstract class AbstractImportEtudiantsService implements ImportEtudiantsServiceInterface
{

    use EtudiantServiceAwareTrait;
    use GroupeServiceAwareTrait;

    /**
     * @throws \Application\Exceptions\ImportException
     */
    public function importer(array $data): array
    {
        $this->assertImportData($data);
        $etudiants = $this->importerEtudiants();
        if(empty($etudiants)){ return [];}
        $this->addEtudiantsInGroupes();
        return $etudiants;
    }

    use HasEtudiantsTrait;
    use HasGroupesTrait;


    /**
     * @throws \Application\Exceptions\ImportException
     */
    protected abstract function assertImportData(array $data): bool;

    /**
     * @throws \Application\Exceptions\ImportException
     */
    protected abstract function importerEtudiants(): array;

    /**
     * @throws \Application\Exceptions\ImportException
     */
    protected abstract function addEtudiantsInGroupes(): static;


    /** @var array $logs */
    protected array $logs = [];

    public function addLog(string $log) : static
    {
        $this->logs[] = $log;
        return $this;
    }

   public function renderLogs() : ?string
   {
       if(empty($this->logs)){return null;}
       $res = "<strong>Résultat de l'import :</strong>";
       $res .= "<ul>";
       foreach($this->logs as $log){
           $res .= sprintf("<li>%s</li>", $log);
       }
       $res .= "</ul>";
       return $res;
   }
//   Valeur attendu Messenger::SUCCESS|WARNING ....
    protected ?string $logType = Messenger::INFO;
    public function getLogType(): ?string
    {
        return $this->logType;
    }

    public function setLogType(?string $type): static
    {
        $this->logType = $type;
        return $this;
    }

} 