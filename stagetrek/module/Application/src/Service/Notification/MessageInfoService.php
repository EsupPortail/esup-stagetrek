<?php

namespace Application\Service\Notification;

use Application\Entity\Db\Etudiant;
use Application\Entity\Db\MessageInfo;
use Application\Entity\Db\Stage;
use Application\Provider\Notification\MessageInfoProvider;
use Application\Provider\Roles\RolesProvider;
use Application\Service\Misc\CommonEntityService;
use BjyAuthorize\Service\Authorize;
use DateTime;
use Exception;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\View\Renderer\PhpRenderer;
use UnicaenAuthentification\Service\Traits\UserContextServiceAwareTrait;
use ZfcUser\Entity\UserInterface;

/**
 * Class MessageInfoService
 * @package Application\Service\Messages
 */
class MessageInfoService extends CommonEntityService
{
    /** @return string */
    public function getEntityClass(): string
    {
        return MessageInfo::class;
    }

    /**
     * @return array
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findAll() : array
    {
        $messages = $this->getObjectRepository()->findAll();
        return $this->sortInfoMessages($messages);
    }

    public function sortInfoMessages(array $messages): array
    {
        usort($messages, function (MessageInfo $m1, MessageInfo $m2) {
            $severityOrder = [// severity   => order
                MessageInfoProvider::ERROR => 1, MessageInfoProvider::WARNING => 2, MessageInfoProvider::SUCCESS => 3, MessageInfoProvider::INFO => 4,];
            $m1Serverity = (isset($severityOrder[$m1->getPriority()])) ? $severityOrder[$m1->getPriority()] : 5;
            $m2Serverity = (isset($severityOrder[$m2->getPriority()])) ? $severityOrder[$m2->getPriority()] : 5;
            if ($m1Serverity != $m2Serverity) {
                return $m1Serverity - $m2Serverity;
            }
            return $m2->getDateMessage()->getTimestamp() - $m1->getDateMessage()->getTimestamp();
        });
        return $messages;
    }

    /**
     * todo : a reovir
     * @return MessageInfo[]
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     * @desc Retourne la liste des messages à afficher pour un utilisateur
     */
    public function getMessagesForCurrentUser(): array
    {
        $role = $this->getCurrentUserRole();
        /** @var MessageInfo[] $messages */
        $messages = $this->findAll();
        $messages = array_filter($messages, function (MessageInfo $message) use ($role) {
            if (!$message->isActif()) {
                return false;
            }
            return ($message->getRoles()->isEmpty() || $message->getRoles()->contains($role));
        });

        //Rajout de messages si l'utilisateur est étudiant
        //TODO : a ne pas faire ici, a revoir
        $today = new DateTime();
        $user = $this->getCurrentUser();
        if ($role && $role->getRoleId() == RolesProvider::ROLE_ETUDIANT) {
            $etudiant = $this->getObjectManager()->getRepository(Etudiant::class)->findOneBy(['user' => $user->getId()]);
            if ($etudiant) {
                /** @var Stage $stage */
                foreach ($etudiant->getStages() as $stage) {
                    if ($stage->hasEtatPhasePreferences()
                        && $stage->getPreferences()->count() == 0
                        && $stage->getDateDebutChoix() <= $today
                        && $today < $stage->getDateFinChoix()
                    ) {//Demande à un étudiant de définir ses préférences pour un stage
                        $title = sprintf("Stage %s", $stage->getLibelle());
                        $msg = sprintf("Vous avez jusqu'au <b/>%s</b> pour définir vos préférences.",
                            $stage->getDateFinChoix()->format('d/m/Y, H\hi')
                        );
                        $message = new MessageInfo();
                        $message->setTitle($title);
                        $message->setMessage($msg);
                        $message->setPriority(MessageInfoProvider::INFO);
                        $message->setDateMessage($stage->getDateDebutChoix());
                        $messages[] = $message;
                    }
                    if ($stage->hasEtatPhaseValidation()
                        && !$stage->getValidationStage()->validationEffectue()
                        && $stage->getDateFinValidation() < $today
                    ) {
                        $title = sprintf("%s", $stage->getLibelle());
                        $msg = "L'évaluation de votre stage n'as pas encore été effectuée.";
                        $msg .= "<br/>";
                        $msg .= "Merci de contacter votre responsable de stage afin qu'il procéde à la validation.";
//                        $msg .= sprintf("<span class='text-danger'>Vous ne pouvez pas définir vos préférences pour les sessions de stages suivante tant que cette action n'aura pas été faite.</span>");
                        $message = new MessageInfo();
                        $message->setTitle($title);
                        $message->setMessage($msg);
                        $message->setPriority(MessageInfoProvider::WARNING);
                        $message->setDateMessage($stage->getDateFinValidation());
                        $messages[] = $message;
                    }
                }
            }
        }

        return $this->sortInfoMessages($messages);
    }

    use UserContextServiceAwareTrait;

    /** @return RoleInterface|null */
    private function getCurrentUserRole(): ?RoleInterface
    {
        return $this->serviceUserContext->getSelectedIdentityRole();
    }

    /** @return \ZfcUser\Entity\UserInterface|null */
    private function getCurrentUser(): ?UserInterface
    {
        return $this->serviceUserContext->getDbUser();
    }

    /**
     * @var Authorize|null $serviceAuthorize
     */
    private ?Authorize $serviceAuthorize=null;

    /**
     * @param Authorize|null $serviceAuthorize
     */
    public function setServiceAuthorize(?Authorize $serviceAuthorize): void
    {
        $this->serviceAuthorize = $serviceAuthorize;
    }

    /**
     * @param $resource
     * @param null $privilege
     * @return boolean
     * @throws \Exception
     */
    protected function isAllowed($resource, $privilege = null): bool
    {
        if(!isset($this->serviceAuthorize)){
            throw new Exception("Service non initialisé");
        }
        return $this->serviceAuthorize->isAllowed($resource, $privilege);
    }

    /** @var PhpRenderer */
    protected PhpRenderer $renderer;

    /**
     * @param PhpRenderer $renderer
     * @return MessageInfoService
     */
    public function setRenderer(PhpRenderer $renderer): MessageInfoService
    {
        $this->renderer = $renderer;
        return $this;
    }

}