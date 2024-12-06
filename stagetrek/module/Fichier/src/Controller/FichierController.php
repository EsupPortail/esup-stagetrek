<?php

namespace Fichier\Controller;

use Fichier\Entity\Db\Fichier;
use Fichier\Form\Upload\UploadFormAwareTrait;
use Fichier\Service\Fichier\FichierServiceAwareTrait;
use Fichier\Service\Nature\NatureServiceAwareTrait;
use Fichier\Service\S3\S3ServiceAwareTrait;
use Laminas\Form\Element\Select;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class FichierController extends AbstractActionController {

    const ACTION_UPlOAD = 'upload';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_HISTORISER = 'historiser';
    const ACTION_RESTAURER = 'restaurer';
    const ACTION_DELETE = 'delete';

    use NatureServiceAwareTrait;
    use UploadFormAwareTrait;
    use FichierServiceAwareTrait;

    /**
     * @return \Laminas\View\Model\ViewModel
     */
    public function uploadAction() : ViewModel
    {
        $natureCode = $this->params()->fromRoute('nature');
        if(isset($natureCode)) {
            $nature = $this->getNatureService()->getNatureByCode($natureCode);
        }

        $fichier = new Fichier();
        $form = $this->getUploadForm();
        $form->setAttribute('action', $this->url()->fromRoute('fichier/upload',[] , [], true));
        $form->bind($fichier);

        if (isset($nature)) {
            /** @var Select $select */
            $select = $form->get('nature');
            $select->setValueOptions([ $nature->getId() => $nature->getLibelle()]);
        }

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $natureId = ($data['nature']) ?? null;
                $file = ($data['fichier']) ?? null;
                if ($file['name'] != '') {
                    $nature = $this->getNatureService()->getNature($natureId);
                    $this->getFichierService()->createFichierFromUpload($file, $nature);
                }
                exit();
            }
        }

        $vm =  new ViewModel();
        $vm->setVariables([
            'title' => 'Téléversement d\'un fichier',
            'form' => $form,
        ]);
        return $vm;
    }

    public function downloadAction() : Response|ViewModel
    {
        $fichier = $this->getFichierService()->getRequestedFichier($this);
        if (!$fichier) {
            return $this->notFoundAction();
        }
        $contentType = $fichier->getTypeMime() ?: 'application/octet-stream';
        $contenuFichier = $this->getFichierService()->getStorageFileContent($fichier);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename=' . $fichier->getNomOriginal());
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $fichier->getTaille());
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');

        echo $contenuFichier;
        exit;
    }

    /**
     * @throws \Exception
     */
    public function deleteAction() : Response|ViewModel
    {
        $retour  = $this->params()->fromQuery('retour');
        $fichier = $this->getFichierService()->getRequestedFichier($this);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") {
                if ($fichier) $this->getFichierService()->delete($fichier);
            }

            if ($retour) {
                return $this->redirect()->toUrl($retour);
            }
            exit();
        }

        $vm = new ViewModel();
        if ($fichier !== null) {
            $vm->setTemplate('fichier/default/confirmation');
            $vm->setVariables([
                'title' => "Suppression du fichier " . $fichier->getNomOriginal(),
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('fichier/supprimer', ["fichier" => $fichier->getId()], [], true),
            ]);
        }
        return $vm;
    }

    public function historiserAction() : Response
    {
        $fichier = $this->getFichierService()->getRequestedFichier($this);
        $this->getFichierService()->historise($fichier);

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);
        exit();
    }

    public function restaurerAction() : Response
    {
        $fichier = $this->getFichierService()->getRequestedFichier($this);
        $this->getFichierService()->restore($fichier);

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);
        exit();
    }
}