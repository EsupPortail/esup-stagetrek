<?php

namespace Application\Entity\Traits\InterfaceImplementation;

trait HasDescriptionTrait
{
    /** @var string|null $description */
    protected ?string $description=null;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return \Application\Entity\Traits\InterfaceImplementation\HasDescriptionTrait
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }
}