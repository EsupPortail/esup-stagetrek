<?php

namespace Application\View\Helper\Interfaces\Implementation;

use Laminas\View\Renderer\RendererInterface as Renderer;

/**
 * @method Renderer getView()
 */
trait EtudiantActionViewHelperTrait
{
    protected bool $vueEtudianteActive = false;

    public function vueEtudianteActive(): bool
    {
        return $this->vueEtudianteActive;
    }

    public function setVueEtudiante(bool $active): static
    {
        $this->vueEtudianteActive = $active;
        return $this;
    }

}