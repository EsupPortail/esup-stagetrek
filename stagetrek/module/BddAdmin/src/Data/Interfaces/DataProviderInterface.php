<?php

namespace BddAdmin\Data\Interfaces;
interface DataProviderInterface
{
    /** Retourne la configuration pour la mise à jours de la table nommée
     * $config permet de surchargée certains paramétres
     */
    static public function getConfig(string $table, array $config = []): array;

//    La classe doit également fournir une fonction table_name() : array pour chaque table dont ont souhaite importer les données
}