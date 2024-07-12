import '../../index.css';

jQuery(document).ready(function($) {
    $('#course-search').on('input', function() {
        var searchQuery = $(this).val();

        $.ajax({
            url: tg_lms_ajax.url,
            type: 'POST',
            data: {
                action: 'search_courses',
                search_query: searchQuery
            },
            success: function(response) {
                $('#course-list').html(response);
            }
        });
    });
});
