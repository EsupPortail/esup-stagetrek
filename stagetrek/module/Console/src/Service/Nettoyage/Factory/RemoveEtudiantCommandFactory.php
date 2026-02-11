<?php

namespace Console\Service\Nettoyage\Factory;

use Application\Entity\Db\AffectationStage;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Stage\StageService;
use Console\Service\Nettoyage\RemoveEtudiantCommand;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

class RemoveEtudiantCommandFactory extends Command
{
    /**
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): RemoveEtudiantCommand
    {
        $command = new RemoveEtudiantCommand();

//        /**
//         * @var UserService $userService
//         * @var RoleService $roleService
//         */
        $command->setEtudiantService( $container->get(EtudiantService::class));
        $command->setStageService( $container->get(StageService::class));

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $command->setObjectManager($entityManager);

        return $command;
    }

}