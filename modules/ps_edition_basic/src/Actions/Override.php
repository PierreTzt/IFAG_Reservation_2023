<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace PrestaShop\Module\PsEditionBasic\Actions;

// create constants for the paths
const DEFAULT_NAVBAR = _PS_ADMIN_DIR_ . '/themes/default/template/nav.tpl';
const NEW_THEME_NAVBAR = _PS_ADMIN_DIR_ . '/themes/new-theme/template/components/layout/nav_bar.tpl';
const NAVBAR_OVERRIDE = _PS_ROOT_DIR_ . '/modules/ps_edition_basic/views/templates/admin/overrides/nav_bar.tpl';
const DEFAULT_BREADCRUMB = _PS_ADMIN_DIR_ . '/themes/default/template/page_header_toolbar.tpl';
const NEW_THEME_BREADCRUMB = _PS_ADMIN_DIR_ . '/themes/new-theme/template/page_header_toolbar.tpl';
const DEFAULT_BREADCRUMB_OVERRIDE = _PS_ROOT_DIR_ . '/modules/ps_edition_basic/views/templates/admin/overrides/default/page_header_toolbar.tpl';
const NEW_THEME_BREADCRUMB_OVERRIDE = _PS_ROOT_DIR_ . '/modules/ps_edition_basic/views/templates/admin/overrides/new-theme/page_header_toolbar.tpl';
class Override
{
    public static function replaceNavbars(): void
    {
        if (!file_exists(DEFAULT_NAVBAR . '.bak') & !file_exists(NEW_THEME_NAVBAR . '.bak')) {
            copy(DEFAULT_NAVBAR, DEFAULT_NAVBAR . '.bak');
            copy(NEW_THEME_NAVBAR, NEW_THEME_NAVBAR . '.bak');
        }

        copy(NAVBAR_OVERRIDE, NEW_THEME_NAVBAR);
        copy(NAVBAR_OVERRIDE, DEFAULT_NAVBAR);
    }

    public static function discardReplaceNavbars(): void
    {
        if (!file_exists(DEFAULT_NAVBAR . '.bak') || !file_exists(NEW_THEME_NAVBAR . '.bak')) {
            return;
        }

        copy(DEFAULT_NAVBAR . '.bak', DEFAULT_NAVBAR);
        copy(NEW_THEME_NAVBAR . '.bak', NEW_THEME_NAVBAR);

        unlink(DEFAULT_NAVBAR . '.bak');
        unlink(NEW_THEME_NAVBAR . '.bak');
    }

    public static function replaceBreadcrumb(): void
    {
        if (!file_exists(DEFAULT_BREADCRUMB . '.bak') & !file_exists(NEW_THEME_BREADCRUMB . '.bak')) {
            copy(DEFAULT_BREADCRUMB, DEFAULT_BREADCRUMB . '.bak');
            copy(NEW_THEME_BREADCRUMB, NEW_THEME_BREADCRUMB . '.bak');
        }

        copy(DEFAULT_BREADCRUMB, DEFAULT_BREADCRUMB_OVERRIDE);
        copy(NEW_THEME_BREADCRUMB, NEW_THEME_BREADCRUMB_OVERRIDE);

        $wcBreadcrumb = '
        <ul class="breadcrumb page-breadcrumb" style="padding-left: 0">    
            <wc-breadcrumb breadcrumb="{htmlspecialchars($breadcrumbs2|json_encode)}"></wc-breadcrumb>
        </ul>
        ';

        $file = file_get_contents(DEFAULT_BREADCRUMB_OVERRIDE);
        // replace from <ul class="breadcrumb page-breadcrumb"> to first </ul>
        $file = preg_replace('/<ul class="breadcrumb page-breadcrumb">.*?<\/ul>/s', $wcBreadcrumb, $file, 1);
        file_put_contents(DEFAULT_BREADCRUMB_OVERRIDE, $file);

        $file = file_get_contents(NEW_THEME_BREADCRUMB_OVERRIDE);
        $file = preg_replace('/<ol class="breadcrumb">.*?<\/ol>/s', $wcBreadcrumb, $file, 1);
        file_put_contents(NEW_THEME_BREADCRUMB_OVERRIDE, $file);

        // replace the old breadcrumb with the new one
        copy(DEFAULT_BREADCRUMB_OVERRIDE, DEFAULT_BREADCRUMB);
        copy(NEW_THEME_BREADCRUMB_OVERRIDE, NEW_THEME_BREADCRUMB);
    }

    public static function discardReplaceBreadcrumb(): void
    {
        if (!file_exists(DEFAULT_BREADCRUMB . '.bak') || !file_exists(NEW_THEME_BREADCRUMB . '.bak')) {
            return;
        }

        copy(DEFAULT_BREADCRUMB . '.bak', DEFAULT_BREADCRUMB);
        copy(NEW_THEME_BREADCRUMB . '.bak', NEW_THEME_BREADCRUMB);

        unlink(DEFAULT_BREADCRUMB . '.bak');
        unlink(NEW_THEME_BREADCRUMB . '.bak');
    }
}
