<?php

namespace Fichier\Form\Upload;

trait UploadFormAwareTrait {

    /** @var UploadForm $uploadForm */
    private $uploadForm;

    /**
     * @return UploadForm
     */
    public function getUploadForm()
    {
        return $this->uploadForm;
    }

    /**
     * @param UploadForm $uploadForm
     * @return UploadForm
     */
    public function setUploadForm($uploadForm)
    {
        $this->uploadForm = $uploadForm;
        return $this->uploadForm;
    }


}