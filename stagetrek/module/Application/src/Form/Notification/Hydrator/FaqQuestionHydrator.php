<?php


namespace Application\Form\Notification\Hydrator;

use Application\Entity\Db\Faq;
use Application\Entity\Db\FaqCategorieQuestion;
use Application\Form\Notification\Fieldset\FaqQuestionFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenUtilisateur\Entity\Db\Role;

/**
 * Class FaqQuestionHydrator
 * @package Application\Form\Hydrator
 */
class FaqQuestionHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var Faq $question */
        $question = $object;
        $data = [];
        $data[FaqQuestionFieldset::ID] = ($question->getId()) ?: 0;
        $data[FaqQuestionFieldset::CATEGORIE] = ($question->getCategorie()) ? $question->getCategorie()->getId() : null;
        $data[FaqQuestionFieldset::ORDRE] = ($question->getOrdre()) ?: 0;
        $data[FaqQuestionFieldset::QUESTION] = ($question->getQuestion()) ?: null;
        $data[FaqQuestionFieldset::REPONSE] = ($question->getReponse()) ?: null;
        if($question->getRoles()->isEmpty()){
            $data[FaqQuestionFieldset::ROLES]=[];
        }
        else{
            foreach ($question->getRoles() as $r){
                $data[FaqQuestionFieldset::ROLES][]=$r->getId();
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param object $object
     * @return Faq
     */
    public function hydrate(array $data, object $object): Faq
    {
        /** @var Faq $question */
        $question = $object;

       if (key_exists(FaqQuestionFieldset::CATEGORIE, $data)) {
           $idCategorie = intval($data[FaqQuestionFieldset::CATEGORIE]);
           /** @var FaqCategorieQuestion $categorie */
           $categorie = $this->getObjectManager()->getRepository(FaqCategorieQuestion::class)->find($idCategorie);
           if ($categorie) {
               $question->setCategorie($categorie);
           }
       }
       if(key_exists(FaqQuestionFieldset::ORDRE, $data)){
           $question->setOrdre(intval($data[FaqQuestionFieldset::ORDRE]));
       }
       if(key_exists(FaqQuestionFieldset::QUESTION, $data)){
           $question->setQuestion(trim($data[FaqQuestionFieldset::QUESTION]));
       }
       if(key_exists(FaqQuestionFieldset::REPONSE, $data)){
           $question->setReponse(trim($data[FaqQuestionFieldset::REPONSE]));
       }
       if(key_exists(FaqQuestionFieldset::ROLES, $data)){
           $rolesSelected = $data[FaqQuestionFieldset::ROLES];
           /** @var Role[] $roles */
           $roles = $this->getObjectManager()->getRepository(Role::class)->findAll();
           $roles = array_filter($roles, function (Role $r) use ($rolesSelected) {
               return in_array($r->getId(), $rolesSelected);
           });
           $question->getRoles()->clear();
           foreach ($roles as $r){
               $question->addRole($r);
           }
       }
       else{
           $question->getRoles()->clear();
       }
        return $question;
    }
}