<?php

namespace Application\Form\Misc\Traits;

use Application\Form\Misc\Element\TagSelectPicker;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use UnicaenTag\Entity\Db\Tag;
use UnicaenTag\Service\Tag\TagServiceAwareTrait;

/**
 * @method add(array $array)
 * @method setInputfilterSpecification($TAGS, array $array)
 * @method get(string $name)
 */
trait TagInputAwareTrait
{
    public function initTagsInputs() : static
    {
        $this->add([
            'type' => TagSelectPicker::class,
            'name' => HasTagInputInterface::TAGS,
            'options' => [
                'label' => 'Tags',
            ],
            'attributes' => [
                'id' => HasTagInputInterface::TAGS,
                "class" => 'selectpicker',
                "data-live-search" => true,
                "data-live-search-normalize" => true,
                "multiple" => "multiple",
                "data-tick-icon" => "fas fa-check text-primary",
                "title" => "Sélectionner des tags",
                "data-selected-text-format" => "count > 2",
                "data-count-selected-text" => "{0} tags selectionnées",
                "data-actions-box" => "true",
                "data-select-all-text" => "Tout selectionner",
                "data-deselect-all-text" => "Tout déselectionner",
            ],
        ]);

        $this->setInputfilterSpecification(HasTagInputInterface::TAGS , [
            "name" => HasTagInputInterface::TAGS,
            'required' => false,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ],
        ]);

        $tag = $this->getTagsAvailables();
        $this->setTagsAvailables($tag);
        return $this;
    }

    /**
     * @param $tags
     */
    public function setTagsAvailables(array $tags) : static
    {
        /** @var TagSelectPicker $input */
        $input = $this->get(HasTagInputInterface::TAGS);
        $input->setTags($tags);
        return $this;
    }

    use TagServiceAwareTrait;
    /**
     * @param $tags
     */
    public function getTagsAvailables() : array
    {
        $tags = $this->getTagService()->getTags();
        usort($tags, function (Tag $t1, Tag $t2) {
            $c1 = $t1->getCategorie();
            $c2 = $t2->getCategorie();
            if($c1->getId() !== $c2->getId()){
                if($c1->getOrdre() < $c2->getOrdre()) return -1;
                if($c2->getOrdre() < $c1->getOrdre()) return 1;
                return ($c1->getId() < $c2->getId()) ? -1 : 1;
            }
            if($t1->getOrdre() < $t2->getOrdre()) return -1;
            if($t2->getOrdre() < $t1->getOrdre()) return 1;
            return ($t1->getId() < $t2->getId()) ? -1 : 1;
        });
        return $tags;
    }
}