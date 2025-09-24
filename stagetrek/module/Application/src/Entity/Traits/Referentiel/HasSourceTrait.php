<?php

namespace Application\Entity\Traits\Referentiel;

use Application\Entity\Db\Source;

/**
 *
 */
trait HasSourceTrait
{
    /**
     * @var \Application\Entity\Db\Source|null
     */
    protected ?Source $source=null;

    /**
     * @return \Application\Entity\Db\Source|null
     */
    public function getSource(): ?Source
    {
        return $this->source;
    }

    /**
     * @param \Application\Entity\Db\Source|null $source
     * @return \Referentiel\Entity\Db\Traits\HasSourceTrait
     */
    public function setSource(?Source $source): static
    {
        $this->source = $source;
        return $this;
    }


    public function hasSource(): bool
    {
        return $this->source !== null;
    }

    /**
     * @var string
     */
    private ?string $sourceCode = null;

    /**
     * @return string|null
     */
    public function getSourceCode(): ?string
    {
        return $this->sourceCode;
    }

    /**
     * @param string|null $sourceCode
     */
    public function setSourceCode(?string $sourceCode): static
    {
        $this->sourceCode = $sourceCode;
        return $this;
    }

    public function hasExternalSource() : bool
    {
        return isset($this->source) && $this->source->getCode() != Source::STAGETREK;
    }
}