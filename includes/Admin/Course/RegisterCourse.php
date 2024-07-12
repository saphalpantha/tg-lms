<?php

namespace TgRoot\Admin\Course;

/**
 * Class RegisterCourse
 *
 * Register the custom post type tg-couse
 *
 * @package TgRoot\Admin\Course
 * @since 1.0.0
 */
class RegisterCourse
{
    private $post_type;

    /**
     * RegisterCourse constructor.
     *
     * @since 1.0.0
     * @param string $post_type The custom post type to register.
     */
    function __construct($post_type)
    {
        $this->post_type = $post_type;
        add_action('init', array($this, 'reg_custom_post_type'));
    }

    /**
     * Registers the custom post type.
     *
     * @since 1.0.0
     * @return void
     */
    public function reg_custom_post_type()
    {
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

        $res = register_post_type('tg_course', $args);
    }
}