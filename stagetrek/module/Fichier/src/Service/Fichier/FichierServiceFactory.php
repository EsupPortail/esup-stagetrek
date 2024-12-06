<?php

namespace Fichier\Service\Fichier;

use Doctrine\ORM\EntityManager;
use Exception;
use Fichier\Filter\FileName\FileNameFormatterInterface;
use Interop\Container\ContainerInterface;
use UnicaenStorage\Adapter\StorageAdapterInterface;
use UnicaenStorage\StorageAdapterManager;
use UnicaenUtilisateur\Service\User\UserService;
use Webmozart\Assert\Assert;

class FichierServiceFactory {

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container): FichierService
    {
        try {
            $config = $container->get('Config');
            if(!isset($config['fichier'])){
                throw new Exception("Le paramétre de configuration 'fichier' n'est pas définie");
            }
        }
        catch (Exception $e) {
            throw new Exception("La configuration de la gestion des fichiers n'est pas correctement définie.<br/>".$e->getMessage());
        }
        /**
         * @var EntityManager $entityManager
         * @var UserService $userService
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userService = $container->get(UserService::class);

        $service = new FichierService();
        $service->setObjectManager($entityManager);
        $service->setUserService($userService);

        $storageAdapter = $this->getStorageAdpater($container);
        $service->setStorageAdapter($storageAdapter);

        $fileNameProvider =  $this->getFileNameProvider($container);
        $service->setFileNameFormatter($fileNameProvider);

        return $service;
    }


    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getFileNameProvider(\Psr\Container\ContainerInterface $container): FileNameFormatterInterface
    {
        /** @var array $config */
        $config = $container->get('Config');
        $fileNameProviderName = ($config['fichier']['file-name-formatter']) ?? null;
        Assert::notNull($fileNameProviderName, "Clé de config introuvable : 'fichier > file-name-formatter'");

        /** @var FileNameFormatterInterface $fileNameProvicer */
        $fileNameProvicer = $container->get($fileNameProviderName);
        if(!is_a($fileNameProvicer, FileNameFormatterInterface::class)){
            throw new Exception("Le FileNameProvider n'est pas valide");
        }

        return $fileNameProvicer;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getStorageAdpater(\Psr\Container\ContainerInterface $container): StorageAdapterInterface
    {
        /** @var array $config */
        $config = $container->get('Config');

        $storageAdapterServiceName = ($config['fichier']['storage']['adapter']) ?? null;
        Assert::notNull($storageAdapterServiceName, "Clé de config introuvable : 'fichier > storage > adapter'");

        /** @var StorageAdapterManager $storageAdapterManager */
        $storageAdapterManager = $container->get(StorageAdapterManager::class);

        return $storageAdapterManager->get($storageAdapterServiceName);
    }
}