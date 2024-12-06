<?php

//TODO : a revoir ici, pour transformer en se basant sur un module référentiel plus adapté (cf LDAP Adaptater)
namespace Application\Service\Referentiel\RechercheEtudiant;

use Application\Service\Referentiel\RechercheEtudiant\Entity\LdapEtudiantResult;
use Application\Service\Referentiel\RechercheEtudiant\Interfaces\RechercheEtudiantResultatInterface;
use Application\Service\Referentiel\RechercheEtudiant\Interfaces\RechercheEtudiantServiceInterface;
use Laminas\Ldap\Filter\AbstractFilter;
use UnicaenLdap\Exception;
use UnicaenLdap\Filter\People as PeopleFilter;
use UnicaenLdap\Service\LdapPeopleServiceAwareTrait;
use UnicaenUtilisateur\Exception\RuntimeException;

class RechercheEtudiantLdapService implements RechercheEtudiantServiceInterface
{
    use LdapPeopleServiceAwareTrait;

    /**
     * @param string $name
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */
    public function findEtudiantsByName(string $name, int $limit=-1) : array
    {
        $name=trim(strtolower($name));
//        TODO : mettre en config
//        Filtre sur le fait que ce soit des étudiants
        $studentfilter=PeopleFilter::andFilter(
            PeopleFilter::string('edupersonaffiliation=member'),
            PeopleFilter::string('edupersonaffiliation=student')
        );
//        Nom en premier
        $nameFilter = PeopleFilter::orFilter(
            PeopleFilter::username($name),
            PeopleFilter::nameContains($name)
        );
        $filter = $studentfilter->addAnd($nameFilter);
        return $this->findEtudiants($filter, $limit);
    }


    /**
     * @param int|string $numero
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */

    public function findEtudiantsByNumero(int|string $numero, int $limit=-1) : array
    {
        $numero = intval($numero);
        $studentfilter=PeopleFilter::andFilter(
            PeopleFilter::string('edupersonaffiliation=member'),
            PeopleFilter::string('edupersonaffiliation=student')
        );
        $numFilter = PeopleFilter::string('supannetuid='.$numero.'*');
        $filter = $studentfilter->addAnd($numFilter);
        return $this->findEtudiants($filter, $limit);
    }

    /**
     * @param string $codePromo
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */
    public function findEtudiantsByPromo(string $codePromo, int $limit=-1) : array
    {
        $studentfilter=PeopleFilter::andFilter(
            PeopleFilter::string('edupersonaffiliation=member'),
            PeopleFilter::string('edupersonaffiliation=student')
        );
        $promoFilter = PeopleFilter::string('supannetuetape=*'.$codePromo);
        $filter = $studentfilter->addAnd($promoFilter);
        return $this->findEtudiants($filter, -1);
    }

    protected function findEtudiants(string|AbstractFilter $filter, int $limit) : array
    {
        try {
            $ldapPeoples = $this->ldapPeopleService->search($filter, null, $limit);
        } catch (Exception $e) {
            throw new RuntimeException("Un exception ldap est survenue :", $e);
        }
        /** @var RechercheEtudiantResultatInterface[] $results */
        $results = [];
        foreach ($ldapPeoples as $people) {
            $etu = new LdapEtudiantResult($people);
            $results[] = $etu;
        }
        return $results;
    }

}