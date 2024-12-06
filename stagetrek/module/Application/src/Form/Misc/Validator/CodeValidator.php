<?php

namespace Application\Form\Misc\Validator;

use Application\Service\Misc\CommonEntityService;
use Laminas\Validator\AbstractValidator;
use RuntimeException;

/**
 * Si oui, Transformet en EntityExsitValidator ou faire d'autre validator spécifique ?
 * Class LibelleValidator
 * @package Application\Form\Validator
 */
class CodeValidator extends AbstractValidator
{
    const CODE_EMPTY_ERROR = 'CODE_EMPTY_ERROR';
    const CODE_ALREADY_USED_ERROR = 'CODE_ALREADY_USED_ERROR';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::CODE_EMPTY_ERROR => "Le champ code ne peut être vide.",
        self::CODE_ALREADY_USED_ERROR => "Ce code est déjà utilisé.",
    ];

    public function setMessageTemplate($key, $value)
    {
        $this->messageTemplates[$key] = $value;
        $this->abstractOptions['messageTemplates'][$key] = $value;
    }

    /**
     * @var array
     */
    protected $messageVariables = [
    ];

    /** @var CommonEntityService $entityService */
    protected CommonEntityService $entityService;

    public function setEntityService(CommonEntityService $entityService): static
    {
        $this->entityService = $entityService;
        return $this;
    }

    /** @var mixed|null $entity */
    protected mixed $entity = null;

    public function setEntity(mixed $entity): static
    {
        if ($entity === null) {
            $this->entity = null;
            return $this;
        }
        if (method_exists($entity, 'getCode()')) {
            throw new RuntimeException("L'entité fournise doit implémenté la fonction getCode().");
        }
        $this->entity = $entity;
        return $this;
    }

    /**
     * Validation
     *
     * @param mixed $value
     * @param mixed $context Additional context to provide to the callback
     * @return bool
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isValid($value, $context = null)
    {
        $noError = false;
        if ($this->entityService === null) {
            throw new RuntimeException("Aucun service n'as été fournis pour déterminer si le code est valide.");
        }
        if ($value == null || $value == "") {
            $this->error(self::CODE_EMPTY_ERROR);
            return false;
        }

        $attribute = ($this->attributeCode) ?? 'code';
        $id = ($this->attributeId) ?? 'id';
        $getId = $this->idGetter();
        $entityCorrespondante = $this->entityService->findByAttribute($value, $attribute, false);
        $currentId = ($context[$id]) ?? null;
        if (!$entityCorrespondante) {
            return true;
        }
        elseif($currentId != $entityCorrespondante->$getId()) { // cas d'une modification
            $this->error(self::CODE_ALREADY_USED_ERROR);
            return false;
        }
        return true;
    }

    /** @var string $attributeCode */
    protected $attributeCode;

    public function setAttributeCode(?string $attributeCode)
    {
        $this->attributeCode = $attributeCode;
    }

    protected function attributeGetter()
    {
        $attribute = ($this->attributeCode) ?? 'code';
        return 'get' . ucfirst($attribute);
    }

    /** @var string $attributeId */
    protected $attributeId;

    public function setAttributeId(?string $attributeId)
    {
        $this->attributeId = $attributeId;
    }

    protected function idGetter()
    {
        $attribute = ($this->attributeId) ?? 'id';
        return 'get' . ucfirst($attribute);
    }

}
