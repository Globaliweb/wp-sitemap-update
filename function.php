You can run this once using a plugin like Code Snippets or add it temporarily to your theme’s functions.php.
<?php
add_action('init', function () {
    // Only run once and then comment this out
    if (!is_admin()) return;

    global $wpdb;
    
    // Define post types to update (products and pages)
    $post_types = ['product', 'page'];
    
    // Set date threshold (6 months ago)
    $date_threshold = date('Y-m-d H:i:s', strtotime('-6 months'));

    foreach ($post_types as $post_type) {
        $posts = get_posts([
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'date_query' => [
                [
                    'column' => 'post_modified',
                    'before' => $date_threshold,
                ],
            ],
            'fields' => 'ids',
        ]);

        foreach ($posts as $post_id) {
            $now = current_time('mysql');
            $wpdb->update(
                $wpdb->posts,
                ['post_modified' => $now, 'post_modified_gmt' => get_gmt_from_date($now)],
                ['ID' => $post_id]
            );
        }
    }

    echo count($posts) . " posts updated. Please disable this code after execution.";
    exit; // Stop further loading
});
?>
