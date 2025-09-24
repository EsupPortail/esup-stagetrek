<?php

namespace Application\Service\Referentiel\Interfaces;


use Application\Entity\Db\Etudiant;

interface ImportEtudiantsServiceInterface extends ReferentielEtudiantServiceInterface
{
    /**
     * @param array $data
     * @return Etudiant[]
     */
   public function importer(array $data) : array;
   public function addLog(string $log) : static;
   public function renderLogs() : ?string;
   public function setLogType(string $type) : static;
   public function getLogType() : ?string;

} 