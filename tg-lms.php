<?php

/**
 * Plugin Name: TG LMS
 * Plugin URL: http://tg-lms.local
 * Version: 1.0.0
 * Author: saphal_pantha
 * Text-Domain: tg-lms
 * Description: WordPress LMS plugin for building an e-learning website effortlessly. Design interactive online courses with features like adding, removing, querying courses and many more.
 * Licence: GNU
 * Author URI: https://github.com/saphalpantha
 */

namespace TgRoot\Admin;

use TgRoot\Admin\Course\Course;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * TG_LMS class handles the main functionalities of the TG LMS plugin.
 *
 * @since 1.0.0
 */
class TgLMS {
    
    /**
     * Class constructor for initialsizing the TG LMS plugin.
     *
     * @since 1.0.0
     */
    public function __construct() {
        
        add_shortcode("tg_courses", array($this, "tg_course_shortcode_handle"));
        
        add_action('admin_menu', array($this, 'create_course_menus'));
        
        add_action('admin_enqueue_scripts', array($this, 'add_react_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'tg_lms_enqueue_scripts'));
        
        add_action('wp_ajax_nopriv_handle_create_course_form', array($this, 'handle_create_course_form'));
        add_action('wp_ajax_handle_create_course_form', array($this, 'handle_create_course_form'));

        add_action('wp_ajax_nopriv_tg_lms_fetch_courses', array($this, 'tg_lms_fetch_courses'));
        add_action('wp_ajax_tg_lms_fetch_courses', array($this, 'tg_lms_fetch_courses'));
        
        add_action('wp_ajax_tg_lms_fetch_single_course', array($this, 'tg_lms_fetch_single_course'));
        add_action('wp_ajax_nopriv_tg_lms_fetch_single_course', array($this, 'tg_lms_fetch_single_course'));
        
        add_action('wp_ajax_search_courses', array($this, 'search_courses'));
        add_action('wp_ajax_nopriv_search_courses', array($this, 'search_courses'));

        add_shortcode('tg_render_react_template', 'tg_render_react');
        
        add_action('init', function  (){
            $labels = array(
                'name'          => __('Courses', 'tg-lms'),
                'singular_name' => __('Course', 'tg-lms'),
                'add_new'       => __('Add New Course', 'tg-lms'),
            );

            $supports = array(
                'title',
                'editor',
                'thumbnail',
                'custom-fields',
            );

            $args = array(
                'label'         => __('Course', 'tg-lms'),
                'description'   => __('Course Description', 'tg-lms'),
                'labels'        => $labels,
                'supports'      => $supports,
                'taxonomies'    => array('category', 'post_tag'),
                'public'        => true,
                'show_ui'       => true,
                'has_archive'   => false,
                'rewrite'       => array('slug' => 'course'),
            );

            register_post_type('tg_course', $args);
    });

        add_filter('template_include', array($this, 'load_course_templates'));


        add_filter('tg_course_sanitized_input', function($data){

            $data['test_key'] = 'test value';

            return $data;
            
        }, 10, 1);

       // <!-- $regiser_course = new \TgRoot\Admin\Course\RegisterCourse('tg-course'); -->


        
        $courseApi = new \TgRoot\Admin\Course\Api\CourseApi();
    }

    /**
     * Load Single Page Post for Course
     *
     * @since 1.0.0
     * @return $template HTML string
     */
    public function load_course_templates($template) {
        if(is_singular('tg_course')){
            $template = plugin_dir_path(__FILE__) . 'templates/single-course.php';

            if(file_exists($template)){
                return $template;
            }
        }

        return $template;
    }
    
    /**
     * Handles the form submission for creating a new course.
     *
     * @since 1.0.0
     * @return WP_REST_Response
     */
    public function handle_create_course_form() {
        $errors = array();

        $user_input = array(
            "course_title"       => isset($_POST["course_title"]) ? sanitize_text_field($_POST["course_title"]) : '',
            "course_description" => isset($_POST["course_description"]) ? sanitize_textarea_field($_POST["course_description"]) : '',
            "course_price"       => isset($_POST["course_price"]) ? sanitize_text_field($_POST["course_price"]) : '',
            "course_category"    => isset($_POST["course_category"]) ? sanitize_text_field($_POST["course_category"]) : '',
            "course_status"      => isset($_POST["course_status"]) ? sanitize_text_field($_POST["course_status"]) : '',
            "course_image"       => isset($_FILES['course_image']) ? $_FILES['course_image'] : null,
        );

        foreach ($user_input as $key => $val) {
            if ($key === "course_image") {
                if (!isset($_FILES[$key])) {
                    $errors[] = $key;
                }
            } else {
                if (empty($val)) {
                    $errors[] = $key;
                }
            }
        }

        if (!empty($errors)) {
            wp_send_json_error("Missing required fields: " . implode(", ", $errors));
            return;
        }

        $course = new Course();

        $response = $course->create_course($user_input);

        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        } else {
            wp_send_json_success($response);
        }
    }
    
    /**
     * Enqueues React scripts for the admin area.
     *
     * @since 1.0.0
     * @return void
     */
    public function add_react_scripts() {
        $curr_screen = get_current_screen();
        
        if ($curr_screen && $curr_screen->id === "toplevel_page_tg-lms") {
            wp_enqueue_script('tg_react_script', plugins_url('src/front/dist/js/tg.bundle.js', __FILE__), array('jquery'), '1.0.0', false);
            wp_localize_script(
                'tg_react_script',
                'tg_global',
                array(
                    'admin_url' => admin_url('admin-ajax.php'),
                )
            );
        }
    }
    
    /**
     * Enqueues scripts and styles for the frontend.
     *
     * @since 1.0.0
     * @return void
     */
    public function tg_lms_enqueue_scripts() {
        wp_enqueue_script('tg_lms_script', plugins_url('src/Front/dist/js/all-courses.js', __FILE__), array('jquery'), '1.0.0', true);
        wp_enqueue_style('tg_lms_style', plugin_dir_url(__FILE__) . 'templates/style.css', array(), '1.0.0', 'all');

        wp_localize_script('tg_lms_script', 'tg_lms_ajax', array(
            'url'   => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tg_lms_nonce'),
        ));

    }
    
    /**
     * Fetches a single course via AJAX.
     *
     * @since 1.0.0
     * @return void
     */
    public function tg_lms_fetch_single_course() {
        $course_id = isset($_POST['course_id']) ? absint($_POST['course_id']) : 0;
        $course = new Course();
        $single_course = $course->get_course_by_id($course_id);
        echo json_encode($single_course);
        wp_die();
    }

    /**
     * Fetches all courses via AJAX.
     *
     * @since 1.0.0
     * @return void
     */
    public function tg_lms_fetch_courses() {
        $course = new Course();
        $all_courses = $course->get_all_courses();
        echo json_encode($all_courses);
        wp_die();
    }

    /**
     * Handles the AJAX search request for courses.
     *
     * @since 1.0.0
     * @return void
     */
/**
* Registers the main menu and submenus for the TG LMS plugin in the WordPress admin.
*
* @since 1.0.0
* @return void
*/
 function create_course_menus() {
        add_menu_page(
            __("Courses", 'tg-lms'),
            __("Courses", 'tg-lms'),
            'manage_options',
            'tg-lms',
            array($this, 'tg_render_react'),
            'dashicons-welcome-learn-more',
            6
        );

        add_submenu_page(
            "tg-lms",
            __("New Course", 'tg-lms'),
            __("New Course", 'tg-lms'),
            'manage_options',
            '#/new-course',
            '',
            2
        );

        add_submenu_page(
            "tg-lms",
            __("All Courses", 'tg-lms'),
            __("All Courses", 'tg-lms'),
            'manage_options',
            '#/courses/',
            '',
            2
        );
    }

/**
* Shortcode handler for displaying a single course.
*
* @since 1.0.0
* @return void
*/
public function tg_course_single_handle() {
?>
<div class="course_single">
    <?php echo do_shortcode('[tg_course_single]'); ?>
</div>
<?php
    }

    /**
     * Shortcode handler for displaying all courses.
     *
     * @since 1.0.0
     * @return void
     */
    public function tg_course_shortcode_handle() {
        ob_start();
        include_once plugin_dir_path(__FILE__) . 'templates/all-tg-courses.php';   
        return ob_get_clean();
    }

    /**
     * Renders the React component for the TG LMS plugin.
     *
     * @since 1.0.0
     * @return string HTML content
     */
    public function tg_render_react() {
        ob_start();
        include_once 'includes/Admin/public/index.php';
        echo $html = ob_get_clean();
        return $html;
    }
}

new TgLMS();