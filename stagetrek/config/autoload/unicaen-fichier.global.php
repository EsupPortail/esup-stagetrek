<?php

use Fichier\Filter\FileName\NatureBasedFileNameFormatter;
use UnicaenStorage\Adapter\FilesystemStorageAdapter;
use UnicaenStorage\Adapter\S3StorageAdapter;

$storageType = ($_ENV['STORAGE_TYPE']) ?? 'local';
$storageConfig = [];
switch ($storageType) {
    case 'local' :
        $storageConfig['adapter'] = FilesystemStorageAdapter::class;
    break;
    case 's3' :
        $storageConfig['adapter'] = S3StorageAdapter::class;
    break;
}

if(isset($_ENV['LOCAL_FILE_STORAGE']) && $_ENV['LOCAL_FILE_STORAGE'] !== '' ){
    $storageConfig['adapters'][FilesystemStorageAdapter::class]['root_path'] = $_ENV['LOCAL_FILE_STORAGE'];
}

if(isset($_ENV['AWS_ENDPOINT']) && $_ENV['AWS_ENDPOINT'] !== '') {
    $storageConfig['adapters'][S3StorageAdapter::class]['client']['end_point'] = $_ENV['AWS_ENDPOINT'];

    if(isset($_ENV['AWS_ACCESS_KEY_ID'])){
        $storageConfig['adapters'][S3StorageAdapter::class]['client']['access_key'] = $_ENV['AWS_ACCESS_KEY_ID'];
    }
    if(isset($_ENV['AWS_SECRET_ACCESS_KEY'])){
        $storageConfig['adapters'][S3StorageAdapter::class]['client']['secret_key'] = $_ENV['AWS_SECRET_ACCESS_KEY'];
    }
    if(isset($_ENV['AWS_BUCKET_NAME'])){
        $storageConfig['adapters'][S3StorageAdapter::class]['root_path'] = $_ENV['AWS_BUCKET_NAME'];
    }

    $storageConfig['adapters'][S3StorageAdapter::class]['client']['version'] = ($_ENV['AWS_VERSION']) ?? 'latest';
    $storageConfig['adapters'][S3StorageAdapter::class]['client']['region'] = ($_ENV['AWS_DEFAULT_REGION']) ?? 'eu-sxb-1';

}

return [

    'fichier' => [
        //Permet de fournir une configuration spécifique pour les formulaire d'upload de fichier
        'uplpoad' => [
            'max-size' => '2MB',
            'extentions' => ['pdf', 'csv'], // Extention des fichiers autorisée
            //Pour les csv typeMine = text/plain
            'type-mine' => ['application/pdf', 'text/plain'],
            'validators' => [],
        ],

        'file-name-formatter' => NatureBasedFileNameFormatter::class,
        'storage' =>  $storageConfig,
    ],

];