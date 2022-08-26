<?php

/*
 *	Prestashop
 *	Module by. Modestas Š.
 */
if( !defined( '_PS_VERSION_' ) ) { return false; }

/*
 *	Module
 */
class AdminGallery extends Module implements \PrestaShop\PrestaShop\Core\Module\WidgetInterface {
	
	// Variables
	private $decide;
	private $photosDirectory;
	
	private $customer_product_id;
	private $admin_product_id;
	
	// Template
	private $tplFile;
	private $cssFile;
	private $jsFile;
	
	// Photo Directory
	private $photosDir;
	
	// Logic
	public function __construct() {
		
		//
		$this->name = 'adminGallery';
		$this->author = 'Modestas Š.';
		$this->version = '1.0.0';
		
		//
		$this->bootstrap = true;
		
		//
		parent::__construct();
		
		//
		$this->displayName = $this->l('Gallery');
		$this->description = $this->l('Administration Gallery');
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_ );

		// Prepare 'custom_Logic'
		$this->onEnable();
		
		// Template
		$this->tplFile = 'module:adminGallery/views/templates/hook/AdminGallery.tpl';
		
		// CSS
		$this->cssFile = $this->_path.'views/css/adminGallery.css';
		
		// JS
		$this->jsFile = $this->_path.'views/js/adminGallery.js';
	}
	
	/*
	 *	custom 'Widget Logic'
	 */
	public function onEnable() {
		
		// Things beyond the mind..
		$address = $_SERVER['REQUEST_URI'];
		$this->decide = ( str_contains($address, "prestashop/index.php?") ) ? true : false;
		
		// Explosions!
		if( $this->decide ) {

			// 'CUSTOMER'
			$this->customer_product_id = Tools::getValue('id_product');
			$this->photosDirectory = _PS_ROOT_DIR_.'/var/adminGallery/'.$this->customer_product_id;
		} else {

			// 'ADMINISTRATION'
			$first_boom = explode("/sell/catalog/products/", $address)[1];
			$second_boom = explode("?_token=", $first_boom)[0];
			
			$this->admin_product_id = $second_boom;
			$this->photosDirectory = _PS_ROOT_DIR_.'/var/adminGallery/'.$this->admin_product_id;
		}

		/*
		 * create 'dir' for customer product images if not exists..
		 */
		if( !file_exists( _PS_ROOT_DIR_.'./var/adminGallery/' ) ) {
			mkdir(_PS_ROOT_DIR_.'./var/adminGallery/', 0777, true);
		}
	}
	
	/*
	 *	Template Fetch
	 */
	public function renderWidget($hookName, array $configuration) {

		/*
		 *	-
		 */
		$this->smarty->assign( array( 'photos' => $this->getProductImages() ) );
		$this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
		
		/*
		 *	Display Template
		 */
		return $this->fetch($this->tplFile);
	}
	
	/*
	 *	Include some `data`.
	 */
	public function getWidgetVariables( $hookName, array $configuration ) {

		/*
		 *	TODO: add '@module/translations'
		 */
		return array(
			'admin_Title' => "Add Customer Photos",
			'index_Title' => "PHOTOS FROM OUR CUSTOMERS",
			
			'admin_product_id' => $this->admin_product_id,
			'customer_product_id' => $this->customer_product_id
		);
	}
	
	/*
	 *	override default Install..
	 *	skip 'Design/Positions' manual work..
	 *
	 *	{widget name='adminGallery'}
	 */
	public function install() {
		
		/*
		 *	Hook's
		 *	'displayHeader' - 'CSS'
		 *	'displayBackOfficeHeader' - 'CSS' for 'ADMIN PAGE'
		 */
		return parent::install()
			&& $this->registerHook( 'displayHeader' )
			&& $this->registerHook( 'displayBackOfficeHeader' )
			&& $this->registerHook( 'displayAdminProductsMainStepLeftColumnMiddle' )
			&& $this->registerHook( 'displayAfterProductThumbs' );
	}
	
	/*
	 *	-
	 */
	public function uninstall() {
	
		if( parent::uninstall()) { return true; }
		return false;
		
	}
	
	/*
	 *	hook(Header) for Prestashop
	 */
	public function hookDisplayHeader($params) {
		$this->context->controller->addCSS($this->cssFile, 'all');
	}
	
	/*
	 *	hook(Header) for Prestashop Admin Page
	 */
	public function hookDisplayBackOfficeHeader($params) {
		$this->context->controller->addCSS($this->cssFile, 'all');
		$this->context->controller->addJS($this->jsFile);
	}

	/*
	 *	simple Image 'getter'
	 */
	public function getProductImages() {

		if( !file_exists( $this->photosDirectory ) ) { mkdir( $this->photosDirectory, 0777, true); }

		$files = array_diff( scandir( $this->photosDirectory, SCANDIR_SORT_DESCENDING ), array('..', '.') );

		return $files;
	}
	
	/*
	 *	END
	 */
	
}