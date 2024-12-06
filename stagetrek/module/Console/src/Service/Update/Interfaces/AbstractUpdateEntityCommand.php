<?php

namespace Console\Service\Update\Interfaces;

use Application\Service\Misc\CommonEntityService;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractUpdateEntityCommand extends Command
    implements UpdateEntityCommandInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /** @var ServiceManager $serviceManager */
    private ServiceManager $serviceManager;

    /** @return ServiceManager|null */
    public function getServiceManager(): ?ServiceManager
    {
        return $this->serviceManager;
    }

    /** @param ServiceManager $serviceManager */
    public function setServiceManager(ServiceManager $serviceManager): void
    {
        $this->serviceManager = $serviceManager;
    }

    protected ?CommonEntityService $entityService = null;

    /**
     * @throws \Exception
     */
    protected function initEntityService(string $serviceName): CommonEntityService
    {
        try {
            $this->entityService = $this->serviceManager->get($serviceName);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }
        return $this->entityService;
    }

    /**
     * @throws Exception
     */
    public function getEntityService(): CommonEntityService
    {
        return  $this->entityService;
    }

    protected ?SymfonyStyle $io = null;
    public function initInputOut(InputInterface $input, OutputInterface $output): SymfonyStyle
    {
        $this->io = new SymfonyStyle($input, $output);
        return  $this->io;
    }
    public function getInputOutPut() : SymfonyStyle
    {
        return  $this->io;
    }


    protected array $log=[];
    public function addLog($msg): void
    {
        $this->log[] = $msg;
    }

    public function clearLog(): void
    {
        $this->log=[];
    }
    public function renderLog() : void
    {
        if(!empty($log)){
            $this->getInputOutPut()->info($this->log);
        }
    }


    //Pour quelques cas (ie : les contraintes de cursus)

    /**
     * @throws \Exception
     */
    protected function execProcedure($procedure, $params = []): void
    {
        try {
            $this->beginTransaction(); // suspend auto-commit
            $plsql = sprintf("call %s(%s);", $procedure, implode($params));
            $stmt = $this->getObjectManager()->getConnection()->prepare($plsql);
            $stmt->executeStatement();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }


    /**
     * Proxy method.
     *
     * @see EntityManager::beginTransaction()
     */
    public function beginTransaction(): void
    {
        $this->getObjectManager()->beginTransaction();
    }

    /**
     * Proxy method.
     *
     * @see EntityManager::commit()
     */
    public function commit(): void
    {
        $this->getObjectManager()->commit();
    }

    /**
     * Proxy method.
     *
     * @see EntityManager::rollback()
     */
    public function rollback(): void
    {
        $this->getObjectManager()->rollback();
    }
}