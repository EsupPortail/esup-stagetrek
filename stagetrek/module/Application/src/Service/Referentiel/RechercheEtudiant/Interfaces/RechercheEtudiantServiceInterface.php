<?php

namespace Application\Service\Referentiel\RechercheEtudiant\Interfaces;


interface RechercheEtudiantServiceInterface
{
    /**
     * @return RechercheEtudiantResultatInterface[]
     */
    public function findEtudiantsByName(string $name, int $limit=-1) : array;

    /**
     * @param int|string $numero
     * @param int $limit
     * @return RechercheEtudiantResultatInterface[]
     */

    public function findEtudiantsByNumero(int|string $numero, int $limit=-1) : array;

    /**
     * @var string $codePromo
     * @param int $limit
     * @return RechercheEtudiantResultatInterface[]
     */
    public function findEtudiantsByPromo(string $codePromo, int $limit=-1) : array;
} 