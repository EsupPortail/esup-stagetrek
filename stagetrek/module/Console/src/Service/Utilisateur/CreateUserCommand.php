<?php

namespace Console\Service\Utilisateur;

use Application\Provider\Mailing\CodesMailsProvider;
use Application\Provider\Misc\TypeAuthentificationProvider;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenMail\Entity\Db\Mail;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;
use UnicaenUtilisateur\Entity\Db\User;
use UnicaenUtilisateur\Entity\Db\UserInterface;
use UnicaenUtilisateur\Exception\RuntimeException;
use UnicaenUtilisateur\Service\User\UserService;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'utilisateur:create-user';

    use UserServiceAwareTrait;

    protected function configure() : static
    {
        $this->setDescription("Création d'un utilisateur");
        return $this;
    }
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        try{
            $this->io = new SymfonyStyle($input, $output);
        }
        catch (Exception $e){
            $this->io->error($e->getMessage());
            exit(-1);
        }
    }

    protected ?SymfonyStyle $io = null;


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        try {
            $this->io->title("Création d'un utilisateur");
            $user = new User();

            $username = $this->io->ask('Username');
            $username = (isset($username)) ? trim($username) : null;
            if(!isset($username)||$username==""){
                $this->io->error('username non saisie');
                return Command::FAILURE;
            }
            $existingUser = $this->getUserService()->findByUsername($username);
            if ($existingUser) {
                $continue = $this->io->confirm("Un utilisateur avec ce username existe déjà, voulez-vous le modifier ?", true);
                if(!$continue){
                    $this->io->text("Abandon de la création de l'utilisateur");
                    return Command::SUCCESS;
                }
                $user = $existingUser;
            }
            $user->setUsername($username);

            $displayName = $this->io->ask('Display name');
            $displayName = (isset($displayName)) ? trim($displayName) : null;
            if(!isset($displayName)||$displayName==""){
                $this->io->error('DisplayName non saisie');
                return Command::FAILURE;
            }
            $user->setDisplayName($displayName);

            $mail = $this->io->ask('Email');
            $mail = (isset($mail)) ? trim($mail) : null;
            if(!isset($mail)||!filter_var($mail, FILTER_VALIDATE_EMAIL)){
                $this->io->error('Adresse mail non valide');
                return Command::FAILURE;
            }
            $existingUser = $this->getUserService()->getRepo()->findOneBy(['email' => $mail]);
            if ($existingUser && $user->getId() != null && $existingUser->getId() != $user->getId()) {
                $this->io->error("Un utilisateur autre utilisateur avec adresse mail existe déjà.");
                return Command::FAILURE;
            }
            elseif ($existingUser) {
                $continue = $this->io->confirm("Un utilisateur avec cet adresse mail existe déjà, voulez-vous le modifier ?", true);
                if(!$continue){
                    $this->io->text("Abandon de la création de l'utilisateur");
                    return Command::SUCCESS;
                }
                $user = $existingUser;
            }
            $user->setEmail($mail);

            $authServices = $this->authentificationServices;
            if(!isset($authServices) || empty($authServices)){
                throw new Exception("Aucun service d'authentification n'est défini");
            }
            $auth = $this->io->choice(
                "Veuillez choisir le type d'authentification de l'utilisateur",
                $authServices
            );
            $user->setPassword($auth);
            $user->setState(true);

            $user = $this->getUserService()->create($user);

            if($auth==TypeAuthentificationProvider::LOCAL){
                $this->generatePassowrdToken($user);
            }
            $this->io->success("Création de l'utilisateur ".$username." réussie");
        }
        catch (Exception $e){
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    use MailServiceAwareTrait;
    protected ?string $appname = null;
    protected ?string $uriHost = null;
    protected ?string $uriScheme = null;
    public function setAppname(?string $appname): void
    {
        $this->appname = $appname;
    }
    public function setUriHost(?string $uriHost): void
    {
        $this->uriHost = $uriHost;
    }
    public function setUriScheme(?string $uriScheme): void
    {
        $this->uriScheme = $uriScheme;
    }

    /**
     * @param $user
     * @return \UnicaenUtilisateur\Entity\Db\User
     * @throws \Exception
     * @see UserService::createLocal()
     * @desc contourne le problème du UrlViewHelper en console
     */
    private function generatePassowrdToken($user) : User
    {
        $user->setPassword('db');
        try {
            $token = Uuid::uuid4()->toString();
        } catch (Exception $e) {
            throw new Exception("Une erreur s'est produite lors de la génération du token.");

        }
        $user->setPasswordResetToken($token);
        $this->getUserService()->update($user);

        $appname = $this->appname;
        if(!isset($appname)||$appname==""){
            throw new RuntimeException("La configuration du paramétre appname n'existe pas");
        }
        $url = $this->getInitPasswordURL($user);

        $subject = "Initialisation de votre compte pour l'application" . $appname;
        $body  = "<p>Bonjour " . $user->getDisplayName() . ",</p>";
        $body .= "<p>Pour initialiser votre mot de passe pour l'application " . $appname . " veuillez suivre le lien suivant <a href='" . $url . "'>" . $url . "</a>.</p>";
        $body .= "<p>Vous aurez besoin de votre identifiant qui est <strong>" . $user->getUsername() . "</strong></p>";

        try{
            $mail = $this->getMailService()->sendMail($user->getEmail(), $subject, $body);
            //Si le mail est resté en mode envoie, il passe en echec
            if($mail->getStatusEnvoi() == Mail::PENDING){
                $mail->setStatusEnvoi(Mail::FAILED);
                $this->getMailService()->update($mail);
            }
            if($mail->getStatusEnvoi() == Mail::FAILED){
                throw new Exception("Echec de l'envoie du mail");
            }
            $this->io->info("Un email a été envoyée à l'utilisateur afin qu'il génére sont mot de passe");
        }
        catch (Exception $e){
            $this->io->error("Echec de l'envoie du mail pour l'authentification : ".PHP_EOL.$e->getMessage());
            $url = $this->getInitPasswordURL($user);
            $this->io->info("Merci de lui transmettre l'URL suivante : ".$url." afin qu'il initialise sont mot de passe.");
        }
        return  $user;
    }

    private function getInitPasswordURL(User $user) : string
    {
        $uriHost = $this->uriHost;
        $uriScheme = $this->uriScheme;

        if(!isset($uriHost)||$uriHost==""){
            throw new RuntimeException("La configuration du paramétre uriHost de la console n'existe pas");
        }
        if(!isset($uriScheme)||$uriScheme==""){
            throw new RuntimeException("La configuration du paramétre uriScheme de la console n'existe pas");
        }
        if($user->getId()==null){
            throw new RuntimeException("L'identifiant de l'utilsateur n'est pas valide");
        }
        /** @see UtilisateurController::changerMotDePasseAction() */
        return sprintf('%s://%s/utilisateur/changer-mot-de-passe/%s/%s',
            $uriScheme, $uriHost,
            $user->getId(),
            $user->getPasswordResetToken()
        );

    }




    protected array $authentificationServices = [];
    //Doit être sous la forme d'un table ['db', 'ldap' ...];

    public function getAuthentificationServices(): array
    {
        return $this->authentificationServices;
    }

    public function setAuthentificationServices(array $authentificationServices): static
    {
        $this->authentificationServices = $authentificationServices;
        return $this;
    }

}
