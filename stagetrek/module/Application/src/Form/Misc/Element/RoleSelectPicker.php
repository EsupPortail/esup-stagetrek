<?php


namespace Application\Form\Misc\Element;

use Application\Form\Abstrait\Element\AbstractSelectPicker;
use UnicaenUtilisateur\Entity\Db\Role;

class RoleSelectPicker extends AbstractSelectPicker
{
    public function setDefaultData() : static
    {
        $roles = $this->getObjectManager()->getRepository(Role::class)->findAll();
        usort($roles, function (Role $r1, Role $r2) {
            return strcmp($r1->getLibelle(), $r2->getLibelle());
        });
        $this->setRoles($roles);
        return $this;
    }

    /**
     * @param Role[] $roles
     */
    public function setRoles(array $roles) : static
    {
        //!!!Supprime tout les roles précédent
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = [];
        $this->setOptions($inputOptions);
        foreach ($roles as $r) {
            $this->addRole($r);
        }
        return $this;
    }

    public function addRole(Role $role): static
    {
        if ($this->hasRole($role)) return $this;
        $this->setRoleOption($role, 'label', $role->getLibelle());
        $this->setRoleOption($role, 'value', $role->getId());
        return $this;
    }

    public function removeRole(Role $role) : static
    {
        if (!$this->hasRole($role)) return $this;
        $options = $this->getOption('options');
        unset($options[$role->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasRole(Role $role) : bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($role->getId(), $options));
    }

    public function getRoleOptions(Role $role) : array
    {
        if (!$this->hasRole($role)) return [];
        $options = $this->getOption('options');
        return $options[$role->getId()];
    }

    public function getRoleAttributes(Role $role) : array
    {
        $options = $this->getRoleOptions($role);
        if (!key_exists('attributes', $options)) {
            return [];
        }
        return $options['attributes'];
    }

    public function setRoleOption(Role $role, $key, $value) : static
    {
        $options = $this->getRoleOptions($role);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$role->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setRoleAttribute(Role $role, $key, $value) : static
    {
        $attributes = $this->getRoleAttributes($role);
        $attributes[$key] = $value;
        $this->setRoleOption($role, 'attributes', $attributes);
        return $this;
    }
}