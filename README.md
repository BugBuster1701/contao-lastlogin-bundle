# Contao 5 Lastlogin Bundle

[![Latest Stable Version](https://poser.pugx.org/bugbuster/contao-lastlogin-bundle/v/stable.svg)](https://packagist.org/packages/bugbuster/contao-lastlogin-bundle)
![Contao Version](https://img.shields.io/badge/Contao-5.3-orange) ![Contao Version](https://img.shields.io/badge/Contao-4.13-orange)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/bugbuster/contao-lastlogin-bundle)
![GitHub issues](https://img.shields.io/github/issues/BugBuster1701/contao-lastlogin-bundle)
[![License](https://poser.pugx.org/bugbuster/contao-lastlogin-bundle/license.svg)](https://packagist.org/packages/bugbuster/contao-lastlogin-bundle)


## About

Display the "Last Login" time and "online members" by using Insert-Tags. More details in the manual.<br>
If you are upgrading from version 1 to 2, please read the section 'API changes'.

## Installation

Installation with Contao-Manager: 
* search for package: bugbuster/contao-lastlogin-bundle
* update the database

__Attention__: 
* Users of Contao 5.5+, use `^2.0` as version number! 
* Users of Contao 5.3.x, use `^1.8` as version number! 
* Users of Contao 4.13.x, use `^1.6` as version number! 


Installation über Contao-Manager

* Suche das Paket: bugbuster/contao-lastlogin-bundle
* Datenbank Update durchführen

__Achtung__: 
* Nutzer von Contao 5.5+, verwenden `^2.0` als Versionsangabe!
* Nutzer von Contao 5.3.x, verwenden `^1.8` als Versionsangabe!
* Nutzer von Contao 4.13.x, verwenden `^1.6` als Versionsangabe!

## API changes

### Version 1.* to 2.0

#### Insert-Tags
The prefix ‘cache_’ is no longer supported. All insert tag outputs are now generally not cached.
When upgrading from version 1.x to 2.x, the insert tags used must be adapted:
- cache_last_login => last_login
- cache_last_login_number_online_members => last_login_number_online_members
- cache_last_login_number_offline_members => last_login_number_offline_members
- cache_last_login_number_registered_members => last_login_number_registered_members
