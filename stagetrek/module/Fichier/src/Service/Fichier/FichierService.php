<?php

namespace Fichier\Service\Fichier;

use Doctrine\ORM\NonUniqueResultException;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use Fichier\Entity\Db\Fichier;
use Fichier\Entity\Db\Nature;
use Fichier\Filter\FileName\FileNameFormatterInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use UnicaenApp\Exception\RuntimeException;
use UnicaenStorage\Adapter\StorageAdapterInterface;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class FichierService
    implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use UserServiceAwareTrait;

    protected StorageAdapterInterface $storageAdapter;
    public function getStorageAdapter(): StorageAdapterInterface
    {
        return $this->storageAdapter;
    }

    public function setStorageAdapter(StorageAdapterInterface $storageAdapter): void
    {
        $this->storageAdapter = $storageAdapter;
    }
    protected FileNameFormatterInterface $fileNameFormatter;

    public function getFileNameFormatter(): FileNameFormatterInterface
    {
        return $this->fileNameFormatter;
    }

    public function setFileNameFormatter(FileNameFormatterInterface $fileNameProvider): void
    {
        $this->fileNameFormatter = $fileNameProvider;
    }

    public function findAll() : array
    {
        return $this->getObjectManager()->getRepository(Fichier::class)->findAll();
    }
    public function findBy(array $criteria = [], array $order = []) : array
    {
        return $this->getObjectManager()->getRepository(Fichier::class)->findBy($criteria, $order);
    }

    /**
     * @param Fichier $fichier
     * @return Fichier
     */
    public function create(Fichier $fichier, ?bool $addToStorage=true) : Fichier
    {
        try {
            if($addToStorage){
                $tmpName = $fichier->getTmpName();
//                Au cas ou s'il n'y a pas de nom temporaire, on test avec le nom originale du fichier
                if(!isset($tmpName)){$tmpName = $fichier->getNomOriginal(); }
                if(!file_exists($tmpName)){
                    throw new Exception("Le fichier demandé n'as pas été trouvé", 404);
                }
                $fileContent = file_get_contents($tmpName);
                $this->addToStorage($fichier, $fileContent);
            }
            $this->getObjectManager()->persist($fichier);
            $this->getObjectManager()->flush($fichier);
        }
        catch (Exception $e) {
            throw new Exception("Un problème s'est produit lors de la création d'un Fichier. <br/>".$e->getMessage(),0, $e);
        }
        return $fichier;
    }

    /**
     * @param Fichier $fichier
     * @return Fichier
     */
    public function update(Fichier $fichier) : Fichier
    {
        try {
            $this->getObjectManager()->flush($fichier);
        } catch (Exception $e) {
            throw new Exception("Un problème s'est produit lors de la mise à jour d'un Fichier.", $e);
        }
        return $fichier;
    }

    /**
     * @param Fichier $fichier
     * @return Fichier
     */
    public function historise(Fichier $fichier) : Fichier
    {
        $user = $this->getUserService()->getConnectedUser();
        $fichier->historiser($user);

        try {
            $this->getObjectManager()->flush($fichier);
        } catch (Exception $e) {
            throw new Exception("Un problème s'est produit lors de l'historisation d'un Fichier.", $e);
        }
        return $fichier;
    }

    /**
     * @param Fichier $fichier
     * @return Fichier
     */
    public function restore(Fichier $fichier) : Fichier
    {
        $fichier->dehistoriser();
        try {
            $this->getObjectManager()->flush($fichier);
        } catch (Exception $e) {
            throw new Exception("Un problème s'est produit lors de la restauration d'un Fichier.", $e);
        }
        return $fichier;
    }

    /**
     * @param Fichier $fichier
     * @return FichierService
     */
    public function delete(Fichier $fichier, bool $deleteFromStorage=true) : static
    {
        try {
            if($deleteFromStorage){$this->removeFromStorage($fichier);}
            $this->getObjectManager()->remove($fichier);
            $this->getObjectManager()->flush();
        } catch (Exception $e) {
            throw new Exception("Un problème s'est produit lors de la suppression d'un Fichier.", $e);
        }
        return $this;
    }

    /**
     * @param string $id
     * @return Fichier|null
     */
    public function getFichier(string $id) : ?Fichier
    {
        $qb = $this->getObjectManager()->getRepository(Fichier::class)->createQueryBuilder('fichier')
            ->andWhere('fichier.id = :id')
            ->setParameter('id', $id)
        ;
        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new Exception("Plusieurs Fichier partagent le même identifiant [".$id."]", $e);
        }
        return $result;
    }

    /**
     * @param AbstractActionController $controller
     * @param string $paramName
     * @return Fichier|null
     */
    public function getRequestedFichier(AbstractActionController $controller, string $paramName = 'fichier') : ?Fichier
    {
        $id = $controller->params()->fromRoute($paramName);
        return $this->getFichier($id);
    }

    /**
     * Crée un fichier à partir des données d'upload fournies.
     *
     * @param array           $file         Données résultant de l'upload de fichier
     * @param Nature          $nature       Version de fichier
     * @return Fichier|null fichier
     */
    public function createFichierFromUpload(array $file, Nature $nature, ?bool $addToStorage=true) : ?Fichier
    {
        $fichier = null;
        if (isset($file['name'])) {
            $tmpName = $file['tmp_name'];
            $nomFichier = $file['name'];
            $typeFichier = $file['type'];
            $tailleFichier = $file['size'];
            $uid = uniqid();
            $fichier = new Fichier();
            $fichier
                ->setId($uid)
                ->setNature($nature)
                ->setTypeMime($typeFichier)
                ->setNomOriginal($nomFichier)
                ->setTmpName($tmpName)
                ->setTaille($tailleFichier)
            ;
            $nomStockage = $this->fileNameFormatter->getFileName($fichier);
            $fichier->setNomStockage($nomStockage);
            $this->create($fichier);
        }
        return $fichier;
    }

    public function createFichierFromFile(string $fileName, Nature $nature, ?bool $addToStorage=true) : Fichier
    {
        if(!file_exists($fileName)){
            throw new Exception("Le fichier demandé n'as pas été trouvé", 404);
        }
        $uid = uniqid();
        $typeMime =  mime_content_type($fileName);
        $size = filesize($fileName);
        $fichier = new Fichier();
        $fichier
            ->setId($uid)
            ->setNature($nature)
            ->setTypeMime($typeMime)
            ->setNomOriginal($fileName)
            ->setTmpName($fileName)
            ->setTaille($size)
        ;
        $nomStockage = $this->fileNameFormatter->getFileName($fichier);
        $fichier->setNomStockage($nomStockage);
        return $this->create($fichier);
    }
    /** ajoute au storage */
    public function addToStorage(Fichier $fichier, string $fileContent) : static
    {
        $storage = $this->getStorageAdapter();
        if(!isset($storage)){
            throw new RuntimeException("Storage service non défini");
        }
        $fName = $this->fileNameFormatter->getFileName($fichier);
        $dirName = $storage->computeDirectoryPath();
        //La séparation du subDir permet de creer le répertoire si besoin
        $storage->saveFileContent($fileContent, $dirName, $fName);
        return $this;
    }


    /**
     * Retourne le contenu d'un Fichier sous la forme d'une chaîne de caractères.
     *
     * @param Fichier $fichier
     * @return string
     */
    public function getStorageFileContent(Fichier $fichier) : string
    {
        $storage = $this->getStorageAdapter();
        if(!isset($storage)){
            throw new RuntimeException("Storage service non défini");
        }
        $filename = $fichier->getNomStockage();
        $path = $storage->computeDirectoryPath();
        return $storage->getFileContent($path,$filename);
    }

    /** Supprime le fichier du storage, mais pas de la bdd */
    public function removeFromStorage(Fichier $fichier) : static
    {
        $storage = $this->getStorageAdapter();
        if(!isset($storage)){
            throw new RuntimeException("Storage service non défini");
        }

        $filename = $fichier->getNomStockage();
        $path = $storage->computeDirectoryPath();
        $storage->deleteFile($path,$filename);
        return $this;
    }


}