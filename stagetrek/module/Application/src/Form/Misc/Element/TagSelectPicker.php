<?php


namespace Application\Form\Misc\Element;

use Application\Form\Abstrait\Element\AbstractSelectPicker;
use UnicaenTag\Entity\Db\Tag;
use UnicaenTag\Entity\Db\TagCategorie;

class TagSelectPicker extends AbstractSelectPicker
{
    public function setDefaultData() : static
    {
        $tags = $this->getObjectManager()->getRepository(Tag::class)->findAll();
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
        $this->setTags($tags);
        return $this;
    }


    /**
     * @param Tag[] $tags
     */
    public function setTags(array $tags) : static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = [];
        $this->setOptions($inputOptions);
        foreach ($tags as $t){
            $this->addTag($t);
        }
        return $this;
    }

    //Permet nottament de modifier le label de la catégorie
    public function addCategorie(TagCategorie $categorie) : static
    {
        if($this->hasCategorie($categorie)) return $this;
        $this->setCategorieOption($categorie, 'label', $categorie->getLibelle());
        $this->setCategorieOption($categorie, 'options', []);
        return $this;
    }

    public function addTag(Tag $tag) : static
    {
        if($this->hasTag($tag)) return $this;
        $categorie = $tag->getCategorie();
        if(!$this->hasCategorie($categorie)){
            $this->addCategorie($categorie);
        }
        $this->setTagOption($tag, 'label', $tag->getLibelle());
        $this->setTagOption($tag, 'value', $tag->getId());
        $label = sprintf("<span class='badge' style='background:%s'><span class='%s'></span>%s</span>",
            $tag->getCouleur(),
            ($tag->getIcone()) ? "me-1 ".$tag->getIcone() : null,
            $tag->getLibelle());
        $this->setTagAttribute($tag, 'data-content', $label);
        return $this;
    }

    public function removeCategorieDeStage(TagCategorie $categorie) : static
    {
        if(!$this->hasCategorie($categorie))  return $this;
        $value_options = $this->getOption('value_options');
        unset($value_options[$categorie->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function removeTag(Tag $tag) : static
    {
        if(!$this->hasTag($tag)) return $this;
        $categorie = $tag->getCategorie();
        $value_options = $this->getOption('value_options');
        unset($value_options[$categorie->getId()]['options'][$tag->getId()]);
        if(empty($value_options[$categorie->getId()]['options'])){
            $this->removeCategorieDeStage($categorie);
            //Car a été changé
            $value_options = $this->getOption('value_options');
        }
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }


    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasCategorie(TagCategorie $categorie) : bool
    {
        $value_options = $this->getOption('value_options');
        return ($value_options && key_exists($categorie->getId(), $value_options));
    }
    public function hasTag(Tag $tag) : bool
    {
        $categorie = $tag->getCategorie();
        if(!$this->hasCategorie($categorie)) return false;
        $categorieOption = $this->getCategorieOptions($categorie);
        return (key_exists('options', $categorieOption) && key_exists($tag->getId(), $categorieOption['options']));
    }

    public function getCategorieOptions(TagCategorie $categorie) : array{
        if(!$this->hasCategorie($categorie)) return [];
        $value_options = $this->getOption('value_options');
        return $value_options[$categorie->getId()];
    }
    public function getCategorieAttributes(TagCategorie $categorie) : array
    {
        $categorieOptions = $this->getCategorieOptions($categorie);
        if(!key_exists('attributes', $categorieOptions)){return [];}
        return $categorieOptions['attributes'];
    }

    public function getTagOptions(Tag $tag) : array
    {
        if(!$this->hasTag($tag)) return [];
        $categorie = $tag->getCategorie();
        $categorieOptions = $this->getCategorieOptions($categorie);
        return  $categorieOptions['options'][$tag->getId()];
    }

    public function getTagAttributes(Tag $tag) : array
    {
        $tagOptions = $this->getTagOptions($tag);
        if(!key_exists('attributes', $tagOptions)){return [];}
        return $tagOptions['attributes'];
    }

    /**
     * ie : setCategorieOption($tag, 'label', "Label de la catégorie")
     * @param TagCategorie $categorie
     * @param string $key
     * @param mixed $value
     * @return \Application\Form\Misc\Element\TagSelectPicker
     */
    public function setCategorieOption(TagCategorie $categorie, string $key, mixed $value) : static
    {
        $options = $this->getCategorieOptions($categorie);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$categorie->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /**
     * ie : setTagOption($tag, 'label', "Label du terrain")
     * @param Tag $tag
     * @param String $key
     * @param mixed $value
     * @return \Application\Form\Misc\Element\TagSelectPicker
     */
    public function setTagOption(Tag $tag, string $key, mixed $value) : static
    {
        $options = $this->getTagOptions($tag);
        $options[$key] = $value;
        $categorie = $tag->getCategorie();
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$categorie->getId()]['options'][$tag->getId()]=$options;
        $this->setOptions($inputOptions);
        return $this;
    }

    //Permet nottament de rajouter des data-attributes
    public function setCategorieAttribute(TagCategorie $categorie, string $key, mixed $value) : static
    {
        if(!$this->hasCategorie($categorie)) return $this;
        $attributes = $this->getCategorieAttributes($categorie);
        $attributes[$key]=$value;
        $this->setCategorieOption($categorie, 'attributes', $attributes);
        return $this;
    }

    public function setTagAttribute(Tag $tag, string $key, mixed $value) : static
    {
        if(!$this->hasTag($tag))  return $this;
        $attributes = $this->getTagAttributes($tag);
        $attributes[$key]=$value;
        $this->setTagOption($tag, 'attributes', $attributes);
        return $this;
    }
}