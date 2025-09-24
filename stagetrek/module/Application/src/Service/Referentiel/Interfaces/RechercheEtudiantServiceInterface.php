<?php

namespace Application\Service\Referentiel\Interfaces;


interface RechercheEtudiantServiceInterface extends ReferentielEtudiantServiceInterface
{
    /**
     * @return ReferentielEtudiantInterface[]
     */
    public function findEtudiantsByName(string $name, int $limit=-1) : array;

    /**
     * @param int|string $numero
     * @param int $limit
     * @return ReferentielEtudiantInterface[]
     */

    public function findEtudiantsByNumero(int|string $numero, int $limit=-1) : array;

    /**
     * @param int $limit
     * @return ReferentielEtudiantInterface[]
     * @var string $codePromo
     */
    public function findEtudiantsByPromo(string $codePromo, string $codeAnnee, int $limit=-1) : array;

    /**
     * @param int $limit
     * @return ReferentielEtudiantInterface|null
     * @var string $codePromo
     */
    public function findEtudiantByMail(string $email) : ?ReferentielEtudiantInterface;
} 