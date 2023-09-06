<?php

namespace PrestaShop\Module\PsEditionBasic\Traits;

use PrestaShop\Module\PsEditionBasic\Actions\Override;

trait HaveConfigurationPage
{
    /**
     * This method handles the module's configuration page
     *
     * @return string The page's HTML content
     */
    public function getContent(): string
    {
        $output = $this->handleSaveForm();

        return $output . $this->displayForm();
    }

    public function displayForm(): string
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans(
                        'Settings',
                        [],
                        'Admin.Global'
                    ),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->trans(
                            'Activate new menu and breadcrumb',
                            [],
                            'Modules.Editionbasic.Admin'
                        ),
                        'name' => 'SMB_IS_NEW_MENU_ENABLED',
                        'required' => true,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'new_menu_enabled',
                                    'name' => 'ON',
                                ],
                                [
                                    'id' => 'new_menu_disabled',
                                    'name' => 'OFF',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->trans(
                        'Save',
                        [],
                        'Admin.Actions'
                    ),
                ],
            ],
        ];

        $helper = new \HelperForm();
        $helper->show_toolbar = false;
        $lang = new \Language((int) \Configuration::get('PS_LANG_DEFAULT'));
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSmbConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ) .
            '&configure=' . $this->name .
            '&tab_module=' . $this->tab .
            '&module_name=' . $this->name;
        $helper->token = \Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues(): array
    {
        return [
            'SMB_IS_NEW_MENU_ENABLED' => \Tools::getValue(
                'SMB_IS_NEW_MENU_ENABLED',
                \Configuration::get('SMB_IS_NEW_MENU_ENABLED')
            ),
        ];
    }

    private function handleSaveForm(): string
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (\Tools::isSubmit('submitSmbConfiguration')) {
            // retrieve the value set by the user
            $configValue = (string) \Tools::getValue('SMB_IS_NEW_MENU_ENABLED');

            // check that the value is valid
            if (empty($configValue) || !\Validate::isGenericName($configValue)) {
                // invalid value, show an error
                $output = $this->displayError($this->trans('Invalid Configuration value', [], 'Modules.Editionbasic.Admin'));
            } else {
                // call Override::replaceNavBars is configValue is enable
                if ($configValue == 'new_menu_enabled') {
                    Override::replaceNavbars();
                    Override::replaceBreadcrumb();
                } else {
                    Override::discardReplaceNavbars();
                    Override::discardReplaceBreadcrumb();
                }
                \Configuration::updateValue('SMB_IS_NEW_MENU_ENABLED', $configValue);
                $output = $this->displayConfirmation($this->trans('Settings updated', [], 'Modules.Editionbasic.Admin'));
            }
        }

        return $output;
    }
}
