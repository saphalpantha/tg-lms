<?php

use TgRoot\Admin\Course\Course;
global $post;

$course_meta_data = get_post_meta($post->ID);
$image_url = isset($course_meta_data['course_image'][0]) ? wp_get_attachment_url($course_meta_data['course_image'][0]) : '';
$course_price = isset($course_meta_data['course_price'][0]) ? $course_meta_data['course_price'][0] : '';
$course_status = isset($course_meta_data['course_status'][0]) ? $course_meta_data['course_status'][0] : '';


if (have_posts()) : while (have_posts()) : the_post(); 
?>

<div class="flex items-center justify-center h-screen">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-4 text-center"><?php the_title(); ?></h1>

        <div class="mb-4">
            <?php the_content(); ?>
        </div>

        <?php if (!empty($image_url)) : ?>
        <div class="mb-4">
            <img src="<?php echo esc_url($image_url); ?>" class="w-full rounded-lg shadow-md"
                alt="<?php the_title_attribute(); ?>" />
        </div>
        <?php endif; ?>

        <div class="mb-4 text-center">
            <span
                class="inline-block bg-blue-500 text-white px-3 py-1 rounded-full mb-2"><?php echo esc_html($course_status); ?></span>
            <p class="text-gray-700"><strong>Price:</strong> <?php echo esc_html($course_price); ?></p>
        </div>

        <div class="text-center">
            <a href="<?php echo esc_url(get_post_type_archive_link('course')); ?>"
                class="text-blue-500 hover:underline">Back to Courses</a>
        </div>
    </div>
</div>

<?php endwhile; endif;


wp_reset_postdata();
?>