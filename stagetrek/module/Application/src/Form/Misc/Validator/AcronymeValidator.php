<?php

namespace Application\Form\Misc\Validator;

use Application\Entity\Interfaces\AcronymeEntityInterface;
use Application\Service\Misc\CommonEntityService;
use Laminas\Validator\AbstractValidator;
use RuntimeException;

/**
 * TODO : voir si besoin de généralisé à d'autre champ que libellé (par exemple numeroEtudiant
 * Si oui, Transformet en EntityExsitValidator ou faire d'autre validator spécifique ?
 * Class AcronymeValidator
 * @package Application\Form\Validator
 */
class AcronymeValidator extends AbstractValidator
{
    const ACRONYME_EMPTY_ERROR = 'ACRONYME_EMPTY_ERROR';
    const ACRONYME_ALREADY_USED_ERROR = 'ACRONYME_ALREADY_USED_ERROR';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::ACRONYME_EMPTY_ERROR => "Le champ acronyme ne peut être vide.",
        self::ACRONYME_ALREADY_USED_ERROR => "Cet acronyme est déjà utilisé.",
    ];

    public function setMessageTemplate($key, $value) : static
    {
        $this->messageTemplates[$key] = $value;
        $this->abstractOptions['messageTemplates'][$key] = $value;
         return $this;
    }
    /**
     * @var array
     */
    protected $messageVariables = [
    ];

    /** @var CommonEntityService|null $entityService */
    protected ?CommonEntityService $entityService = null;
    public function setEntityService(CommonEntityService $entityService) : static
    {
        $this->entityService = $entityService;
        return $this;
    }

    /** @var AcronymeEntityInterface|null $entity */
    protected ?AcronymeEntityInterface $entity = null;
    public function setEntity(AcronymeEntityInterface $entity) : static
    {
        if(method_exists($entity,$this->idGetter())){
            throw new RuntimeException("L'entité fournise doit implémenté la fonction getId().");
        }
        if(method_exists($entity, $this->acronymeGetter())){
            throw new RuntimeException("L'entité fournise doit implémenté la fonction getAcronyme().");
        }
        $this->entity = $entity;
        return $this;
    }

    /**
     * Validation
     *
     * @param mixed $value
     * @param mixed|null $context Additional context to provide to the callback
     * @return bool
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function isValid(mixed $value, mixed $context = null) : bool
    {
        if(!$this->unique){
            return true;
        }
        if($this->entityService===null){
            throw new RuntimeException("Aucun service n'as été fournis pour déterminer si le libellé est valide.");
        }
        if($value==null ||$value==""){
            $this->error(self::ACRONYME_EMPTY_ERROR);
            return false;
        }

        $attribute = ($this->attributeAcronyme) ?? 'acronyme';
        $id = ($this->attributeId) ?? 'id';
        $getId = $this->idGetter();
        $entityCorrespondante = $this->entityService->findOneBy([$attribute => $value]);
        $currentId = ($context[$id]) ?? null;
        if (!$entityCorrespondante) {
            return true;
        }
        elseif($currentId != $entityCorrespondante->$getId()) { // cas d'une modification
            $this->error(self::ACRONYME_ALREADY_USED_ERROR);
            return false;
        }
        return true;
    }

    protected bool $unique=true;
    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @param bool $unique
     */
    public function setUnique(bool $unique): void
    {
        $this->unique = $unique;
    }


    /** @var string $attributeAcronyme */
    protected string $attributeAcronyme = 'acronyme';

    public function setAttributeAcronyme(?string $attributeAcronyme) : static
    {
        $this->attributeAcronyme = $attributeAcronyme;
        return $this;
    }

    protected function acronymeGetter() : string
    {
        $attribute = ($this->attributeAcronyme) ?? 'acronyme';
        return 'get' . ucfirst($attribute);
    }


    /** @var string $attributeId */
    protected string $attributeId = 'id';

    public function setAttributeId(?string $attributeId) : static
    {
        $this->attributeId = $attributeId;
        return $this;
    }

    protected function idGetter() : string
    {
        $attribute = ($this->attributeId) ?? 'id';
        return 'get' . ucfirst($attribute);
    }
}
