<?php

/*
 * This file is part of a BugBuster Contao Bundle (Resources\contao)
 *
 * @copyright  Glen Langer 2023 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Lastlogin
 * @license    LGPL-3.0-or-later
 * @see        https://github.com/BugBuster1701/contao-lastlogin-bundle
 */

/**
 * Register hook functions
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('BugBuster\LastLogin\LastLogin', 'ReplaceInsertTagsLastLogin');

/**
 * Abschaltung der Login Bedingung mit "false", Default ist "true"
 * Updatesicher sollte dies in der localconfig.php eingetragen werden.
 * Vorher Frontend Nutzer Einverständnis einholen.
 * 
 * $GLOBALS['TL_CONFIG']['mod_lastlogin_login_check'] = false;
 */

