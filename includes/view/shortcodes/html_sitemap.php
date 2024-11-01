<?php
/**
 *  HTML Sitemap Page
**/
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
    
    $exclude_ids = '';
    if(!empty(get_option( 'vitsitemap_exclude_ids' ))){
        $exclude_ids = get_option( 'vitsitemap_exclude_ids' );
    }
    
    if(!empty(get_option( 'vitsitemap_new_tab_opening' ))){
        $new_tab_opening = '_blank';
    }else{
        $new_tab_opening = '_self';
    }


    $page_args = array(
        'title_li' => '', 
        'echo' => 0, 
        'exclude' => $exclude_ids,
        'walker'   => new vitsitemap_page_list(),

    );
    $pages_list = wp_list_pages($page_args);

    $args = array(
      'post_type' => 'post',
      'posts_per_page' => -1,
    );
    $query = new WP_Query($args);

    $posts_list = '';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $category = get_the_category();
            $posts_list .= '<li><a target="' . esc_attr($new_tab_opening)  . '" href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
        }
    }
    wp_reset_postdata();

    $vitsitemap_html = '<div class="sitemap_col_2" id="sitemap_col_2"><div class="sitemap_col"><h2>Pages</h2><ul class="pages">' . wp_kses_post($pages_list) . '</ul></div>';

    $vitsitemap_html .= '<div class="sitemap_col"><h2>Posts</h2><ul class="posts">' . wp_kses_post($posts_list) . '</ul></div></div>';

    echo wp_kses_post($vitsitemap_html);


?>
