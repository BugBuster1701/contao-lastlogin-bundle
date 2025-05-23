<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2025 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Lastlogin
 * @link       https://github.com/BugBuster1701/contao-lastlogin-bundle
 *
 * @license    LGPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
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
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('BugBuster\LastloginBundle\BugBusterLastloginBundle')
                ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle', 'BugBuster\OnlineBundle\BugBusterOnlineBundle'])
                ->setReplace(['lastlogin']),
        ];
    }
}
