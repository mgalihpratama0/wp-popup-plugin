<?php
/*
Plugin Name: WP Popup Plugin
Description: Plugin untuk menampilkan pop-up di WordPress.
Version: 1.0
Author: Galih Pratama
*/

// Gunakan namespace
namespace WP_Popup_Plugin;

// Gunakan Singleton Pattern
class Popup_Plugin {
    private static $instance;

    private function __construct() {
        // Private constructor untuk mencegah instansiasi langsung
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action('init', array($this, 'register_custom_post_type'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        $this->add_shortcode();
        add_action('rest_api_init', array($this, 'register_rest_api'));
    }

    public function register_custom_post_type() {
        register_post_type('popup', array(
            'labels' => array(
                'name' => 'Popups',
                'singular_name' => 'Popup',
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
        ));
    }

    public function display_popup() {
        $args = array(
            'post_type' => 'popup',
            'posts_per_page' => 1,
        );
        $query = new \WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                echo '<div id="popup" style="display:none;">';
                echo '<h2>' . get_the_title() . '</h2>';
                echo '<p>' . get_the_content() . '</p>';
                echo '</div>';
            }
        }
        wp_reset_postdata();
    }

    public function add_shortcode() {
        add_shortcode('display_popup', array($this, 'display_popup'));
    }

    public function register_rest_api() {
        register_rest_route('artistudio/v1', '/popup', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_popup_data'),
            'permission_callback' => function() {
                return is_user_logged_in();
            }
        ));
    }
    
    public function get_popup_data() {
        $args = array(
            'post_type' => 'popup',
            'posts_per_page' => 1,
        );
        $query = new \WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                return array(
                    'title' => get_the_title(),
                    'content' => get_the_content(),
                );
            }
        }
        wp_reset_postdata();
    }

    public function enqueue_scripts() {
        // Enqueue CSS dan JS
        wp_enqueue_style('popup-style', plugins_url('assets/css/popup-style.css', __FILE__));
        wp_enqueue_script('popup-script', plugins_url('assets/js/popup-script.js', __FILE__), array('jquery'), null, true);
    }
}

// Inisialisasi plugin
Popup_Plugin::get_instance()->init();