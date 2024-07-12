<?php

namespace TgRoot\Admin\Course\Api;

use TgRoot\Admin\Course\Course as MainCourse;

/**
 * Class CourseApi
 *
 * Handles REST API endpoints for course LMS.
 *
 * @since 1.0.0
 */
class CourseApi {

    /**
     * CourseApi constructor.
     *
     * Initializes the CourseApi class and hooks into REST API initialization.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action("rest_api_init", array($this, "crud_post_init"));
    }

    /**
     * Fetch all courses.
     *
     * @since 1.0.0
     * @return array List of courses
     */
    public function get_all_courses_h() {
        $course = new MainCourse();
        return $course->get_all_courses();
    }

    /**
     * Creates a new course.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response
     */
    public function create_course_h($request) {
        $user_input = $request->get_params();
        
        $errors = array();

        $sanitized_user_input = array(
            "course_title"       => sanitize_text_field($user_input["course_title"]),
            "course_description" => sanitize_textarea_field($user_input["course_description"]),
            "course_price"       => sanitize_text_field($user_input["course_price"]),
            "course_category"    => sanitize_text_field($user_input["course_category"]),
            "course_status"      => sanitize_text_field($user_input["course_status"]),
        );

        //error_log(print_r($sanitized_user_input, true));
        //$sanitized_user_input = apply_filters('tg_course_sanitized_input', $sanitized_user_input);

        //error_log(print_r($sanitized_user_input, true));

        $sanitized_user_input["course_image"] = $_FILES['course_image'];

    foreach ($sanitized_user_input as $key => $val) {
        if (empty($val)) {
            $errors[] = $key;
        }
    }
        if (!empty($errors)) {
            wp_send_json_error("Missing required fields: " . implode(", ", $errors));
            return;
        }

        $course = new MainCourse();
        return $course->create_course($sanitized_user_input);
    }

    /**
     * Retrieves a course by ID.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response
     */
    public function get_course_by_id_h($request) {
        $course = new MainCourse();
        $id = $request['id'];
        return $course->get_course_by_id($id);
    }

    /**
     * Deletes a course by ID.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Responsee
     */
    public function delete_course_by_id_h($request) {
        $id = $request['id'];
        $course = new MainCourse();
        return $course->delete_course_by_id($id);
    }

    /**
     * Updates an existing course.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response
     */
    public function update_course_h($request) {
        
        $user_input = $request->get_params();
        
        $errors = array();

        $user_input = array(
            "course_id"         => $user_input["course_id"],
            "course_title"       => $user_input["course_title"],
            "course_description" => $user_input["course_description"],
            "course_price"       => $user_input["course_price"],
            "course_category"    => $user_input["course_category"],
            "course_status"      => $user_input["course_status"],
            "course_image"       => $_FILES['course_image'],
        );
        
        foreach ($user_input as $key => $val) {
            if ($key === "course_image") {
                if (!isset($_FILES[$key])) {
                    $errors[] = $key;
                }
            } else {
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
            "course_title"       => sanitize_text_field($user_input["course_title"]),
            "course_description" => sanitize_textarea_field($user_input["course_description"]),
            "course_price"       => sanitize_text_field($user_input["course_price"]),
            "course_category"    => sanitize_text_field($user_input["course_category"]),
            "course_status"      => sanitize_text_field($user_input["course_status"]),
            "course_image"       => $user_input["course_image"],
        );

        $id = $request['id'];
        $sanitized_user_input["course_id"] =  $id;
        $course = new MainCourse();
        return $course->update_course($sanitized_user_input);
    }

    /**
     * Searches courses by keyword.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response array List of courses matches the search query
     */
    public function search_courses_h($request) {
        $search_query = sanitize_text_field($request->get_param('q'));
        $course = new MainCourse();
        return $course->search_courses($search_query);
    }

    /**
     * Filters courses by category.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response array List of courses filtered by category
     */
    public function filter_courses_by_category_h($request) {
        $category = sanitize_text_field($request->get_param('cat'));
        $course = new MainCourse();
        return $course->filter_courses_by_category($category);
    }

    /**
     * Retrieves paginated courses.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response array Paginated list of courses
     */
    public function get_paginated_courses_h($request) {
        $course = new MainCourse();
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $posts_per_page = isset($request['posts_per_page']) ? intval($request['posts_per_page']) : 1;
        return $course->get_paginated_courses($page, $posts_per_page);
    }

    /**
     * Sorts courses by title.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response array List of sorted courses
     */
    public function sort_course_by_title_h($request) {
        $course = new MainCourse();
        return $course->sort_course_by_title();
    }

    /**
     * Filters courses by price type.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response array List of courses filtered by price type
     */
    public function filter_courses_by_price_type_h($request) {
        $price_type = sanitize_text_field($request->get_param('price_type'));
        $course = new MainCourse();
        return $course->filter_courses_by_price_type('course_status', $price_type);
    }

    /**
     * Retrieves all course categories.
     *
     * @since 1.0.0
     * @return WP_REST_Response array List of course categories
     */
    public function get_all_categories_h() {
        $course = new MainCourse();
        return $course->get_all_categories();
    }

    /**
     * Registers REST API endpoints for courses and categories.
     *
     * @since 1.0.0
     * @return void
     */
    public function crud_post_init() {
        register_rest_route(
            'tg-course/v1',
            '/course',
            array(
                array(
                    'methods' => 'GET',
                    'callback' => array($this, 'get_all_courses_h'),
                    'permission_callback' => function () {
                        return true;
                    }
                ),
                array(
                    'methods' => 'POST',
                    'callback' => array($this, 'create_course_h'),
                    'permission_callback' => function () {
                        return true;
                    }
                ),
            )
        );

        register_rest_route(
            'tg-course/v1',
            '/categories',
            array(
                array(
                    'methods' => 'GET',
                    'callback' => array($this, 'get_all_categories_h'),
                    'permission_callback' => function () {
                        return true;
                    }
                ),
            )
        );

        register_rest_route(
    'tg-course/v1',
    '/course/edit/(?P<id>\d+)',
    array(
        'methods'             => 'PUT',
        'callback'            => array($this, 'update_course_h'),
        'permission_callback' => function () {
                    return true;
        },

    )
);

        register_rest_route(
            'tg-course/v1',
            '/course/(?P<id>\d+)',
            array(
                array(
                    'methods' => 'GET',
                    'callback' => array($this, 'get_course_by_id_h'),
                    'permission_callback' => function () {
                        return true;
                    }
                ),
                array(
                    'methods' => 'DELETE',
                    'callback' => array($this, 'delete_course_by_id_h'),
                    'permission_callback' => function () {
                        return true;
                    },
                    'args' => array(
                        'id' => array(
                            'required' => false,
                            'validate_callback' => function ($param, $request, $key) {
                                return is_numeric($param);
                            }
                        ),
                    ),
                ),
            ),
        );

        register_rest_route(
            'tg-course/v1',
            '/courses/search',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'search_courses_h'),
                'permission_callback' => function () {
                    return true;
                }
            )
        );

        register_rest_route(
            'tg-course/v1',
            '/courses/sort',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'sort_course_by_title_h'),
                'permission_callback' => function () {
                    return true;
                }
            )
        );

        register_rest_route(
            'tg-course/v1',
            '/courses/filter',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'filter_courses_by_category_h'),
                'permission_callback' => function () {
                    return true;
                }
            )
        );

        register_rest_route(
            'tg-course/v1',
            '/courses/filter/status',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'filter_courses_by_price_type_h'),
                'permission_callback' => function () {
                    return true;
                }
            )
        );

        register_rest_route(
            'tg-course/v1',
            '/courses/',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_paginated_courses_h'),
                'permission_callback' => function () {
                    return true;
                },
                'args' => array(
                    'page' => array(
                        'required' => false,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                    'posts_per_page' => array(
                        'required' => false,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                ),
            )
        );
    }
}