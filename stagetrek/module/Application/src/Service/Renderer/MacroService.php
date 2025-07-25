<?php

namespace Application\Service\Renderer;

use Application\Service\Renderer\Traits\AdresseRendererServiceAwareTrait;
use Application\Service\Renderer\Traits\ContactRendererServiceAwareTrait;
use Application\Service\Renderer\Traits\ParametreRendererServiceAwareTrait;
use Application\Service\Renderer\Traits\PdfRendererServiceAwareTrait;
use Application\Service\Renderer\Traits\DateRendererServiceAwareTrait;
use Application\Service\Renderer\Traits\UrlServiceAwareTrait;

/** Surcouche du MacroService de UnicaenRender */
class MacroService extends \UnicaenRenderer\Service\Macro\MacroService
{
    //TODO : amélioration a apporter : gestion des macros qui peuvent être vide ou non selon le context

    // Accés aux différents service de remplacement pour le contenue des macros
    use ParametreRendererServiceAwareTrait;
    use DateRendererServiceAwareTrait;
    use AdresseRendererServiceAwareTrait;
    use UrlServiceAwareTrait;
    use ContactRendererServiceAwareTrait;
    use PdfRendererServiceAwareTrait;

    const CLASS_MACRO = 'macro';
    const CLASS_MACRO_NO_METHODE = 'macro-no-methode';
    const CLASS_MACRO_NO_VARIABLES = 'macro-no-variable';
    const CLASS_MACRO_NOT_NOT_DEFIND = 'macro-not-defind';

    /**
     * @desc Surcharge mais avec modifications des textes en cas d'erreur en utilisant les messages d'erreur
     * @param string|null $code
     * @param array $variables
     * @return string
     */
    public function getTexte(?string $code, array $variables = []): string
    {
        $code = str_replace('VAR[', '', $code);
        $code = str_replace(']', '', $code);
        $macro = $this->getMacroByCode($code);

        if ($macro === null) {
            return sprintf("<span class='%s %s' style='color:darkred;'> Macro [%s] non trouvée </span>",
                self::CLASS_MACRO, self::CLASS_MACRO_NOT_NOT_DEFIND, $code
            );
        }
        if (!isset($variables[$macro->getVariable()])) {
            return sprintf("<span class='%s %s' style='color:darkred;'> Variable [%s] non trouvée </span>",
                self::CLASS_MACRO, self::CLASS_MACRO_NO_VARIABLES, $macro->getVariable()
            );
        }
        if (!method_exists($variables[$macro->getVariable()], $macro->getMethode())) {
            return sprintf("<span class='%s %s' style='color:darkred;'> Méthode [%s] non trouvée </span>",
                self::CLASS_MACRO, self::CLASS_MACRO_NO_METHODE, $macro->getMethode()
            );
        }
        $texte = $variables[$macro->getVariable()]->{$macro->getMethode()}();
        return ($texte) ?: "";
    }

    /** @desc retourne la liste des macros présente dans un text plus celle non remplacée */
    public function findMacrosInText(?string $texteInitial): array
    {
        if(!isset($texteInitial)){return [];}
        if ($texteInitial == '') {
            return [];
        }
        $matches = [];
        preg_match_all('/VAR\[[a-zA-Z0-9_]*#[a-zA-Z0-9_]*\]/', $texteInitial, $matches);
        return array_unique($matches[0]);
    }

    /**
     * @param string|null $texteInitial
     * @param array $variables
     * @return string
     */
    public function replaceMacros(?string $texteInitial, array $variables = []): string
    {
        if(!isset($texteInitial)){return "";}
        if ($texteInitial == '') return "";
        $macros = $this->findMacrosInText($texteInitial);
        $replacements=[];
        if (empty($macros)) {
            return $texteInitial;
        }
        foreach ($macros as $macro) {
            $macroValue = $this->getTexte($macro, $variables);
            $replacements[$macro] = $macroValue;
        }
        return str_replace($macros, $replacements, $texteInitial);
    }

    //Détermine si du text contient une macro qui n'as pas été remplacé (ou qui n'as pas pu l'être)
    /** @deprecated TODO : a supprimer quand la gestions des macros dans les conventions sera revue
     */
    public function textContainsMacro(?string $text): bool
    {
        if(!isset($text)){return false;}
        if ($text == '') return false;
        //Macros dont le remplacement a échouée
        $patterns = [
            'VAR\[[a-zA-Z0-9_]*#[a-zA-Z0-9_]*\]',
            self::CLASS_MACRO,
            self::CLASS_MACRO_NO_METHODE,
            self::CLASS_MACRO_NO_VARIABLES,
            self::CLASS_MACRO_NOT_NOT_DEFIND
        ];
        foreach ($patterns as $pattern) {
            $matches = [];
            preg_match_all('/' . $pattern . '/', $text, $matches);
            $patterns_matches = array_unique($matches[0]);
            if (!empty($patterns_matches)) return true;
        }
        return false;
    }
}