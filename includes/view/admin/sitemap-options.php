<?php
/**
 * Sitemap Settings Page
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if (isset($_POST['generate_vitsitemap']) && current_user_can('manage_options')) {
    if (check_admin_referer('generate_vitsitemap_nonce', 'generate_vitsitemap_nonce')) {
        // Nonce is valid, user has the right permissions, proceed to save sitemap
        $this->vitsitemap_fn_save_sitemap_xml();
    }
}

?>
<div class="container">
	<h1>VIT Sitemap Settings</h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="postbox">
					<h3 class="hndle">XML Sitemap</h3>
					<div class="inside">
					 	<form method="post">
					 	<?php
						$sitemap_path = ABSPATH . 'sitemap.xml'; 
						if (file_exists($sitemap_path)) { ?>
					    	<p class="description">The website contains an XML Sitemap. <a href="<?php echo esc_url(get_site_url()) ?>/sitemap.xml" target='_blank'>Click here</a> to view the sitemap.</p>
						<?php } else { 
							wp_nonce_field('generate_vitsitemap_nonce', 'generate_vitsitemap_nonce'); // Add a nonce field
							?>
					     <input type="submit" class="button-primary" name="generate_vitsitemap" value="Generate XML Sitemap">
					<?php } ?>
				    	</form>
				    </div>
				</div>
		        <form method="post" action="options.php">
		            <?php settings_fields( 'vitsitemap-setting-group' ); ?>
		            <?php do_settings_sections( 'vitsitemap-setting-group' ); ?>
		            <div class="postbox">
		            	<h3 class="hndle">HTML Sitemap</h3>
		            	<h3 class="hndle">General settings</h3>
		            	<div class="inside">
			            	<?php 
			            		$new_tab_opening = get_option( 'vitsitemap_new_tab_opening' );
			            	?>
			            	<label for="vitsitemap_new_tab_opening"><input type="checkbox" name="vitsitemap_new_tab_opening" id="vitsitemap_new_tab_opening" value="1" <?php echo esc_attr( checked( 1, $new_tab_opening, false ) ); ?>>Open Links in New Tab</label>
			            	<p class="description">Once you select the option, clicking on the link will result in it opening in a new tab.</p>
			            </div>
			            <h3 class="hndle">Exclude data from Sitemap</h3>
		            	<div class="inside">
			            	<?php
			            		$exclude_ids = get_option( 'vitsitemap_exclude_ids' );
			            	?>
			            	<label for="vitsitemap_exclude_ids">Add Exclude Page's ID<input class="select_box" type="text" id="vitsitemap_exclude_ids" name="vitsitemap_exclude_ids" value="<?php echo esc_attr($exclude_ids); ?>"/></label>
			            	<p class="description">Please add the IDs of the pages that you do not want to display on the sitemap page. Separate the IDs using the "," symbol.</p>
			            </div>
		            </div>
		        <?php submit_button(); ?>
			    </form>
			</div>
			<div id="postbox-container-1" class="postbox-container">
	            <div class="postbox">
	            	<h3 class="hndle">Plugin Shortcodes</h3>
	            	<div class="inside">
						<p class="content"><b>[vit_sitemap_generator]</b></p>
						<p class="description">Insert the given shortcode to display the HTML sitemap on your webpage.</p>
		        	</div>
	            </div>
			</div>
		</div>
	</div>
</div>
