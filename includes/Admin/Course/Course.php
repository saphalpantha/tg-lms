<?php


namespace TgRoot\Admin\Course;

/**
 * Class Course
 *
 * Manages course-related operations, including CRUD operations, filtering, and searching.
 *
 * @package TgRoot\Admin\Course
 * @since 1.0.0
 */
class Course {

    private $course_id;
    private $course_title;
    private $course_description;
    private $course_image;
    private $course_price;
    private $course_category;
    private $course_status;

    /**
     * Course constructor.
     *
     * Initializes the Course class.
     *
     * @since 1.0.0
     */
    function __construct() {}

    /**
     * Deletes a course by ID.
     *
     * @since 1.0.0
     * @param int $id The ID of the course to delete.
     * @return WP_Error|WP_REST_Response
     */
    function delete_course_by_id($id) {
        if(!$id){
            return new WP_Error('delete_error', 'Failed to Delete post');
        }
        $res = wp_delete_post($id);
        if (!$res) {
            return wp_send_json_error(__('Failed to delete post', 'tg-lms') . " $id");
        }
        return rest_ensure_response(__('Successfully deleted post', 'tg-lms') . " $id");
    }

    /**
     * Handles the file upload for a course.
     *
     * @since 1.0.0
     * @return WP_Error|int The attachment ID or an error.
     */
    function handle_file_upload() {
        $upload_overrides = array('test_form' => false);
        
        error_log(print_r($this->course_image, true));
        if (!function_exists('wp_handle_sideload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
 
        $movefile = wp_handle_sideload($this->course_image, $upload_overrides);
        
        if (isset($movefile['url'])) {
            $args = array(
                'guid' => $movefile['url'],
                'post_mime_type' => $this->course_image['type'],
                'post_title' => $this->course_image['name'],
                'post_status' => 'inherit',
            );
            
            $res = wp_insert_attachment($args, $movefile['file']);
            
            if (is_wp_error($res)) {
                return new WP_Error('image_upload_error', __('Failed to insert image', 'tg-lms'), array('status' => 500));
            }
            
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($res, $movefile['file']);
            wp_update_attachment_metadata($res, $attach_data);

            return $res;
        }

        return new WP_Error('image_upload_error', __('Failed to upload image', 'tg-lms'), array('status' => 500));
    }

    /**
     * Retrieves all categories.
     *
     * @since 1.0.0
     * @return WP_REST_Response A list of category names.
     */
    function get_all_categories() {
       $all_cat =  get_categories(array('orderby' => 'name', 'order' => 'ASC'));

       $category_names = array();
       foreach($all_cat as $cat) {
             $category_names[]= $cat->name;
       }

       return rest_ensure_response($category_names);
    }

    /**
     * Retrieves all courses.
     *
     * @since 1.0.0
     * @return WP_REST_Response A list of all courses.
     */
  function get_all_courses() {
        $all_posts = get_posts(array("post_type" => "tg-course"));
        foreach ($all_posts as $p) {
            $p->meta_data = get_post_meta($p->ID);
            $p->meta_data['course_image_url'] = wp_get_attachment_url($p->meta_data['course_image'][0]);
        }
        if (is_wp_error($all_posts)) {
            return new WP_Error('fetch_post_failed', __('Failed to retrieve posts', 'tg-lms'));
        }
        return rest_ensure_response($all_posts);
    }

    /**
     * Retrieves a course by ID.
     *
     * @since 1.0.0
     * @param int $id The ID of the course.
     * @return WP_REST_Response|WP_Error The course data or an error.
     */
     
    function get_course_by_id($id) {
        $single_post = get_post($id);
        $single_post->meta_data = get_post_meta($single_post->ID);
        $single_post->meta_data['course_image_url'] = wp_get_attachment_url($single_post->meta_data['course_image'][0]);
        if(is_wp_error($single_post)){
            return new WP_Error('fetch_error', __('Failed to fetch course', 'tg-lms') . ' ' . $id);
        }
        return rest_ensure_response($single_post);
    }



    /**
     * Sorts courses by title.
     *
     * @since 1.0.0
     * @return WP_REST_Response A list of sorted courses.
     */
        public function sort_course_by_title() {
        $args = array(
            'post_type' => 'tg_course',
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
        );

        $query = new \WP_Query($args);
        $courses = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = $query->post;
                $post->meta_data = get_post_meta($post->ID);
                $post->meta_data['course_image_url'] = wp_get_attachment_url($post->meta_data['course_image'][0]);
                $courses[] = $post;
            }
            wp_reset_postdata();
        }

        return rest_ensure_response(array(
            'courses' => $courses,
        ));
    }

    /**
     * Retrieves paginated courses.
     *
     * @since 1.0.0
     * @param int $page The page number.
     * @param int $posts_per_page The number of posts per page.
     * @return WP_REST_Response The paginated list of courses.
     */
   public function get_paginated_courses($page, $posts_per_page) {
        $args = array(
            'post_type' => 'tg_course',
            'paged' => $page,
            'posts_per_page' => $posts_per_page,
        );

        $query = new \WP_Query($args);
        $courses = $query->posts;

        foreach ($courses as $course) {
            $course->meta_data = get_post_meta($course->ID);
            $course->meta_data['course_image_url'] = wp_get_attachment_url($course->meta_data['course_image'][0]);
        }

        if (is_wp_error($courses)) {
            return rest_ensure_response(new WP_Error('retrieve_failed', __('Failed to retrieve posts', 'tg-lms')), 500);
        }

        return rest_ensure_response(array(
            'courses' => $courses,
            'total_posts' => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'current_page' => $page,
        ));
    }
     /**
     * Searches courses by a keyword.
     *
     * @since 1.0.0
     * @param string $search_query The search query.
     * @return WP_REST_Response The search results.
     */
    public function search_courses($search_query) {
        $args = array(
            'post_type' => 'tg_course',
            's' => $search_query,
        );

        $query = new \WP_Query($args);
        $courses = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = $query->post;
                $post->meta_data = get_post_meta($post->ID);
                $post->meta_data['course_image_url'] = wp_get_attachment_url($post->meta_data['course_image'][0]);
                $courses[] = $post;
            }
            wp_reset_postdata();
        }

        return rest_ensure_response(array(
            'courses' => $courses,
        ));
    }


    /**
     * Filters courses by tag.
     *
     * @since 1.0.0
     * @param string $tag The tag to filter by.
     * @return array The filtered courses.
     */
    public function filter_courses_by_category($cat) {
        $args = array(
            'post_type' => 'tg_course',
            'tag' => $cat,
        );
        $query = new \WP_Query($args);
        $courses = $query->posts;

        return rest_ensure_response(array(
            'courses' => $courses,
        ));
    }

    /**
     * Creates a new course with the given data.
     *
     * @since 1.0.0
     * @param array $user_input The course data.
     * @return WP_REST_Response|WP_Error The result of the creation operation.
     */
    public function create_course($user_input) {
        $this->course_image = $user_input['course_image'];
        
        $attach_id = null;
        
        if ($this->course_image) {
            $attach_id = $this->handle_file_upload();
            if (is_wp_error($attach_id)) {
                return rest_ensure_response(new WP_Error('file_upload_failed', __('File upload failed', 'tg-lms')), 500);
            }
        }

        $post = array(
            "post_content" => $user_input['course_description'],
            "post_title" => $user_input['course_title'],
            "post_excerpt" => $user_input['course_category'],
            'post_status' => 'publish',
            'post_type' => 'tg_course',
            'meta_input' => array(
                'course_image' => $attach_id,
                'course_price' => $user_input['course_price'],
                'course_status' => $user_input['course_status'],
            ),
        );

        $res = wp_insert_post($post);

        if (is_wp_error($res)) {
            return rest_ensure_response(new WP_Error('creation_failed', __('Failed to create post', 'tg-lms')), 500);
        }

        return rest_ensure_response(array("message" => __('Course created successfully', 'tg-lms'), "course" => $post));
    }
    /**
     * Updates a course with new data.
     *
     * @since 1.0.0
     * @param array $user_input The course data to update.
     * @return WP_REST_Response|WP_Error The result of the update operation.
     */
     
    public function update_course($user_input) {
        $this->course_image = $user_input['course_image'];
        
        $attach_id = null;
        
        if ($this->course_image) {
            $attach_id = $this->handle_file_upload();
            if (is_wp_error($attach_id)) {
                return rest_ensure_response(new WP_Error('file_upload_failed', __('File upload failed', 'tg-lms')), 500);
            }
        }

        $post = array(
            'ID' =>  $user_input['course_id'],
            "post_content" => $user_input['course_description'],
            "post_title" => $user_input['course_title'],
            "post_excerpt" => $user_input['course_category'],
            'post_status' => 'publish',
            'post_type' => 'tg_course',
            'meta_input' => array(
                'course_image' => $attach_id,
                'course_price' => $user_input['course_price'],
                'course_status' => $user_input['course_status'],
            ),
        );

        $res = wp_insert_post($post);

        if (is_wp_error($res)) {
            return rest_ensure_response(new WP_Error('creation_failed', __('Failed to Update post', 'tg-lms')), 500);
        }

        return rest_ensure_response(array("message" => __('Course Updated successfully', 'tg-lms'), "course" => $post));
    }
}