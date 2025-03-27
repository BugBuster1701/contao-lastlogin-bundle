<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Contao Bundle
 *
 * @copyright  Glen Langer 2025 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Lastlogin
 * @license    LGPL-3.0-or-later
 * @see        https://github.com/BugBuster1701/contao-lastlogin-bundle
 */

namespace BugBuster\LastloginBundle\InsertTag;


use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
// use Contao\CoreBundle\InsertTag\Exception\InvalidInsertTagException;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bundle\SecurityBundle\Security;

#[AsInsertTag('last_login', asFragment: true)]
#[AsInsertTag('last_login_number_online_members', asFragment: true)]
#[AsInsertTag('last_login_number_offline_members', asFragment: true)]
#[AsInsertTag('last_login_number_registered_members', asFragment: true)]
class LastloginInsertTag implements InsertTagResolverNestedResolvedInterface
{
    public function __construct(
        private readonly TokenChecker $tokenChecker,
        private readonly Security $security,
        private readonly Connection $connection,
        private readonly ContaoFramework $framework
    ) {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        switch ($insertTag->getName())
        {
            case "last_login":
                return new InsertTagResult($this->getLastLogin($insertTag), OutputType::text);
                break;
            case "last_login_number_registered_members":
                return new InsertTagResult((string) $this->getLastLoginNumberRegisteredMembers(), OutputType::text);
                break;
            case "last_login_number_online_members":
                return new InsertTagResult((string) $this->getLastLoginNumberOnlineMembers(), OutputType::text);
                break;
            case "last_login_number_offline_members":
                return new InsertTagResult((string) $this->getLastLoginNumberOfflineMembers(), OutputType::text);
                break;
            default:
                return new InsertTagResult('=/\=', OutputType::text); // :-)

        }
    }

    /**
     * Insert-Tag: Last Login
     * @return string return value of the Insert-Tag, empty if FE user not logged in
     */
    private function getLastLogin($insertTag) :string
    {
        // {{last_login}}
        // {{last_login::d.m.Y}}
        // {{last_login::zero}}
        // {{last_login::zero::d.m.Y}}
        if (!$this->tokenChecker->hasFrontendUser()) {
            return '';
        }

        $user = $this->security->getUser();

        $strDate = '';
        $zero = false;

        //$strDateFormat = $GLOBALS['TL_CONFIG']['dateFormat'];
        $config = $this->framework->getAdapter(Config::class);
        $strDateFormat = $config->get('dateFormat');

        if ($user->id !== null) 
        {
            $lastlogin = $this->connection->fetchOne(
                "SELECT lastLogin FROM tl_member WHERE id = :id",
                ['id' => $user->id],
                ['id' => Types::INTEGER],
            );

            // zero/date Parameter angegeben? 
            if (null !== $insertTag->getParameters()->get(0)) {
                if ('zero' == $insertTag->getParameters()->get(0))
                {
                    $zero = true;
                } else {
                    $strDateFormat = $insertTag->getParameters()->get(0); // date
                }
                // wenn zweiter Parameter, muss date Definition sein
                if (null !== $insertTag->getParameters()->get(1)) {
                    $strDateFormat = $insertTag->getParameters()->get(1); // date
                }
            }

            // Auswertung
            if ((int) $lastlogin > 0) 
            {
                $strDate = date($strDateFormat, (int) $lastlogin);
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
        }

        return '';
    }

    /**
     * Insert-Tag: Last Login Number Registered Members (aktiv, login allowed)
     * @return int number of registered members
     */
    private function getLastLoginNumberRegisteredMembers(): int
    {
        $count = $this->connection->fetchOne(
            "SELECT count(`id`) AS ANZ FROM `tl_member` WHERE `disable` != :disa AND `login` = :logi LIMIT 1",
            ['disa' => 1],
            ['disa' => Types::INTEGER],
            ['logi' => 1],
            ['logi' => Types::INTEGER],
        );

        return (int) $count;
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
        $count = $this->connection->fetchOne(
            "SELECT 
                count(DISTINCT username) AS ANZ 
            FROM 
                tl_member tlm, 
                tl_online_session tls
            WHERE 
                tlm.id=tls.pid 
            AND 
                tls.tstamp > :tstamp 
            AND 
                tls.instanceof  = :instanceof
            LIMIT 1",
            ['tstamp' => time() - $timeout],
            ['tstamp' => Types::INTEGER],
            ['instanceof' => 'FE_USER_AUTH'],
            ['instanceof' => Types::STRING],
        );

        if (false === $count) 
        {
            $NumberMembersOnline = 0;
        } else {
            $NumberMembersOnline = $count;
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
        $count = $this->connection->fetchOne(
            "SELECT 
                    COUNT(" . $llmo . ") as ANZ 
                FROM 
                    tl_member tlm 
                WHERE 
                    `disable` != :disa 
                AND 
                    `login` = :logi 
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
                        tls.tstamp > :tstamp 
                    AND 
                        tls.instanceof  = :instanceof
                    )
                AND 
                    " . $llmo . "  NOT IN 
                    ( 
                    SELECT 
                        " . $llmo . "
                    FROM 
                        tl_member tlm 
                    WHERE 
                        tlm.currentLogin <= :currentLogin
                    AND 
                        " . $llmo . "  NOT IN 
                        (
                        SELECT DISTINCT pid AS id 
                        FROM 
                            tl_online_session
                        WHERE 
                            instanceof  = :instanceof
                        )
                    )
                ",
                ['disa' => 1],
                ['disa' => Types::INTEGER],
                ['logi' => 1],
                ['logi' => Types::INTEGER],
                ['tstamp' => time() - $timeout],
                ['tstamp' => Types::INTEGER],
                ['instanceof' => 'FE_USER_AUTH'],
                ['instanceof' => Types::STRING],
                ['currentLogin' => mktime(0, 0, 0, (int) date("m"), (int) date("d"), (int) date("Y"))],
                ['currentLogin' => Types::INTEGER],
        );

        $NumberMembersOffline = $count;

        return (int) $NumberMembersOffline;
    }
}
