<?php





use TgRoot\Admin\Course\Course;

require_once __DIR__ . '/vendor/autoload.php';

class TgLMS{
        
    /**
    * Class constructor for initializing the TG LMS plugin.
    *
    * @method void
    *
    */
    

    function __construct(){
        
        add_shortcode("tg_all_courses", array($this, "tg_all_courses_handle"));

        add_action('admin_menu', array($this, 'create_course_menus'));
        add_action('admin_enqueue_scripts', array($this, 'add_react_scripts'));

        add_action('wp_ajax_nopriv_handle_create_course_form', array($this, 'handle_create_course_form'));
        add_action('wp_ajax_handle_create_course_form', array($this, 'handle_create_course_form'));

        $regiser_course = new \TgRoot\Front\Course\RegisterCourse('tg-course');

        $courseApi      = new TgRoot\Front\Course\Api\CourseApi();
    }


    

    public function handle_create_course_form(){

        $errors = array();

        $user_input = array(

            "course_title"       => $_POST["course_title"],
            "course_description" => $_POST["course_description"],
            "course_price"       => $_POST["course_price"],
            "course_category"    => $_POST["course_category"],
            "course_status"      => $_POST["course_status"],
            "course_image"       => $_FILES['course_image'],

        );


        foreach ($user_input as $key => $val) {
            
            if ($input === "course_image") {
                
                if (!isset($_FILES[$val])) {
                    $errors[] = $key;
                }
                
            }
             
            else {
                
                if (!isset($_POST[$val])) {
                    $errors[] = $key;
                }
                
            }
            
        }


        if (!empty($errors)) {
            wp_send_json_error("Missing required fields: " . implode(", ", $errors));
            return;
        }


        $sanitized_user_input = array(

            "course_title" => sanitize_text_field( $user_input["course_title"] ),
            "course_description" => sanitize_textarea_field( $user_input["course_description"] ),
            "course_price" => sanitize_text_field( $user_input["course_price"] ),
            "course_category" => sanitize_text_field( $user_input["course_category"] ),
            "course_status" => sanitize_text_field( $user_input["course_status"] ),
            "course_image" =>  $user_input["course_image"],
            
        );


        $course = new Course();
        
        return $course->create_course($sanitized_user_input);
        
    }

    public function add_react_scripts(){
        
        global $post;

        $curr_screen = get_current_screen();
        
        if ($curr_screen->id === "toplevel_page_tg-lms") {
            wp_enqueue_script('tg_react_script', plugins_url('src/front/dist/js/tg.bundle.js', __FILE__), array(), '1.0.0', false);
            wp_localize_script(
                'tg_react_script',
                'tg_global',
                array(

                    'admin_url' => admin_url('admin-ajax.php'),

                ),
            );
        }
        //wp_enqueue_style('add_reactstyle', plugins_url('./src/react/test-wp/dist/assets/index-DiwrgTda.css', __FILE__), array(), '1.0.0', true);                
    }


    function create_course_menus(){
        
        add_menu_page(
            __("", 'tg-lms'),
            __("Courses", 'tg-lms'),
            'manage_options',
            'tg-lms',
            array($this, 'tg_render_react'),
            1
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


    public function tg_all_courses_handle(){
        ob_start();
        include_once '/includes/Client/public/all-courses.php';
        echo $html = ob_get_clean();
        return $html;
    }



    public function tg_render_react(){
        
        ob_start();
        include_once 'src/Front/public/index.php';
        echo $html = ob_get_clean();
        return $html;
        
    }
}