<?php

namespace Application\Form\Misc\Validator;

use Application\Service\Misc\CommonEntityService;
use Laminas\Validator\AbstractValidator;
use RuntimeException;

/**
 * TODO : voir si besoin de généralisé à d'autre champ que libellé (par exemple numeroEtudiant
 * Si oui, Transformet en EntityExsitValidator ou faire d'autre validator spécifique ?
 * Class LibelleValidator
 * @package Application\Form\Validator
 */
class LibelleValidator extends AbstractValidator
{
    const LIBELLE_EMPTY_ERROR = 'ACRONYME_EMPTY_ERROR';
    const LIBELLE_ALREADY_USED_ERROR = 'ACRONYME_ALREADY_USED_ERROR';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::LIBELLE_EMPTY_ERROR => "Le champ libellé ne peut être vide.",
        self::LIBELLE_ALREADY_USED_ERROR => "Ce libellé est déjà utilisé.",
    ];

    public function setMessageTemplate($key, $value){
        $this->messageTemplates[$key] = $value;
        $this->abstractOptions['messageTemplates'][$key] = $value;
    }
    /**
     * @var array
     */
    protected $messageVariables = [
    ];

    /** @var CommonEntityService $entityService */
    protected $entityService;
    public function setEntityService(CommonEntityService $entityService){
        $this->entityService = $entityService;
    }

    /** @var mixed|null $entity */
    protected $entity;
    public function setEntity($entity){
        if($entity===null){
            $this->entity = null;
            return;
        }
        if(method_exists($entity,$this->idGetter())){
            throw new RuntimeException("L'entité fournise doit implémenté la fonction getId().");
        }
        if(method_exists($entity, $this->libelleGetter())){
            throw new RuntimeException("L'entité fournise doit implémenté la fonction getLibelle().");
        }
        $this->entity = $entity;
    }

    /**
     * Validation
     *
     * @param mixed $value
     * @param mixed $context Additional context to provide to the callback
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        if(!$this->unique){
            return true;
        }
        if($this->entityService===null){
            throw new RuntimeException("Aucun service n'as été fournis pour déterminer si le libellé est valide.");
        }
        if($value==null ||$value==""){
            $this->error(self::LIBELLE_EMPTY_ERROR);
            return false;
        }

        $attribute = ($this->attributeLibelle) ?? 'libelle';
        $id = ($this->attributeId) ?? 'id';
        $getId = $this->idGetter();
        $entityCorrespondante = $this->entityService->findOneBy([$attribute => $value]);
        $currentId = ($context[$id]) ?? null;
        if (!$entityCorrespondante) {
            return true;
        }
        elseif($currentId != $entityCorrespondante->$getId()) { // cas d'une modification
            $this->error(self::LIBELLE_ALREADY_USED_ERROR);
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


    /** @var string $attributeLibelle */
    protected $attributeLibelle;

    public function setAttributeLibelle(?string $attributeLibelle)
    {
        $this->attributeLibelle = $attributeLibelle;
    }

    protected function libelleGetter()
    {
        $attribute = ($this->attributeLibelle) ?? 'libelle';
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
