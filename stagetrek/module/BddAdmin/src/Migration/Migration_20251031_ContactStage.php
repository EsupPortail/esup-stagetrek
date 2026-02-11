<?php

namespace BddAdmin\Migration;

use Unicaen\BddAdmin\Migration\MigrationAction;

class Migration_20251031_ContactStage extends MigrationAction {

    protected ?string $version=null;

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }


    public function description(): string
    {
        return "Autorise tout les contacts de terrains a recevoir la liste des étudiants affectés aux stages";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    { //Si la totalités des responsables n'est pas
        $version = $this->getVersion();
        if(!isset($version)){return false;}
//      ne s'applique que sur une version 1.7.* : sinon on suppose soit que c'est déjà fait, soit ce n'est pas nécessaire
        if(!str_contains($version, '1.7.')){return false;}
        //Que si aucun contact de terrains n'est autorisé à recevoir les mails d'affectation;
        $sql = "select * from contact_terrain where send_mail_auto_liste_etudiants_stage is true";
        $bdd = $this->getBdd();
        $exists = $bdd->selectOne($sql);
        return !$exists;
    }

    public function before():void
    {
    }

    public function after():void
    {
        $sql = "update contact_terrain set send_mail_auto_liste_etudiants_stage=true";
        $bdd = $this->getBdd();
        $bdd->exec($sql);
    }

}