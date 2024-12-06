<?php

namespace Application\Misc;

use DateTime;

/**
 * Version
 */
class Version
{
    /**
     * Liste des commandes utilisées
     */
    static protected $branch = 'git rev-parse --abbrev-ref HEAD';
    static protected $last_commit_hash_short = 'git log --pretty="%h" -n1 HEAD';
    static protected $last_commit_hash_long = 'git log --pretty="%H" -n1 HEAD';
    static protected $last_commit_date = 'git log --pretty="%ci" -n1 HEAD';
    static protected $last_commit_complete = 'git describe --always';
    static protected $last_tag_commit_hash = 'git rev-list --tags --max-count=1';

    static protected $last_tag_commit_date_pattern = 'git log --pretty="%ci" -n1 <hash>';
    static protected $last_tag_number_pattern = 'git describe --tags <hash>';
    static protected $revision_pattern = 'git rev-list <tag>.. --count';


    /**
     * Branche git
     *
     * @return string
     */
    public static function getBranch()
    {
        return trim(exec(self::$branch));
    }

    /**
     * Date de version
     *
     * @return DateTime
     * @throws \Exception
     */
    public static function getDate()
    {
        $commitHash = trim(exec(self::$last_tag_commit_hash));
        $commitDate = new DateTime(self::getTagDate($commitHash));
        return $commitDate;
    }

    private static function getTagDate($hash)
    {
        return trim(exec(preg_replace('/<hash>/', $hash, self::$last_tag_commit_date_pattern)));
    }

    /**
     * Version complète du dernier commit
     * Format : <version>-<revision>-g<hash>
     *
     * @return string
     */
    public static function getLastCommitComplete()
    {
        return trim(exec(self::$last_commit_complete));
    }

    /**
     * Hash du dernier commit
     *
     * @param string $size
     * @return string
     */
    public static function getLastCommitHash($size = 'short')
    {
        switch ($size) {
            case 'short':
                $hash = exec(self::$last_commit_hash_short);
                break;
            case 'long':
            default:
                $hash = exec(self::$last_commit_hash_long);
                break;
        }

        return trim($hash);
    }

    /**
     * Date du dernier commit
     *
     * @return DateTime
     * @throws \Exception
     */
    public static function getLastCommitDate()
    {
        return new DateTime(trim(exec(self::$last_commit_date)));
    }

    /**
     * Révision depuis le dernier tag
     *
     * @return string
     */
    public static function getRevision()
    {
        $commitHash = trim(exec(self::$last_tag_commit_hash));
        return trim(exec(preg_replace('/<tag>/', self::getTagNumber($commitHash), self::$revision_pattern)));
    }

    private static function getTagNumber($hash)
    {
        return trim(exec(preg_replace('/<hash>/', $hash, self::$last_tag_number_pattern)));
    }

    /**
     * Numéro de version
     *
     * @return string
     */
    public static function getTag()
    {
        $commitHash = trim(exec(self::$last_tag_commit_hash));
        return self::getTagNumber($commitHash);
    }
}
