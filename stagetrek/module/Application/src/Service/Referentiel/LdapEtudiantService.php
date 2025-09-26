<?php
//TODO : a revoir ici, pour transformer en se basant sur un module référentiel plus adapté (cf LDAP Adaptater)
namespace Application\Service\Referentiel;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Entity\Hydrator\EtudiantHydrator;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Entity\Traits\Referentiel\HasReferentielPromoTrait;
use Application\Exceptions\ImportException;
use Application\Form\Annees\Traits\AnneeUniversitaireFormAwareTrait;
use Application\Service\Referentiel\Entity\LdapEtudiantResult;
use Application\Service\Referentiel\Interfaces\AbstractImportEtudiantsService;
use Application\Service\Referentiel\Interfaces\ImportEtudiantsServiceInterface;
use Application\Service\Referentiel\Interfaces\RechercheEtudiantServiceInterface;
use Application\Service\Referentiel\Interfaces\ReferentielEtudiantInterface;
use Application\Service\Referentiel\Traits\ReferentielPromoServiceAwareTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Ldap\Filter\AbstractFilter;
use UnicaenApp\View\Helper\Messenger;
use UnicaenLdap\Exception;
use UnicaenLdap\Filter\People as PeopleFilter;
use UnicaenLdap\Service\LdapPeopleServiceAwareTrait;
use UnicaenUtilisateur\Exception\RuntimeException;

class LdapEtudiantService extends AbstractImportEtudiantsService implements RechercheEtudiantServiceInterface, ImportEtudiantsServiceInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    protected EtudiantHydrator|null $etudiantHydrator = null;

    use LdapPeopleServiceAwareTrait;

    public function getKey(): string
    {
        return Source::LDAP;
    }

    public function getEtudiantHydrator(): ?EtudiantHydrator
    {
        return $this->etudiantHydrator;
    }

    public function setEtudiantHydrator(?EtudiantHydrator $etudiantHydrator): static
    {
        $this->etudiantHydrator = $etudiantHydrator;
        return $this;
    }

    /**
     * @param string $name
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */
    public function findEtudiantsByName(string $name, int $limit = -1): array
    {
        $name = trim(strtolower($name));
//        TODO : mettre en config
//        Filtre sur le fait que ce soit des étudiants
        $studentfilter = PeopleFilter::andFilter(PeopleFilter::string('edupersonaffiliation=member'), PeopleFilter::string('edupersonaffiliation=student'));
//        Nom en premier
        $nameFilter = PeopleFilter::orFilter(PeopleFilter::username($name), PeopleFilter::nameContains($name));
        $filter = $studentfilter->addAnd($nameFilter);
        return $this->findEtudiants($filter, $limit);
    }

    protected function findEtudiants(string|AbstractFilter $filter, int $limit): array
    {
        try {
            $ldapPeoples = $this->ldapPeopleService->search($filter, null, $limit);
        } catch (Exception $e) {
            throw new RuntimeException("Un exception ldap est survenue :", $e);
        }
        /** @var ReferentielEtudiantInterface[] $results */
        $results = [];
        foreach ($ldapPeoples as $people) {
            $etu = new LdapEtudiantResult($people);
            $results[] = $etu;
        }
        return $results;
    }

    /**
     * @param int|string $numero
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */
    public function findEtudiantsByNumero(int|string $numero, int $limit = -1): array
    {
        $numero = intval($numero);
        $studentfilter = PeopleFilter::andFilter(PeopleFilter::string('edupersonaffiliation=member'), PeopleFilter::string('edupersonaffiliation=student'));
        $numFilter = PeopleFilter::string('supannetuid=' . $numero . '*');
        $filter = $studentfilter->addAnd($numFilter);
        return $this->findEtudiants($filter, $limit);
    }

    /**
     * @param string $codePromo
     * @param int $limit
     * @return ReferentielEtudiantInterface|null
     */
    public function findEtudiantByMail(string $email): ?ReferentielEtudiantInterface
    {
        $studentfilter = PeopleFilter::andFilter(PeopleFilter::string('edupersonaffiliation=member'), PeopleFilter::string('edupersonaffiliation=student'));
        $promoFilter = PeopleFilter::string('mail=' . $email);
        $filter = $studentfilter->addAnd($promoFilter);
        $etudiants = $this->findEtudiants($filter, -1);
        if (sizeof($etudiants) > 1) {
            throw new Exception("Plusieurs étudiants correspondent a cette adresse mail");
        }
        $etudiant = current($etudiants);
        return ($etudiant instanceof ReferentielEtudiantInterface) ? $etudiant : null;
    }

    /**
     * @param string $codePromo
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */
    public function findEtudiantsByPromo(string $codePromo, string $codeAnnee, int $limit = -1): array
    {
        $memberFiltter = PeopleFilter::string('edupersonaffiliation=member');
        $studentfilter = PeopleFilter::string('edupersonaffiliation=student');
        $promoFilter = PeopleFilter::string(sprintf('supannetuetape=*%s', $codePromo));
        $anneeFilter = PeopleFilter::string(sprintf('supannEtuAnneeInscription=%s', $codeAnnee));
        $filter = PeopleFilter::andFilter($memberFiltter, $studentfilter, $promoFilter,
            $anneeFilter
        );
        return $this->findEtudiants($filter, -1);
    }

    use HasReferentielPromoTrait;
    use HasAnneeUniversitaireTrait;
    /**
     * @throws \Application\Exceptions\ImportException
     */
    protected function assertImportData(array $data) : bool
    {
        $referentiel = ($data['referentiel']) ?? null;
        $annee = ($data['annee']) ?? null;
        if (!isset($referentiel) || !$referentiel instanceof ReferentielPromo) {
            throw new ImportException("Le référentiel de données fournis n'est pas valide");
        }
        if (!isset($annee) || !$annee instanceof AnneeUniversitaire) {
            throw new ImportException("L'année fournise n'est pas valide");
        }
        $this->setReferentielPromo($referentiel);
        $this->setAnneeUniversitaire($annee);
        /** @var Groupe[] $groupes */
        $groupes = $referentiel->getGroupes()->toArray();
        $groupes = array_filter($groupes, function (Groupe $groupe) use ($annee) {
            return $groupe->getAnneeUniversitaire()->getCode() == $annee->getCode();
        });
        $this->setGroupes($groupes);
        return true;
    }

    protected function importerEtudiants() : array
    {
        $referentiel = $this->getReferentielPromo();
        $annee = $this->getAnneeUniversitaire();

        $etudiants = [];
        try {
            $cptAdd = 0;
            $cptEdit = 0;
            $codePromo = $referentiel->getCodePromo();
            $codeAnnee = $annee->getCode();
            $ldapEtudiants = $this->findEtudiantsByPromo($codePromo, $codeAnnee);
            if(empty($ldapEtudiants)){
                $this->addLog("Aucun étudiant n'as été trouvé dans l'annuaire LDAP pour l'année demandée");
                $this->setLogType(Messenger::WARNING);
                return [];
            }
            foreach ($ldapEtudiants as $ldapEtu) {
                $etudiant = $this->getEtudiantService()->findOneBy(['numEtu' => $ldapEtu->getNumEtu()]);
                if (!isset($etudiant)) {
                    $etudiant = new Etudiant();
                }
                $etuData = $this->etudiantHydrator->extract($ldapEtu);
                $etudiant = $this->etudiantHydrator->hydrate($etuData, $etudiant);
                if ($etudiant->getId() == null) {
                    $cptAdd++;
                    $this->getObjectManager()->persist($etudiant);
                }
                else{
                    $cptEdit++;
                }
                $etudiants[$etudiant->getNumEtu()] = $etudiant;
            }
            $this->getObjectManager()->flush($etudiants);
            if($cptAdd >0){$this->addLog(sprintf("%s étudiants ajoutés", $cptAdd));}
            if($cptEdit >0){$this->addLog(sprintf("%s étudiants mis à jour", $cptEdit));}
            try{
                $this->getEtudiantService()->updateEtats($etudiants);
            }
            catch (Exception $e){
                $this->addLog("Erreur du calcul des états");
                throw $e;
            }
            $this->setLogType(Messenger::SUCCESS);
        } catch (Exception $e) {
            $this->setLogType(Messenger::ERROR);
            $this->addLog($e->getMessage());
            throw new ImportException($e->getMessage(), previous: $e);
        }
        return $etudiants;
    }

    protected function addEtudiantsInGroupes() : static
    {
        $annee = $this->getAnneeUniversitaire();
        $groupes = $this->getGroupes();
        $etudiants = $this->getEtudiants()->toArray();
        if(!isset($groupes) || $groupes->isEmpty()){
            $this->addLog(sprintf("Aucun groupe n'est associé au référentiel pour l'année %s", $annee->getLibelle()));
            return $this;
        }
        try {
            foreach ($groupes as $groupe) {
                $etudiantsCanBeAdd = $this->getGroupeService()->findEtudiantsCanBeAddInGroupe($groupe, $etudiants);
                if(!empty($etudiantsCanBeAdd)) {
                    $this->getGroupeService()->addEtudiants($groupe, $etudiantsCanBeAdd);
                }
                $msg = sprintf("%s étudiants inscrits dans le groupe %s - %s",
                    sizeof($etudiantsCanBeAdd), $groupe->getLibelle(), $groupe->getAnneeUniversitaire()->getLibelle()
                );
                $this->addLog($msg);
                $this->getEtudiantService()->updateEtats($etudiantsCanBeAdd);
            }
        } catch (Exception $e) {
            $this->setLogType(Messenger::ERROR);
            $this->addLog("Erreur lors de l'ajout des étudiants dans les groupes");
            $this->addLog($e->getMessage());
            throw new ImportException($e->getMessage(), previous: $e);
        }
        return $this;
    }


}