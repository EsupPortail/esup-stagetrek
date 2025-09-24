<?php


namespace Application\Service\Referentiel\Entity;


use Application\Entity\Db\Source;
use Application\Service\Referentiel\Interfaces\ReferentielEtudiantInterface;
use DateTime;
use UnicaenLdap\Entity\People;

/**
 * Class LdapEtudiantResult
 * @package Application\Service\Annuaire\ResultEntity
 */
class LdapEtudiantResult implements ReferentielEtudiantInterface
{
    /** @var People|null $ldapEntity */
    protected ?People $ldapEntity = null;

    public function __construct(People $ldapEntity)
    {
        $this->ldapEntity = $ldapEntity;
    }

    public function getSource(): string
    {
        return Source::LDAP;
    }

    /**
     * @throws \UnicaenLdap\Exception
     * @throws \Laminas\Ldap\Exception\LdapException
     */
    public function getId(): string
    {
        return $this->ldapEntity->getId();
    }

    /** @return string
     * @throws \Laminas\Ldap\Exception\LdapException
     */
    public function getUsername(): string
    {
        return $this->ldapEntity->get('supannaliaslogin');
    }

    /** @return string
     * @throws \Laminas\Ldap\Exception\LdapException
     */
    public function getDisplayName(): string
    {
        return $this->ldapEntity->get('displayname');
    }

    /** @return string
     * @throws \Laminas\Ldap\Exception\LdapException
     */
    public function getMail(): string
    {
        return $this->ldapEntity->get('mail');
    }

    /**
     * @return string
     * @throws \Laminas\Ldap\Exception\LdapException
     */
    public function getNumEtu(): string
    {
        return $this->ldapEntity->get('supannetuid');
    }

    /** @return string
     * @throws \Laminas\Ldap\Exception\LdapException
     */
    public function getLastName(): string
    {
        //!!! sn peut être soit le nom soit un tableau [nom d'usage]/[nom de famille]
        // 0 : Nom d'usage
        // 1 : Nom de famille
        $sn = $this->ldapEntity->get('sn');
        $name = $sn;
        if (is_array($sn)) $name = $sn[0];
        return $name;
    }

    /** @return string
     * @throws \Laminas\Ldap\Exception\LdapException
     */
    public function getFirstName(): string
    {
        return $this->ldapEntity->get('givenname');
    }

    /**
     * @return DateTime|null
     * @throws \Laminas\Ldap\Exception\LdapException
     */
    public function getDateNaissance(): ?DateTime
    {
        $dateStr =  $this->ldapEntity->get('dateDeNaissance');
        $date = DateTime::createFromFormat('Ymd', $dateStr);
        $dateStr =  $this->ldapEntity->get('dateDeNaissance');
        if(!$date || $date->format('Ymd') != $dateStr){
            return null;
//            Throw new RuntimeException("Le formatage du champ dateDeNaissance de l'annuaire LDAP n'est pas YYYYMMDD.");
        }
        $date->setTime(0,0);
        return $date;
    }
}

