<?php

namespace Application\View\Helper\Misc;
use Application\Entity\Db\Parametre;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Exception;
use Laminas\View\Helper\AbstractHelper;

class FooterViewHelper extends AbstractHelper
{
    use ParametreServiceAwareTrait;

    /**
     * @return $this
     */
    public function __invoke(): static
    {
        return $this;
    }


    /**
     * @throws \Exception
     */
    public function __toString() : string
    {
        return $this->render();
    }

    public function render() : string
    {;
        return $this->getView()->render('layout/footer');
    }

    public function renderLogos() : string
    {
        $service = $this->getParametreService();
        try{
        $name = $service->getParametreValue(Parametre::FOOTER_UNIV_NAME);        }
        catch (Exception){
            $name =null;
        }
        try{
            $url =  $service->getParametreValue(Parametre::FOOTER_UNIV_URL);        }
        catch (Exception){
            $url =null;
        }
        try{
            $logo = $service->getParametreValue(Parametre::FOOTER_UNIV_LOGO);        }
        catch (Exception){
            $logo =null;
        }

        $rf = "<img src='/unistrap-1.0.0/img/logo/republique-francaise.svg' width='110' height='100' class='logo-rf' alt=''>";

        if(!isset($logo) || $logo==""){return $rf;}
        $univ = sprintf("<img src='%s'  width='160' class='logo-universite' title='%s'>",
            $logo, (isset($name) && $name != "") ? $this->view->escapehtml($name) : ""
        );
        if(isset($url) && $url != ""){
            $univ = sprintf("<a href='%s'>%s</a>", $url, $univ);
        }
        $html = sprintf("%s %s", $rf, $univ);
        return $html;
    }

       public function renderMenuPiedDePage() : string
    {
        $service = $this->getParametreService();
        try{
            $univName = $service->getParametreValue(Parametre::FOOTER_UNIV_NAME);
        }
        catch (Exception){
            $univName =null;
        }
        try{
            $univURl =  $service->getParametreValue(Parametre::FOOTER_UNIV_URL);
        }
        catch (Exception){
            $univURl =null;
        }
        try{
            $contactUrl =  $service->getParametreValue(Parametre::FOOTER_UNIV_CONTACT);
        }
        catch (Exception){
            $contactUrl =null;
        }
        try{
            $viePrivee =  $service->getParametreValue(Parametre::FOOTER_UNIV_VIE_PRIVEE);
        }
        catch (Exception){
            $viePrivee =null;
        }
        try{
            $mentionsLegales =  $service->getParametreValue(Parametre::FOOTER_UNIV_MENTIONS_LEGALS);
        } catch (Exception) {
            $mentionsLegales = null;
        }

        $html ="<ul class='navigation'>";
        if(isset($univName) && $univName != "" && isset($univName) && $univURl != "") {
            $html .= sprintf("<li><a href='%s'  title='%s' target='blank'>%s</a></li>", $univURl,  $this->view->escapeHtml($univName), $univName);
        }
        $html .= sprintf("<li><a href='%s' class='apropo' title='%s' target='blank'>%s</a></li>", '/apropos', $this->view->escapeHtml("À propos de l'application"), 'À  propos');
        $html .= sprintf("<li><a href='%s' class='plan' title='%s' target='blank'>%s</a></li>", '/plan', $this->view->escapeHtml("Page de navigation au sein de l'application"), ' Plan de navigation');

        if(isset($contactUrl) && $contactUrl != "") {
            $html .= sprintf("<li><a href='%s' title='%s' target='blank'>%s</a></li>", $contactUrl,  $this->view->escapeHtml("Contact"), 'Contact');
        }
        if(isset($mentionsLegales) && $mentionsLegales != "") {
            $html .= sprintf("<li><a href='%s' title='%s' target='blank'>%s</a></li>", $mentionsLegales,  $this->view->escapeHtml("Mentions légales"), 'Mentions légales');
        }
        if(isset($viePrivee) && $viePrivee != "") {
            $html .= sprintf("<li><a href='%s' title='%s' target='blank'>%s</a></li>", $viePrivee,  $this->view->escapeHtml("Vie privée"), 'Vie privée');
        }
        $html .="</ul>";
        return $html;
    }


}