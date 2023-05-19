<?php

declare(strict_types=1);

/**
 * This file is part of a BugBuster Contao Bundle
 *
 * @copyright  Glen Langer 2022 <https://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @license    LGPL-3.0-or-later
 * @see	       https://github.com/BugBuster1701/contao-lastlogin-bundle
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */

namespace BugBuster\LastLogin;

/**
 * Class LastLogin
 * 
 * You can use prefix "cache_". The InserTag will be not cached nows. (when "cache" is enabled)
 * 
 * Last Login:
 * {{cache_last_login}}
 * {{cache_last_login::d.m.Y}}
 * {{cache_last_login::zero}}
 * {{cache_last_login::zero::d.m.Y}}
 * 
 * Display number of registered members
 * {{cache_last_login_number_registered_members}}
 * 
 * Display number of online members
 * {{cache_last_login_number_online_members}}
 * 
 * Display number of offline members (logout today)
 * {{cache_last_login_number_offline_members}}
 */
class LastLogin extends \Contao\Frontend
{
    /**
     * Array with splitted Tag parts
     * @var mixed
     */
    private $arrTag = false;

    /**
     * Login check needed
     * @var bool
     */
    // private $login_check = false;

    /**
     * LastLogin Replace Insert-Tag Main Methode
     * @param  string $strTag
     * @return mixed  false: no correct tag
     *                       int: return value of the Insert-Tag
     */
    public function ReplaceInsertTagsLastLogin($strTag)
    {
        // if (true === \Contao\System::getContainer()->get('contao.security.token_checker')->hasFrontendUser()) {
        //     $this->login_check = true;
        // }	
        // if (isset($GLOBALS['TL_CONFIG']['mod_lastlogin_login_check']) &&
        //           $GLOBALS['TL_CONFIG']['mod_lastlogin_login_check'] === false) 
        // {
        //     $this->login_check = true;
        // }

        $this->arrTag = \Contao\StringUtil::trimsplit('::', $strTag);
        switch ($this->arrTag[0]) 
        {
            case "last_login":
            case "cache_last_login":
                return $this->getLastLogin();
                break;
            case "last_login_number_registered_members":
            case "cache_last_login_number_registered_members":
                return $this->getLastLoginNumberRegisteredMembers();
                break;
            case "last_login_number_online_members":
            case "cache_last_login_number_online_members":
                return $this->getLastLoginNumberOnlineMembers();
                break;
            case "last_login_number_offline_members":
            case "cache_last_login_number_offline_members":
                return $this->getLastLoginNumberOfflineMembers();
                break;
            default:
                //not for me
                return false;
        }
    } //function ReplaceInsertTagsLastLogin

    /**
     * Insert-Tag: Last Login
     * @return mixed false: FE user not logged in
     *               string: return value of the Insert-Tag
     */
    private function getLastLogin()
    {
        //member last login
        // {{cache_last_login}}
        // {{cache_last_login::d.m.Y}}
        // {{cache_last_login::zero}}
        // {{cache_last_login::zero::d.m.Y}}
        if (\Contao\System::getContainer()->get('contao.security.token_checker')->hasFrontendUser()) 
        {
            $this->import('FrontendUser', 'User');
            $strDate = '';
            $zero = false;
            $strDateFormat = $GLOBALS['TL_CONFIG']['dateFormat'];
            if ($this->User->id !== null) 
            {
                $objLogin = \Contao\Database::getInstance()
                                ->prepare("SELECT 
                                                lastLogin 
                                            FROM 
                                                tl_member 
                                            WHERE 
                                                id = ?
                                        ")
                                ->limit(1)
                                ->execute($this->User->id);
                // zero Parameter angegeben? 
                if (isset($this->arrTag[1]) &&
                          $this->arrTag[1] == 'zero') 
                {
                    $zero = true;
                }
                // date Definition angegeben und != zero?
                if (isset($this->arrTag[1]) &&
                          $this->arrTag[1] != 'zero') 
                {
                    $strDateFormat = $this->arrTag[1]; // date
                }
                // wenn zweiter Parameter, muss date Definition sein
                if (isset($this->arrTag[2])) 
                {
                    $strDateFormat = $this->arrTag[2]; // date
                }

                // Auswertung
                if ((int) $objLogin->lastLogin > 0) 
                {
                    $strDate = date($strDateFormat, (int) $objLogin->lastLogin);
                } // first login
                elseif ($zero) 
                {
                    $strDate = 0;
                } 
                else 
                {
                    $strDate = date($strDateFormat);
                }

                return $strDate;
            } //$this->User->id
        } //FE_USER_LOGGED_IN

        return false;
    }

    /**
     * Insert-Tag: Last Login Number Registered Members (aktiv, login allowed)
     * @return int number of registered members
     */
    private function getLastLoginNumberRegisteredMembers(): int
    {
        $objLogin = \Contao\Database::getInstance()
                        ->prepare("SELECT 
                                        count(`id`) AS ANZ 
                                    FROM 
                                        `tl_member` 
                                    WHERE 
                                        `disable` != ? 
                                    AND 
                                        `login` = ?
                                ")
                        ->limit(1)
                        ->execute(1, 1);

        return (int) $objLogin->ANZ;
    }

    /**
     * Insert-Tag: Last Login Number Online Members
     * @return int number of online members
     */
    private function getLastLoginNumberOnlineMembers(): int
    {
        //$timeout = (int) ($this->sessionStorageOptions['gc_maxlifetime'] ?? \ini_get('session.gc_maxlifetime'));
        $timeout = (int) \ini_get('session.gc_maxlifetime');
        //number of online members
        // alle die eine zeitlich gueltige Session haben
        $objUsers = \Contao\Database::getInstance()
                        ->prepare("SELECT 
                                        count(DISTINCT username) AS ANZ 
                                   FROM 
                                        tl_member tlm, 
                                        tl_online_session tls
                                   WHERE 
                                        tlm.id=tls.pid 
                                   AND 
                                        tls.tstamp > ? 
                                   AND 
                                        tls.instanceof  = ?
                                ")
                        ->limit(1)
                        ->execute((time() - $timeout), 'FE_USER_AUTH');

        if ($objUsers->numRows < 1) 
        {
            $NumberMembersOnline = 0;
        } else {
            $NumberMembersOnline = $objUsers->ANZ;
        }

        return (int) $NumberMembersOnline;
    }

    /**
     * Insert-Tag: Last Login Number Offline Members
     * @return int number of offline members
     */
    private function getLastLoginNumberOfflineMembers(): int
    {
        //$timeout = (int) ($this->sessionStorageOptions['gc_maxlifetime'] ?? \ini_get('session.gc_maxlifetime'));
        $timeout = (int) \ini_get('session.gc_maxlifetime');
        //number of offline members
        //die heute einmal Online waren und jetzt Offline sind (inaktiv oder heute abgemeldet)
        //$llmo_name = 'tlm.username as name';
        $llmo = 'tlm.id';
        // Alle (aktive) 
        // abzueglich alle die eine zeitlich gueltige Session haben (online aktiv)
        // abzueglich gestern oder aelter angemeldet und wieder abgemeldet (ohne Session)
        // = offline members (lange inaktiv oder heute abgemeldet) 
        $objUsers = \Contao\Database::getInstance()
                        ->prepare("SELECT 
                                        COUNT(" . $llmo . ") as ANZ 
                                    FROM 
                                        tl_member tlm 
                                    WHERE 
                                        `disable` != ? 
                                    AND 
                                        `login` = ? 
                                    AND 
                                        " . $llmo . "  NOT IN 
                                        (
                                        SELECT 
                                            " . $llmo . "
                                        FROM 
                                            tl_member tlm, 
                                            tl_online_session tls 
                                        WHERE 
                                            tlm.id=tls.pid 
                                        AND 
                                            tls.tstamp > ? 
                                        AND 
                                            tls.instanceof  = ?
                                        )
                                   AND 
                                        " . $llmo . "  NOT IN 
                                        ( 
                                        SELECT 
                                            " . $llmo . "
                                        FROM 
                                            tl_member tlm 
                                        WHERE 
                                            tlm.currentLogin <= ?
                                        AND 
                                            " . $llmo . "  NOT IN 
                                            (
                                            SELECT DISTINCT pid AS id 
                                            FROM 
                                                tl_online_session
                                            WHERE 
                                                instanceof  = ?
                                            )
                                        )
                                    ")
                        ->execute(
                            1,
                            1,
                            (time() - $timeout),
                            'FE_USER_AUTH',
                            mktime(0, 0, 0, (int) date("m"), (int) date("d"), (int) date("Y")),
                            'FE_USER_AUTH'
                        );
        $NumberMembersOffline = $objUsers->ANZ;

        return (int) $NumberMembersOffline;
    }

} // class

