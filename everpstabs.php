<?php
/**
 * 2019-2022 Team Ever
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Team Ever <https://www.team-ever.com/>
 *  @copyright 2019-2022 Team Ever
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'everpstabs/models/EverPsTabsClass.php');
use PrestaShop\PrestaShop\Core\Product\ProductExtraContent;

class Everpstabs extends Module
{
    private $html;
    private $postErrors = array();
    private $postSuccess = array();

    public function __construct()
    {
        $this->name = 'everpstabs';
        $this->tab = 'administration';
        $this->version = '1.3.2';
        $this->author = 'Team Ever';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Ever PS Tabs');
        $this->description = $this->l('Add a tab on your product page');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->siteUrl = Tools::getHttpHost(true).__PS_BASE_URI__;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        // Install SQL
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayProductExtraContent') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionObjectProductDeleteAfter') &&
            $this->registerHook('actionObjectProductUpdateAfter');
    }

    public function uninstall()
    {
        // Install SQL
        include(dirname(__FILE__).'/sql/uninstall.php');
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->context->smarty->assign(array(
            'everpstabs_dir' => $this->_path
        ));
        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/header.tpl');
        if ($this->checkLatestEverModuleVersion($this->name, $this->version)) {
            $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/upgrade.tpl');
        }
        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/footer.tpl');

        return $this->html;
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $controller_name = Tools::getValue('controller');
        if ($controller_name == 'product') {
            $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (!$params['id_product']) {
            return;
        } else {
            $id_product = (int)$params['id_product'];
        }
        $product = new Product(
            (int)$id_product,
            false,
            (int)Context::getContext()->language->id,
            (int)Context::getContext()->shop->id
        );
        $everpstabs = EverPsTabsClass::getByIdProduct(
            (int)$product->id
        );
        $ever_ajax_url =  Context::getContext()->link->getAdminLink(
            'AdminModules',
            true,
            [],
            ['configure' => $this->name, 'token' => Tools::getAdminTokenLite('AdminModules')]
        );
        $this->smarty->assign(array(
            'everpstabs' => $everpstabs,
            'default_language' => $this->context->employee->id_lang,
            'ever_languages' => Language::getLanguages(false),
            'ever_ajax_url' => $ever_ajax_url,
        ));
        return $this->display(__FILE__, 'views/templates/admin/product-tab.tpl');
    }

    public function hookActionObjectProductUpdateAfter($params)
    {
        $controllerTypes = array('admin', 'moduleadmin');
        if (!in_array(Context::getContext()->controller->controller_type, $controllerTypes)) {
            return;
        }
        $everpstabs = EverPsTabsClass::getByIdProduct(
            (int)$params['object']->id
        );
        if (!Validate::isLoadedObject($everpstabs)) {
            $everpstabs = new EverPsTabsClass();
            $everpstabs->id_product = (int)$params['object']->id;
        }
        foreach (Language::getLanguages(true) as $language) {
            if (Tools::getValue('everpstabs_title_'.$language['id_lang'])
                && !Validate::isCleanHtml(Tools::getValue('everpstabs_title_'.$language['id_lang']))
            ) {
                die(json_encode(
                    array(
                        'return' => false,
                        'error' => $this->l('Title is not valid')
                    )
                ));
            } else {
                $everpstabs->title = Tools::getValue('everpstabs_title_'.$language['id_lang']);
            }
            if (Tools::getValue('everpstabs_'.$language['id_lang'])
                && !Validate::isCleanHtml(Tools::getValue('everpstabs_'.$language['id_lang']))
            ) {
                die(json_encode(
                    array(
                        'return' => false,
                        'error' => $this->l('Content is not valid')
                    )
                ));
            } else {
                $everpstabs->content[$language['id_lang']] = Tools::getValue(
                    'everpstabs_'.$language['id_lang']
                );
            }
        }
        $everpstabs->save();
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        $controllerTypes = array('admin', 'moduleadmin');
        if (!in_array(Context::getContext()->controller->controller_type, $controllerTypes)) {
            return;
        }
        $everpstabs = EverPsTabsClass::getByIdProduct(
            (int)Tools::getValue('id_product')
        );
        if (Validate::isLoadedObject($everpstabs)) {
            $everpstabs->delete();
        }
    }

    public function hookDisplayProductExtraContent($params)
    {
        $product = new Product(
            (int)$params['product']->id,
            false,
            (int)Context::getContext()->language->id,
            (int)Context::getContext()->shop->id
        );
        $everpstabs = EverPsTabsClass::getByIdProduct(
            (int)$product->id
        );
        $title = $everpstabs->title[
            (int)Context::getContext()->language->id
        ];
        $content = $everpstabs->content[
            (int)Context::getContext()->language->id
        ];
        if (!Validate::isLoadedObject($everpstabs)) {
            return;
        }
        $this->context->smarty->assign(
            array(
                'everpstabs_title' => $title,
                'everpstabs_content' => $content,
            )
        );
        $tab = $this->context->smarty->fetch(
            'module:everpstabs/views/templates/hook/tab.tpl'
        );
        $array = array();
        $array[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())
                ->setTitle($title)
                ->setContent($content);
        return $array;
    }

    public function checkLatestEverModuleVersion($module, $version)
    {
        $upgrade_link = 'https://upgrade.team-ever.com/upgrade.php?module='
        .$module
        .'&version='
        .$version;
        $handle = curl_init($upgrade_link);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($httpCode != 200) {
            return false;
        }
        $module_version = Tools::file_get_contents(
            $upgrade_link
        );
        if ($module_version && $module_version > $version) {
            return true;
        }
        return false;
    }
}
