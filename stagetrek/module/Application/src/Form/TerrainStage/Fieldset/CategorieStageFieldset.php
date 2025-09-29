<?php


namespace Application\Form\TerrainStage\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\AcronymeInputAwareTrait;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Provider\Tag\CategorieTagProvider;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use UnicaenTag\Entity\Db\Tag;

/**
 * Class CategorieStageFieldset
 * @package Application\Form\TerrainStage\Fieldset
 */
class CategorieStageFieldset extends AbstractEntityFieldset
implements HasTagInputInterface
{

    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use OrdreInputAwareTrait;
    use AcronymeInputAwareTrait;
    use TagInputAwareTrait;

    public function init() : static
    {
        $this->initIdInput();
        $this->initCodeInput(true);
        $this->initLibelleInput();
        $this->initOrdreInput();
        $this->initAcronymeInput();
        $this->initInputCategorie();
        $this->initTagsInputs();
        return $this;
    }

    const CATEGORIE_PRINCIPALE = "categoriePrincipale";
    protected function initInputCategorie(): static
    {
        $this->add([
            'name' => self::CATEGORIE_PRINCIPALE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Catégorie de stage principale",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::CATEGORIE_PRINCIPALE,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->setInputfilterSpecification(self::CATEGORIE_PRINCIPALE, [
            'name' => self::CATEGORIE_PRINCIPALE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        return $this;
    }

    public function getTagsAvailables(): array
    {
        $tags = $this->getTagService()->getTags();
        usort($tags, function (Tag $t1, Tag $t2) {
            $c1 = $t1->getCategorie();
            $c2 = $t2->getCategorie();
            if ($c1->getId() !== $c2->getId()) {
                //Trie spécifique : on met d'abord la catégorie Années
                if ($c1->getCode() == CategorieTagProvider::TERRAIN
                    || $c1->getCode() == CategorieTagProvider::CATEGORIE_STAGE
                ) {
                    return -1;
                }
                if ($c2->getCode() == CategorieTagProvider::TERRAIN
                    || $c2->getCode() == CategorieTagProvider::CATEGORIE_STAGE
                ) {
                    return 1;
                }
                if ($c1->getOrdre() < $c2->getOrdre()) return -1;
                if ($c2->getOrdre() < $c1->getOrdre()) return 1;
                return ($c1->getId() < $c2->getId()) ? -1 : 1;
            }
            if ($t1->getOrdre() < $t2->getOrdre()) return -1;
            if ($t2->getOrdre() < $t1->getOrdre()) return 1;
            return ($t1->getId() < $t2->getId()) ? -1 : 1;
        });
        return $tags;
    }

}