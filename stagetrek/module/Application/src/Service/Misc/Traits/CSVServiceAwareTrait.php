<?php


namespace Application\Service\Misc\Traits;


use Application\Service\Misc\CSVService;

Trait CSVServiceAwareTrait
{

    /** @var CSVService|null $csvService */
    protected ?CSVService $csvService = null;

    /**
     * @return \Application\Service\Misc\CSVService|null
     */
    public function getCsvService(): ?CSVService
    {
        return $this->csvService;
    }

    /**
     * @param CSVService $csvService
     * @return \Application\Service\FileService\Traits\CSVServiceAwareTrait
     */
    public function setCsvService(CSVService $csvService) : static
    {
        $this->csvService = $csvService;
        return $this;
    }
}