<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if( !class_exists('vitsitemap_Main') ){

	/**
	 * Plugin Main Class
	 */
	class vitsitemap_Main
	{
		public $plugin_file;
		public $plugin_dir;
		public $plugin_path;
		public $plugin_url;
	
		/**
		 * Static Singleton Holder
		 * @var self
		 */
		protected static $instance;
		
		/**
		 * Get (and instantiate, if necessary) the instance of the class
		 *
		 * @return self
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function __construct()
		{
			$this->plugin_file = VITSITEMAP_PLUGIN_FILE;
			$this->plugin_path = trailingslashit( dirname( $this->plugin_file ) );
			$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
			$this->plugin_url  = str_replace( basename( $this->plugin_file ), '', plugins_url( basename( $this->plugin_file ), $this->plugin_file ) );

			add_action('plugins_loaded', array( $this, 'vitsitemap_plugins_loaded' ), 1);
			add_filter('plugin_action_links', array($this,'vitsitemap_fn_add_settings_link_plugin'), 10, 4 );
			add_filter('network_admin_plugin_action_links', array($this,'vitsitemap_fn_add_settings_link_plugin'), 10, 4 );
			add_action('admin_menu', array($this,'vitsitemap_fn_admin_menu_callback'));
			add_action('admin_enqueue_scripts', array($this, 'vitsitemap_fn_enqueue_admin_scripts'));
			add_action('wp_enqueue_scripts', array($this, 'vitsitemap_fn_enqueue_front_scripts'));
			add_action('init', array($this, 'vitsitemap_fn_add_shortcode'));
			add_action('save_post', array($this, 'vitsitemap_fn_save_sitemap_xml'));
		}
		
		/**
		 * plugin activation callback
		 * @see register_deactivation_hook()
		 *
		 * @param bool $network_deactivating
		 */
		public static function activate() {

		}

		/**
		 * plugin deactivation callback
		 * @see register_deactivation_hook()
		 *
		 * @param bool $network_deactivating
		 */
		public static function deactivate( $network_deactivating ) {

		}
		
		/**
		 * plugin deactivation callback
		 * @see register_uninstall_hook()
		 *
		 * @param bool $network_uninstalling
		 */
		public static function uninstall() {
		   
		}
		
		public function vitsitemap_plugins_loaded() {
			$this->vitsitemap_loadLibraries();
		}

		/**
		 * Load all the required library files.
		 */
		protected function vitsitemap_loadLibraries() {
    		register_setting( 'vitsitemap-setting-group', 'vitsitemap_new_tab_opening' );
    		register_setting( 'vitsitemap-setting-group', 'vitsitemap_exclude_ids' );
		}

		public function vitsitemap_fn_add_settings_link_plugin( $actions, $plugin_file, $plugin_data, $context ) {
 
		    // Add settings action link for plugins
		    if ( !array_key_exists( 'settings', $actions ) && $plugin_file == "WordpressPlugin-main/vit-sitemap.php" && current_user_can( 'manage_options' ) ){
		    	$url = admin_url( "admin.php?page=vit-sitemap" );
		    	$actions['settings'] = sprintf( '<a href="%s">%s</a>', $url, __( 'Settings', 'VIT Sitemap' ) );
		    }
		    
		    return $actions;
		}

		public function vitsitemap_fn_admin_menu_callback(){

			add_menu_page(
				__( 'VIT Sitemap', 'vit-sitemap' ),
				'VIT Sitemap',
				'manage_options',
				'vit-sitemap',
				array($this,'vitsitemap_fn_sitemap_admin_menu_page_callback'),
				'dashicons-text-page',
				25
			);
		}

		function vitsitemap_fn_sitemap_admin_menu_page_callback(){

			require_once( $this->plugin_path. 'includes/view/admin/sitemap-options.php');
		}
        
        public function vitsitemap_fn_enqueue_admin_scripts(){

			wp_enqueue_style( 'vitsitemap-admin-css', $this->plugin_url."assets/css/admin.css" );
			wp_enqueue_script( 'vitsitemap-admin-script', $this->plugin_url."assets/js/admin.js" );
			$params = array('ajaxurl' => admin_url( 'admin-ajax.php'),);
			wp_localize_script( 'vitsitemap-admin-script', 'script_params', $params );
		}

		public function vitsitemap_fn_enqueue_front_scripts(){

			wp_enqueue_style( 'vitsitemap-front-css', $this->plugin_url."assets/css/front_style.css" );
		}

		public function vitsitemap_fn_add_shortcode() {
		
		    add_shortcode('vit_sitemap_generator', array($this, 'vitsitemap_fn_generator_shortcode'));
		}

		public function vitsitemap_fn_generator_shortcode() {
						
			ob_start();
			require_once( $this->plugin_path. 'includes/view/shortcodes/html_sitemap.php');
			return ob_get_clean();
		}

		public function vitsitemap_generate_sitemap_xml() {

		    $post_types = array('post', 'page');

		    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
		    $sitemap .= '<urlset xmlns:xhtml="http://www.sitemaps.org/schemas/sitemap/0.9">';

		    foreach ($post_types as $post_type) {
		        $args = array(
		            'post_type'      => $post_type,
		            'post_status'    => 'publish',
		            'posts_per_page' => -1,
		        );

		        $query = new WP_Query($args);
		        if ($query->have_posts()) {
		            while ($query->have_posts()) {
		                $query->the_post();
		                $post_title = get_the_title();
		                $post_url = get_permalink();
		                $post_date = get_the_date('Y-m-d\TH:i:s\Z');
		                $sitemap .= '<url>';
		                $sitemap .= '<loc>' . $post_title . '</loc>';
		                // Add the href attribute to the URL
		                $sitemap .= '<xhtml:link rel="alternate" href="' . $post_url . '" />';
		                $sitemap .= '<lastmod>' . $post_date . '</lastmod>';
		                $sitemap .= '</url>';
		            }
		        }
		        wp_reset_postdata();
		    }

		    $sitemap .= '</urlset>';
		    return $sitemap;

		}

		public function vitsitemap_fn_save_sitemap_xml() {
		    $sitemap_content = $this->vitsitemap_generate_sitemap_xml();

		    // Specify the path and filename for the sitemap XML file
		    $sitemap_file = ABSPATH . 'sitemap.xml';

		    // Save the content to the file
		    $result = file_put_contents($sitemap_file, $sitemap_content);
		}

	}
}

class vitsitemap_page_list extends Walker_Page {
    function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0) {
        if ( $depth ) {
            $indent = str_repeat( "\t", $depth );
        } else {
            $indent = '';
        }

        if(!empty(get_option( 'vitsitemap_new_tab_opening' ))){
	        $new_tab_opening = '_blank';
	    }else{
	        $new_tab_opening = '_self';
	    }

        $css_class = array( 'page_item', 'page-item-'.$page->ID );

        if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
            $css_class[] = 'page_item_has_children';
        }

        if ( ! empty( $current_page ) ) {
            $_current_page = get_post( $current_page );

            if ( in_array( $page->ID, $_current_page->ancestors ) ) {
                $css_class[] = 'current_page_ancestor';
            }

            if ( $page->ID == $current_page ) {
                $css_class[] = 'current_page_item';
            } elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
                $css_class[] = 'current_page_parent';
            }
        } elseif ( $page->ID == get_option('page_for_posts') ) {
            $css_class[] = 'current_page_parent';
        }

        $css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

        $output .= $indent . '<li class="' . esc_attr($css_class) . '">';
        $output .= '<a href="' . esc_url(get_permalink( $page->ID )) . '" target="' . esc_attr($new_tab_opening)  . '">' . esc_html($page->post_title) . '</a>';
    }
}