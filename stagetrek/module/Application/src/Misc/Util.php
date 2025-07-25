<?php

namespace Application\Misc;

use DateTime;
use RandomLib\Factory as RandomLib;
use function Application\recursive_array_diff;
use function strtr;

class Util extends \UnicaenApp\Util
{
    /**
     * Liste des caractères utilisés pour générer une chaîne de caractères aléatoire
     *
     * @var string
     */
    private static $codeCharacters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';

    /**
     * Liste des nombres utilisés pour générer un code numérique
     *
     * @var string
     */
    private static $codeNumbers = '0123456789';

    /**
     * Variable nécessaire au bon fonctionnement de la méthode statique @see unique_object()
     *
     * @var array
     */
    private static $unique_object_list = [];

    /**
     * Util constructor.
     */
    public function __construct()
    {
        // on réinitialise le tableau statique pour chaque nouvelle instanciation de la classe
        static::$unique_object_list = [];
    }

    /**
     * Supprime les caractères accentués d'une chaîne de caractères
     *
     * @param $string
     *
     * @return string
     */
    public static function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        $chars = [
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        ];

        $string = strtr($string, $chars);

        return $string;
    }

    /**
     * Convertit une chaîne de caractères en "Camel case"
     *
     * @param $str
     * @param $noStrip
     *
     * @return String
     */
    public static function camelCase($str, array $noStrip = array())
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

    /**
     *  Génère une chaîne de caractères d'une longueur spécifiée
     *
     * @param int $length taille de la chaîne de caractères
     * @return string
     */
    static public function randomString($length = 10)
    {
        $factory = new RandomLib();
        $generator = $factory->getMediumStrengthGenerator();
        return $generator->generateString($length, self::$codeCharacters);
    }

    /**
     * Génère une chaîne de chiffres d'une longueur spécifiée
     *
     * @param int $length
     * @return string
     */
    static public function randomNumber($length = 6)
    {
        $factory = new RandomLib();
        $generator = $factory->getMediumStrengthGenerator();
        return $generator->generateString($length, self::$codeNumbers);
    }

    /**
     * Formate l'affichage d'un nom ou un prénom
     *
     * @param $data
     * @return string
     */
    static public function formatName($data)
    {
        $encoding = 'UTF-8';
        return ucwords(
            sprintf('%s%s',
                mb_strtoupper(mb_substr($data, 0, 1), $encoding),
                mb_strtolower(mb_substr($data, 1), $encoding)),
            " \t\r\n\f\v-"
        );
    }

    /**
     * Formate une date dans la bonne locale
     *
     * @param \DateTime $dateTime
     * @param string $format @see http://userguide.icu-project.org/formatparse/datetime.
     * @param string $locale
     * @return string
     */
    static public function formatIntlDateTime(\DateTime $dateTime, $format = 'dd/MM/yyyy', $locale = 'fr_FR.UTF-8')
    {
        $formatter = \IntlDateFormatter::create($locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $formatter->setPattern($format);

        return $formatter->format($dateTime);
    }

    /**
     * Test l'unicité d'un objet
     * À utiliser avec la fonction @param $obj
     * @return bool
     * @see array_filter
     *
     * array_filter(array $array, [new Util(), 'unique_object'])
     *
     */
    static public function unique_object($obj)
    {
        if ($obj == null) {
            return false;
        }

        if (in_array($obj->getId(), self::$unique_object_list)) {
            return false;
        }

        self::$unique_object_list[] = $obj->getId();
        return true;
    }

    /**
     * Permet la comparaison de tableaux ou d'objets
     *
     * Exemple :
     * $obj1 = new StdClass; $obj1->prop = array(1,2);
     * $obj2 = new StdClass; $obj2->prop = array(1,3);
     * print_r(recursive_array_diff((array)$obj1, (array)$obj2));
     *
     * @param $a1
     * @param $a2
     * @return array
     */
    static public function recursive_array_diff($a1, $a2)
    {
        $r = array();
        foreach ($a1 as $k => $v) {
            if (array_key_exists($k, $a2)) {
                if (is_array($v)) {
                    $rad = recursive_array_diff($v, $a2[$k]);
                    if (count($rad)) {
                        $r[$k] = $rad;
                    }
                } else {
                    if ($v != $a2[$k]) {
                        $r[$k] = $v;
                    }
                }
            } else {
                $r[$k] = $v;
            }
        }
        return $r;
    }


//    const POINT_MEDIANT = '&#183;';
//    const POINT_MEDIANT = '&middot;';
    const POINT_MEDIANT = '·'; //marche y compris avec la fonction escapehtml

//    Est-ce que la date courante est avant la date fournis
    public static function todayIsBefore(?DateTime $date, bool $strict=false) : bool
    {
        if(!isset($date)){return false;}
        $today = new DateTime();
        if($strict){return  $today < $date;}
        else{return $today <= $date;}
    }
    public static function todayIsAfter(?DateTime $date, bool $strict=false) : bool
    {
        return !self::todayIsBefore($date, !$strict);
    }
    public static function todayIsBetween(?DateTime $date1, ?DateTime $date2, bool $strict1=false, bool $strict2=false) : bool
    {
        return self::todayIsAfter($date1, $strict1) && self::todayIsBefore($date2, $strict2);
    }
}