<?php


namespace Application\Form\Abstrait\Element;


use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Form\Element\Select;

Abstract class AbstractSelectPicker extends Select implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;


    public function init(): void
    {
        parent::init();
        //Pour avoir les attribus par défaut d'un selectPicker
        $this->setAttributes([]);
        $this->setDefaultData();
    }

    /**
     * @param array|\Traversable $attributes
     * @return AbstractSelectPicker
     */
    public function setAttributes($attributes)
    {
        if(!key_exists('class', $attributes)){
            $attributes['class'] = 'selectpicker';
        }
        else{
            $attributes['class'] .= ' selectpicker';
        }
        if(!key_exists('data-live-search', $attributes)){
            $attributes['data-live-search'] = true;
        }
        if(!key_exists('data-live-search-normalize', $attributes)){
            $attributes['data-live-search-normalize'] = true;
        }
        return parent::setAttributes($attributes);
    }

    /**
     * @param array|\Traversable $options
     * @return AbstractSelectPicker
     */
    public function setOptions($options)
    {
        parent::setOptions($options);
        //Si aucune option ou value_options n'est précisé, on met les options par défaut
        if (!key_exists('value_options', $options)
            && !key_exists('options', $options)
        ) {
            $this->setDefaultData();
        }
        return $this;
    }

    /**
     * @param  string|null $emptyOption
     * @return AbstractSelectPicker
     */
    public function setSelectPickerEmptyOption($emptyOption)
    {
        parent::setEmptyOption($emptyOption);
        $inputOptions = $this->getOptions();
        $inputOptions['empty_option']=$emptyOption;
        $this->setOptions($inputOptions);
        return $this;
    }

    /**
     * Fonction a appelé pour insérer les options du selection après avoir creer l'input
     * permet d'instancier le selectpicker via un tableau d'attribut et ensuite d'y inclure les options
     */
    protected abstract function setDefaultData() : static;
}