<?php


namespace Application\Service\Groupe\Traits;


use Application\Service\Groupe\GroupeService;

/**
 * Traits GroupeServiceAwareTrait
 * @package Application\Service\Groupe
 */
trait GroupeServiceAwareTrait
{
    /**
     * @var GroupeService|null $groupeService
     */
    private ?GroupeService $groupeService = null;

    /**
     * @return GroupeService
     */
    public function getGroupeService() : GroupeService
    {
        return $this->groupeService;
    }

    /**
     * @param GroupeService $groupeService
     * @return \Application\Service\Groupe\GroupeServiceAwareTrait
     */
    public function setGroupeService(GroupeService $groupeService) : static
    {
        $this->groupeService = $groupeService;
        return $this;
    }
}