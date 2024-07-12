<?php
$args = array(
    'post_type' => 'tg_course',
    'posts_per_page' => 5,
    'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1
);

$course_query = new WP_Query($args);

if ($course_query->have_posts()) :
    while ($course_query->have_posts()) :
        $course_query->the_post();

        
        $course_meta_data = get_post_meta(get_the_ID());
        $course_image = isset($course_meta_data['course_image'][0]) ? wp_get_attachment_image_src($course_meta_data['course_image'][0], 'thumbnail') : '';
        $course_price = isset($course_meta_data['course_price'][0]) ? $course_meta_data['course_price'][0] : '';
        $course_status = isset($course_meta_data['course_status'][0]) ? $course_meta_data['course_status'][0] : '';

?>
<div class="bg-white rounded-lg shadow-md p-4 mb-4">
    <?php if ($course_image) : ?>
    <img src="<?php echo esc_url($course_image[0]); ?>" class="w-full rounded-md mb-2"
        alt="<?php the_title_attribute(); ?>" />
    <?php endif; ?>
    <h2 class="text-2xl font-semibold mb-2"><?php the_title(); ?></h2>
    <p class="text-gray-700 mb-2"><?php echo esc_html($course_price); ?></p>
    <span
        class="inline-block bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs mb-2"><?php echo esc_html($course_status); ?></span>
    <p class="text-gray-600 mb-4"><?php the_excerpt(); ?></p>
    <a href="<?php the_permalink(); ?>" class="text-blue-500 hover:underline">Read More</a>
</div>
<?php
    endwhile;

    echo '<div class="pagination">';
    echo paginate_links(array(
        'total' => $course_query->max_num_pages,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ));
    echo '</div>'; 
else :
    echo '<p>No courses found.</p>';
endif;


wp_reset_postdata();
?>