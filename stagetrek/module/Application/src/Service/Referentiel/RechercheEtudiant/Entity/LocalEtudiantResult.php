<?php


namespace Application\Service\Referentiel\RechercheEtudiant\Entity;


use Application\Entity\Db\Etudiant;
use Application\Provider\Source\SourceProvider;
use Application\Service\Referentiel\RechercheEtudiant\Interfaces\RechercheEtudiantResultatInterface;
use DateTime;

/**
 * Class LocalEtudiantResult
 * @package Application\Service\Annuaire\ResultEntity
 */
class LocalEtudiantResult implements RechercheEtudiantResultatInterface
{
    /** @var Etudiant|null $etudiant */
    protected ?Etudiant $etudiant = null;

    /**
     * LocalEtudiantResult constructor.
     * @param $etudiant
     */
    public function __construct($etudiant)
    {
        $this->etudiant = $etudiant;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return SourceProvider::STAGETREK;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->etudiant->getId();
    }

    /** @return string */
    public function getUsername(): string
    {
        $user = $this->etudiant->getUser();
        return (isset($user)) ? $user->getUsername() : "";
    }

    /** @return string */
    public function getDisplayName(): string
    {
        return $this->etudiant->getDisplayName();
    }

    /** @return string */
    public function getMail(): string
    {
        return $this->etudiant->getEmail();
    }

    /**
     * @return string
     */
    public function getNumEtu(): string
    {
        return $this->etudiant->getNumEtu();
    }

    /** @return string */
    public function getLastName(): string
    {
        return $this->etudiant->getNom();
    }

    /** @return string */
    public function getFirstName(): string
    {
        return $this->etudiant->getPrenom();
    }

    public function getDateNaissance(): ?DateTime
    {
        return $this->etudiant->getDateNaissance();
    }
}
