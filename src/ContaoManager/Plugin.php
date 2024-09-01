<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Contao Bundle
 *
 * @copyright  Glen Langer 2024 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Lastlogin
 * @license    LGPL-3.0-or-later
 * @see        https://github.com/BugBuster1701/contao-lastlogin-bundle
 */

namespace BugBuster\LastloginBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

/**
 * Plugin for the Contao Manager.
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('BugBuster\LastloginBundle\BugBusterLastloginBundle')
                ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle', 'BugBuster\OnlineBundle\BugBusterOnlineBundle'])
                ->setReplace(['lastlogin']),
        ];
    }
}
