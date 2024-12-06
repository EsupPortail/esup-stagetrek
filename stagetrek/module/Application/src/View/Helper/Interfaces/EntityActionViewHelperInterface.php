<?php

namespace Application\View\Helper\Interfaces;

use Laminas\Form\Form;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

interface EntityActionViewHelperInterface
{

    public function __toString() : string;
    public function render() : string;

    function renderListe(?array $entities = [], ?array $params = []) : string;
    function renderForm(Form $form) : string;

    function actionAllowed(string $action): bool;
    function callAssertion(ResourceInterface $resource, string $privilege = null): bool;
    function hasPrivilege(?string $privilege): bool;

    function getUrl($name = null, array $params = array(), $options = array(), $reuseMatchedParams = false) :string;
    function generateActionLink(?string $url=null, ?string $libelle=null, array $attributes=[]) : string;


}