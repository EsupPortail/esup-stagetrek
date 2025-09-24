<?php

namespace Application\Entity\Interfaces;


use Application\Entity\Db\Source;

interface HasSourceInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Une entité ayant une source doit nécessairement avoir un code (ne serait-ce que pour dire que la source est elle même
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @return Source|Null
     */
    public function getSource(): ?Source;
    /**
     * @param Source $source
     */
    public function setSource(Source $source): static;
    /**
     * @return string|null
     */
    public function getSourceCode(): ?string;
    /**
     * @param string $codeSource
     */
    public function setSourceCode(?string $codeSource): static;
}