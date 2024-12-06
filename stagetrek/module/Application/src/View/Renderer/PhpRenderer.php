<?php

namespace Application\View\Renderer;

use Application\Controller\Contrainte\ContrainteCursusController;
use Application\Entity\Db\Adresse;
use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\ConventionStage;
use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Faq;
use Application\Entity\Db\FaqCategorieQuestion;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\MessageInfo;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Db\NiveauEtude;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\ParametreCoutAffectation;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Entity\Db\Preference;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Source;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Form\Misc\View\Helper\FormControlText;
use Application\View\Helper\Adresse\AdresseViewHelper;
use Application\View\Helper\Affectation\AffectationViewHelper;
use Application\View\Helper\Annees\AnneeUniversitaireViewHelper;
use Application\View\Helper\BackButtonViewHelper;
use Application\View\Helper\Contacts\ContactStageViewHelper;
use Application\View\Helper\Contacts\ContactTerrainViewHelper;
use Application\View\Helper\Contacts\ContactViewHelper;
use Application\View\Helper\ContrainteCursus\ContrainteCursusEtudiantViewHelper;
use Application\View\Helper\Convention\ConventionViewHelper;
use Application\View\Helper\Convention\ModeleConventionViewHelper;
use Application\View\Helper\Disponibilite\DisponibiliteViewHelper;
use Application\View\Helper\Etudiant\EtudiantViewHelper;
use Application\View\Helper\Groupe\GroupeViewHelper;
use Application\View\Helper\Misc\AlertFlashViewHelper;
use Application\View\Helper\Misc\ConfirmationFormViewHelper;
use Application\View\Helper\Misc\PopAjaxConfirmationViewHelper;
use Application\View\Helper\Notification\FAQCategorieQuestionViewHelper;
use Application\View\Helper\Notification\FAQViewHelper;
use Application\View\Helper\Notification\MessageInfoViewHelper;
use Application\View\Helper\Parametres\NiveauEtudeViewHelper;
use Application\View\Helper\Parametres\ParametreCoutAffectationViewHelper;
use Application\View\Helper\Parametres\ParametreCoutTerrainViewHelper;
use Application\View\Helper\Parametres\ParametreViewHelper;
use Application\View\Helper\Preferences\PreferenceViewHelper;
use Application\View\Helper\Referentiel\ReferentielPromoViewHelper;
use Application\View\Helper\Referentiel\SourceViewHelper;
use Application\View\Helper\SessionsStages\SessionStageViewHelper;
use Application\View\Helper\Stages\CalendrierStageViewHelper;
use Application\View\Helper\Stages\StageViewHelper;
use Application\View\Helper\Terrains\CategorieStageViewHelper;
use Application\View\Helper\Terrains\TerrainStageViewHelper;
use Laminas\Form\ElementInterface;
use Laminas\Form\FormInterface;
use Laminas\Form\View\Helper\FormButton;
use Laminas\Form\View\Helper\FormCaptcha;
use Laminas\Form\View\Helper\FormCheckbox;
use Laminas\Form\View\Helper\FormCollection;
use Laminas\Form\View\Helper\FormColor;
use Laminas\Form\View\Helper\FormDateSelect;
use Laminas\Form\View\Helper\FormDateTimeLocal;
use Laminas\Form\View\Helper\FormElement;
use Laminas\Form\View\Helper\FormElementErrors;
use Laminas\Form\View\Helper\FormEmail;
use Laminas\Form\View\Helper\FormFile;
use Laminas\Form\View\Helper\FormHidden;
use Laminas\Form\View\Helper\FormImage;
use Laminas\Form\View\Helper\FormInput;
use Laminas\Form\View\Helper\FormLabel;
use Laminas\Form\View\Helper\FormMonth;
use Laminas\Form\View\Helper\FormMonthSelect;
use Laminas\Form\View\Helper\FormMultiCheckbox;
use Laminas\Form\View\Helper\FormNumber;
use Laminas\Form\View\Helper\FormPassword;
use Laminas\Form\View\Helper\FormRadio;
use Laminas\Form\View\Helper\FormRange;
use Laminas\Form\View\Helper\FormReset;
use Laminas\Form\View\Helper\FormRow;
use Laminas\Form\View\Helper\FormSearch;
use Laminas\Form\View\Helper\FormSelect;
use Laminas\Form\View\Helper\FormSubmit;
use Laminas\Form\View\Helper\FormTel;
use Laminas\Form\View\Helper\FormText;
use Laminas\Form\View\Helper\FormTextarea;
use Laminas\Form\View\Helper\FormTime;
use Laminas\Form\View\Helper\FormUrl;
use Laminas\Form\View\Helper\FormWeek;
use Laminas\Paginator\Paginator;
use Laminas\View\Helper\Cycle;
use Laminas\View\Helper\DeclareVars;
use Laminas\View\Helper\EscapeCss;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Helper\EscapeJs;
use Laminas\View\Helper\EscapeUrl;
use Laminas\View\Helper\Gravatar;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadStyle;
use Laminas\View\Helper\HeadTitle;
use Laminas\View\Helper\HtmlList;
use Laminas\View\Helper\HtmlTag;
use Laminas\View\Helper\Json;
use Laminas\View\Helper\Layout;
use Laminas\View\Helper\Partial;
use Laminas\View\Helper\Placeholder;
use Laminas\View\Helper\RenderToPlaceholder;
use Laminas\View\Helper\ViewModel;
use UnicaenApp\Entity\HistoriqueAwareInterface;
use UnicaenApp\Form\Element\Date;
use UnicaenApp\Form\Element\DateInfSup;
use UnicaenApp\Form\View\Helper\Form;
use UnicaenApp\Form\View\Helper\FormAdvancedMultiCheckbox;
use UnicaenApp\Form\View\Helper\FormControlGroup;
use UnicaenApp\Form\View\Helper\FormDate;
use UnicaenApp\Form\View\Helper\FormDateInfSup;
use UnicaenApp\Form\View\Helper\FormDateTime;
use UnicaenApp\Form\View\Helper\FormErrors;
use UnicaenApp\Form\View\Helper\FormLdapPeople;
use UnicaenApp\Form\View\Helper\FormRowDateInfSup;
use UnicaenApp\Form\View\Helper\FormSearchAndSelect;
use UnicaenApp\Form\View\Helper\MultipageFormFieldset;
use UnicaenApp\Form\View\Helper\MultipageFormRecap;
use UnicaenApp\Form\View\Helper\MultipageFormRow;
use UnicaenApp\Message\View\Helper\MessageHelper;
use UnicaenApp\View\Helper\AppInfos;
use UnicaenApp\View\Helper\ConfirmHelper;
use UnicaenApp\View\Helper\HeadLink;
use UnicaenApp\View\Helper\HeadScript;
use UnicaenApp\View\Helper\HistoriqueViewHelper;
use UnicaenApp\View\Helper\InlineScript;
use UnicaenApp\View\Helper\MessageCollectorHelper;
use UnicaenApp\View\Helper\Messenger;
use UnicaenApp\View\Helper\TabAjax\TabAjaxViewHelper;
use UnicaenApp\View\Helper\TagViewHelper;
use UnicaenApp\View\Helper\ToggleDetails;
use UnicaenApp\View\Helper\Upload\UploaderHelper;
use UnicaenAuthentification\View\Helper\AppConnection;
use UnicaenEvenement\Entity\Db\Evenement;

/**
 * Description of PhpRenderer
 *
 * Permet d'utiliser les aides de vue avec de l'auto-complétion et de rendre le Refactoring des aides de vues efficace
 *
 * Usage : pour toute les aides de vue, définir la méthode a appeler
 *
 * //Aides de vues de bases (héritées des vues de Zend ...
 * @method Cycle                                cycle(array $data = [], $name = 'default')
 * @method DeclareVars                          declarevars()
 * @method EscapeHtml                           escapehtml($value, $recurse = 0)
 * @method EscapeHtmlAttr                       escapehtmlattr($value, $recurse = 0)
 * @method EscapeJs                             escapejs($value, $recurse = 0)
 * @method EscapeCss                            escapecss($value, $recurse = 0)
 * @method EscapeUrl                            escapeurl($value, $recurse = 0)
 * @method Gravatar                             gravatar($email = '', $options = [], $attribs = [])
 * @method HtmlTag                              htmltag(array $attribs = [])
 * @method HeadMeta                             headmeta($content = null, $keyValue = null, $keyType = 'name', $modifiers = [], $placement = 'APPEND')
 * @method HeadStyle                            headstyle($content = null, $placement = 'APPEND', $attributes = [])
 * @method HeadTitle                            headtitle($title = null, $setType = null)
 * @method string                               htmlflash($data, array $attribs = [], array $params = [], $content = null)
 * @method HtmlList                             htmllist(array $items, $ordered = false, $attribs = false, $escape = true)
 * @method string                               htmlobject($data = null, $type = null, array $attribs = [], array $params = [], $content = null)
 * @method string                               htmlpage($data, array $attribs = [], array $params = [], $content = null)
 * @method string                               htmlquicktime($data, array $attribs = [], array $params = [], $content = null)
 * @method Json                                 json($data, array $jsonOptions = [])
 * @method Layout                               layout($templates = null)
 * @method string                               paginationcontrol(Paginator $paginator = null, $scrollingStyle = null, $partial = null, $params = null)
 * @method string                               partialloop($name = null, $values = null)
 * @method Partial                              partial($name = null, $values = null)
 * @method Placeholder                          placeholder($name = null)
 * @method string                               renderchildmodel($child)
 * @method RenderToPlaceholder                  rendertoplaceholder($script, $placeholder)
 * @method string                               serverurl($requestUri = null)
 * @method ViewModel                            viewmodel()
 * @method string|FormButton                    formbutton(ElementInterface $element = null, $buttonContent = null)
 * @method string|FormCaptcha                   formcaptcha(ElementInterface $element = null)
 * @method string                               captchadumb(ElementInterface $element = null)
 * @method string                               captchafiglet(ElementInterface $element = null)
 * @method string                               captchaimage(ElementInterface $element = null)
 * @method string                               captcharecaptcha(ElementInterface $element = null)
 * @method string|FormCheckbox                  formcheckbox(ElementInterface $element = null)
 * @method string|FormCollection                formcollection(ElementInterface $element = null, $wrap = true)
 * @method string|FormColor                     formcolor(ElementInterface $element = null)
 * @method string|FormDateTimeLocal             formdatetimelocal(ElementInterface $element = null)
 * @method string                               formdatetimeselect(ElementInterface $element = null, $dateType = 1, $timeType = 1, $locale = null)
 * @method string|FormDateSelect                formdateselect(ElementInterface $element = null, $dateType = 1, $locale = null)
 * @method string|FormElement                   formelement(ElementInterface $element = null)
 * @method string|FormElementErrors             formelementerrors(ElementInterface $element = null, array $attributes = [])
 * @method string|FormEmail                     formemail(ElementInterface $element = null)
 * @method string|FormFile                      formfile(ElementInterface $element = null)
 * @method string                               formfileapcprogress(ElementInterface $element = null)
 * @method string                               formfilesessionprogress(ElementInterface $element = null)
 * @method string                               formfileuploadprogress(ElementInterface $element = null)
 * @method string|FormHidden                    formhidden(ElementInterface $element = null)
 * @method string|FormImage                     formimage(ElementInterface $element = null)
 * @method string|FormInput                     forminput(ElementInterface $element = null)
 * @method string|FormLabel                     formlabel(ElementInterface $element = null, $labelContent = null, $position = null)
 * @method string|FormMonth                     formmonth(ElementInterface $element = null)
 * @method string|FormMonthSelect               formmonthselect(ElementInterface $element = null, $dateType = 1, $locale = null)
 * @method string|FormMultiCheckbox             formmulticheckbox(ElementInterface $element = null, $labelPosition = null)
 * @method string|FormNumber                    formnumber(ElementInterface $element = null)
 * @method string|FormPassword                  formpassword(ElementInterface $element = null)
 * @method string|FormRadio                     formradio(ElementInterface $element = null, $labelPosition = null)
 * @method string|FormRange                     formrange(ElementInterface $element = null)
 * @method string|FormReset                     formreset(ElementInterface $element = null)
 * @method string|FormRow                       formrow(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $partial = null)
 * @method string|FormSearch                    formsearch(ElementInterface $element = null)
 * @method string|FormSelect                    formselect(ElementInterface $element = null)
 * @method string|FormSubmit                    formsubmit(ElementInterface $element = null)
 * @method string|FormTel                       formtel(ElementInterface $element = null)
 * @method string|FormText                      formtext(ElementInterface $element = null)
 * @method string|FormTextarea                  formtextarea(ElementInterface $element = null)
 * @method string|FormTime                      formtime(ElementInterface $element = null)
 * @method string|FormUrl                       formurl(ElementInterface $element = null)
 * @method string|FormWeek                      formweek(ElementInterface $element = null)
 * @method string                               currencyformat($number, $currencyCode = null, $showDecimals = null, $locale = null, $pattern = null)
 * @method string                               dateformat($date, $dateType = -1, $timeType = -1, $locale = null, $pattern = null)
 * @method string                               numberformat($number, $formatStyle = null, $formatType = null, $locale = null, $decimals = null)
 * @method string                               plural($strings, $number)
 * @method string                               translate($message, $textDomain = null, $locale = null)
 * @method string                               translateplural($singular, $plural, $number, $textDomain = null, $locale = null)
 * @method string                               zenddevelopertoolstime($time, $precision = 2)
 * @method string                               zenddevelopertoolsmemory($size, $precision = 2)
 * @method string                               zenddevelopertoolsdetailarray($label, array $details, $redundant = false)
 * @method AppConnection                        appconnection()
 * @method Messenger                            messenger()
 * @method string                               modalajaxdialog($dialogDivId = null)
 * @method ConfirmHelper                        confirm($message = null)
 * @method ToggleDetails                        toggledetails($detailsDivId, $rememberState = true)
 * @method MultipageFormFieldset                multipageFormFieldset()
 * @method MultipageFormRow                     multipageFormRow(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $partial = null)
 * @method MultipageFormRecap                   multipageFormRecap()
 * @method FormControlGroup|string              formControlGroup(ElementInterface $element = null, $pluginClass = 'formElement')
 * @method FormDate                             formdate(Date $element = null, $dateReadonly = false)
 * @method FormDateTime                         formdatetime(ElementInterface $element = null)
 * @method FormDateInfSup                       formdateinfsup(DateInfSup $element = null, $dateInfReadonly = false, $dateSupReadonly = false)
 * @method FormRowDateInfSup                    formrowdateinfsup(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $partial = null)
 * @method FormSearchAndSelect                  formsearchandselect(ElementInterface $element = null)
 * @method FormLdapPeople                       formldappeople(ElementInterface $element = null)
 * @method FormErrors                           formerrors(\Laminas\Form\Form $form = null, $message = null)
 * @method Form                                 form(FormInterface $form = null)
 * @method MessageCollectorHelper               messagecollector($severity = null)
 * @method HeadScript                           headscript($mode = 'FILE', $spec = null, $placement = 'APPEND', array $attrs = [], $type = 'text/javascript')
 * @method InlineScript                         inlinescript($mode = 'FILE', $spec = null, $placement = 'APPEND', array $attrs = [], $type = 'text/javascript')
 * @method HeadLink                             headlink(array $attributes = null, $placement = 'APPEND')
 * @method UploaderHelper                       uploader()
 * @method FormAdvancedMultiCheckbox            formadvancedmulticheckbox(ElementInterface $element = null, $labelPosition = null)
 * @method HistoriqueViewHelper                 historique(HistoriqueAwareInterface $entity = null)
 * @method TabAjaxViewHelper                    tabajax($tabs = null)
 * @method TagViewHelper                        tag($name = null, array $attributes = [])
 * @method MessageHelper                        message()
 * @method AppInfos                             appInfos()
 *
 * @method boolean                              isAllowed($resource, $privilege = null)
 *
 * @method FlashMessageDisplayViewHelper                flashMessageDisplay()
 * @method AlertFlashViewHelper                 alertFlash()
 * @method PopAjaxConfirmationViewHelper        popAjaxConfirmation($href, $html, $popAjax = [], $class = [], $title = null, $event = null)
 * @method ConfirmationFormViewHelper           confirmation($form)
 * @method string|FormControlText               formControlText(ElementInterface $element = null)
 * @method BackButtonViewHelper                 backButton($libelle = null)
 *
 * @method SourceViewHelper                     source(?Source $source = null)
 * @method ReferentielPromoViewHelper           referentielPromo(?ReferentielPromo $referentielPromo = null)
 * @method AffectationViewHelper                affectationStage(AffectationStage $affectationStage = null, SessionStage $session = null, Etudiant $etudiant = null)
 * @method ContrainteCursusController        administrationContrainteCursus(Etudiant $etudiant = null)
 * @method ContrainteCursusEtudiantViewHelper           contrainteCursus(ContrainteCursusEtudiant $etudiant = null)
 * @method CategorieStageViewHelper             categorieStage(CategorieStage $categorieStage = null)
 * @method DisponibiliteViewHelper              disponibilite(Disponibilite $disponibilite = null)
 * @method NiveauEtudeViewHelper                niveauEtude(NiveauEtude $niveauEtude = null)
 * @method ParametreViewHelper                  parametre(Parametre $parametre = null)
 * @method ParametreCoutAffectationViewHelper   parametreCoutAffectation(ParametreCoutAffectation $parametre = null)
 * @method ParametreCoutTerrainViewHelper       parametreCoutTerrain(ParametreTerrainCoutAffectationFixe $parametre = null)
 * @method EtudiantViewHelper                   etudiant(Etudiant $etudiant = null)
 * @method AnneeUniversitaireViewHelper         anneeUniversitaire(AnneeUniversitaire $anneeUniversitaire = null)
 * @method CalendrierStageViewHelper            calendrierStage(AnneeUniversitaire $anneeUniversitaire = null)
 * @method GroupeViewHelper                     groupe(Groupe $groupe = null)
 * @method SessionStageViewHelper               sessionStage(SessionStage $session = null)
 * @method StageViewHelper                      stage(Stage $stage = null)
 * @method TerrainStageViewHelper               terrainStage(TerrainStage $terrainStage = null)
 * @method PreferenceViewHelper                 preference(Preference $preference = null)
 * @method ConventionViewHelper                 convention(ConventionStage $convention = null)
 * @method ModeleConventionViewHelper           modeleConvention(ModeleConventionStage $modele = null)
 * @method AdresseViewHelper                    adresse(?Adresse $adresse = null)
 * //A revoir
 * @method ContactViewHelper                    contact(Contact $contact = null)
 * @method ContactTerrainViewHelper             contactTerrain(ContactTerrain $contactTerrain = null)
 * @method ContactStageViewHelper               contactStage(ContactStage $contactStage = null)
 *
 * @method MessageInfoViewHelper                messageInfo(?MessageInfo $messageInfo = null)
 * @method FAQViewHelper                        faq(Faq $question = null)
 * @method FAQCategorieQuestionViewHelper       categorieFaq(FaqCategorieQuestion $categorie = null)
 */
class PhpRenderer extends \Laminas\View\Renderer\PhpRenderer
{
}