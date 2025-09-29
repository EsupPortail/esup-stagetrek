<?php

namespace Application\Form\Misc\Interfaces;

interface HasTagInputInterface
{
    CONST TAGS = 'tags';
    function initTagsInputs() : static;
    function getTagsAvailables() : array;
    function setTagsAvailables(array $tags) : static;
}