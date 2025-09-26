
<?php
/**
 * Configuration pour l'import des étudiants depuis une source externe
 * (repose en grande partie sur unicaen/db-import)
 */

use Application\Entity\Db\Source;
use Application\Form\Referentiel\CSVImportEtudiantsForm;
use Application\Form\Referentiel\ReferentielImportEtudiantsForm;
use Application\Service\Referentiel\CsvEtudiantService;
use Application\Service\Referentiel\LdapEtudiantService;
use Application\Service\Referentiel\ReferentielDbImportEtudiantService;
use Doctrine\DBAL\Driver\OCI8\Driver as OCI8;
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver as PostgreDriver;
use UnicaenDbImport\Filter\ColumnValue\ConcatColumnValueFilter;

$refExteneCode=($_ENV['REF_ETUDIANT_SOURCE_CODE']) ?? null;
$ldapActif = (isset($_ENV['LDAP_REPLICA_HOST']) && ($_ENV['LDAP_REPLICA_HOST']) != "");

$config = [];
if(isset($refExteneCode) && $refExteneCode != ""){
    $config['referentiels']['forms'][$refExteneCode] = ReferentielImportEtudiantsForm::class;
    $config['referentiels']['import_services'][$refExteneCode] = ReferentielDbImportEtudiantService::class;

    //Connection vers la BDD du référentiel
    $refDbDriver =  ($_ENV['REF_ETUDIANT_DRIVER']) ?? "Postgresql";
    $driver = "Non-Configuré";
    switch ($refDbDriver) {
        case "Oracle":
        case "OCI8":
            $driver = OCI8::class;
            break;
        case "Postgresql":
        default :
            $driver =PostgreDriver::class;
    }
    $stagetrekConnection='connection_stagetrek';
    $refConnection='orm_referentiel_etudiant';
    $config['doctrine']['connection'][$refConnection] =
            //Paramétre du lien vers la base source
           [
                'driverClass' => $driver,
                'params' => [
                    //Versions BD Local
                    'host' => ($_ENV['REF_ETUDIANT_DB_HOST']) ?? "Non-configuré",
                    'dbname' => ($_ENV['REF_ETUDIANT_DB_NAME']) ?? "Non-configuré",
                    'port' => ($_ENV['REF_ETUDIANT_DB_PORT']) ?? "Non-configuré",
                    'charset' => 'utf8',
                    'user' => ($_ENV['REF_ETUDIANT_DB_USER']) ?? "Non-configuré",
                    'password' => ($_ENV['REF_ETUDIANT_DB_PSWD']) ?? "Non-configuré",
            ],
        ];

    //Configuration de db-import
    $refTableSource=($_ENV['REF_ETUDIANT_TABLE_SOURCE']) ?? "Non-configuré";
    $defaultCodeAnnee = (new DateTime())->format('Y');
    $config['import']['connections'] = [
        $stagetrekConnection => 'doctrine.connection.orm_default',
        $refConnection => 'doctrine.connection.orm_referentiel_etudiant',
    ];

    //Config de l'import (=copie de la table soucre dans une table destination temporaire
    $config['import']['imports']['etudiant'] = [    //Table / Vue de la source
        'name' => ReferentielDbImportEtudiantService::IMPORT_NAME,
        'order' =>  0,
        'source' => [
            'connection' => $refConnection,
            'code' => $refExteneCode,
            'name' => 'Référentiel étudiants',
            'table' => $refTableSource,
            'source_code_column' => 'code',
            'computed_columns' => [
                'code' => ['name' => ConcatColumnValueFilter::class, 'params' => ['column' => 'code', 'columns' => ['code_annee','code_vet', 'num_etu'], 'separator' => "_"]],
//                            'source_code' => ['name' => ConcatColumnValueFilter::class, 'params' => ['column' => 'code', 'columns' => ['code_annee','code_vet', 'num_etu'], 'separator' => "_"]],
            ],
            'where' => sprintf("code_annee='%s'", $defaultCodeAnnee),
        ],
        //Table de destination de l'import
        'destination' => [
            'connection' => $stagetrekConnection,
            'name' => "Table d'import des étudiants",
            'table' => ReferentielDbImportEtudiantService::IMPORT_TABLE_DESTINATION,
            'source_code_column' => 'code',
            'id_strategy' => null,
            'id_sequence' => null,
        ],
    ];

    //Config de la synchro  (=diff entre les données de la table d'import et la table des étudiants)
    $config['import']['synchros']['etudiant'] = [
        'name' => ReferentielDbImportEtudiantService::SYNCHRO_NAME,
        'order' =>  0,
        'source' => [
            'connection' => $stagetrekConnection,
            'code' => $refExteneCode,
            'name' => 'Synchro avec le référentiel étudiants',
            'table' => ReferentielDbImportEtudiantService::SYNCHRO_TABLE_SOURCE,
            'source_code_column' => 'source_code',
        ],
        'destination' => [
            'connection' => $stagetrekConnection,
            'name' => 'Table etudiant',
            'table' => ReferentielDbImportEtudiantService::SYNCHRO_TABLE_DESTINATION,
            'source_code_column' => 'source_code',
            'id_strategy' => 'SEQUENCE',
            'id_sequence' => 'etudiant_id_seq',
        ],
    ];
}

if($ldapActif){
    $config['referentiels']['forms'][Source::LDAP] = ReferentielImportEtudiantsForm::class;
    $referentiels['referentiels']['import_services'][Source::LDAP] = LdapEtudiantService::class;
}

$config['referentiels']['forms'][Source::CSV] = CSVImportEtudiantsForm::class;
$config['referentiels']['import_services'][Source::CSV] = CsvEtudiantService::class;

return $config;
