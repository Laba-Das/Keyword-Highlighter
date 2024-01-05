<?php
/*
Plugin Name: Keyword Highlighter
Plugin URI: https://www.Teckshop.net/our-plugin/
Description: Automatically highlights keywords in posts.
Version: 1.0.0
Requires at least: 5.2
Requires PHP:      7.2
Author: Teckshop.net
Author URI: https://teckshop.net/
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Update URI:        https://www.Teckshop.net/our-plugin/
Text Domain:       my-basics-plugin
Domain Path:       /languages
*/

// Add meta box to post creation/editing page sidebar
// Add meta box to post creation/editing page sidebar




function keyword_highlighter_add_meta_box() {
    add_meta_box(
        'keyword_highlighter_meta_box',
        'Keyword Highlighter',
        'keyword_highlighter_meta_box_callback',
        'post',
        'high'    // Set a higher priority to display it after the summary box
    );
}
add_action('add_meta_boxes', 'keyword_highlighter_add_meta_box');


// Meta box callback function
function keyword_highlighter_meta_box_callback($post) {
    // Retrieve the stored keyword
    $keyword = get_post_meta($post->ID, 'keyword_highlighter_keyword', true);
    
    // Display the input field
    echo '<input type="text" name="keyword_highlighter_keyword" value="' . esc_attr($keyword) . '" />';
}

// Save keyword to post metadata
function keyword_highlighter_save_keyword($post_id) {
    if (isset($_POST['keyword_highlighter_keyword'])) {
        $keyword = sanitize_text_field($_POST['keyword_highlighter_keyword']);
        update_post_meta($post_id, 'keyword_highlighter_keyword', $keyword);
    }
}
add_action('save_post', 'keyword_highlighter_save_keyword');

// Highlight keywords in post content
function keyword_highlighter_highlight_keywords($content) {
    if (is_single()) {
        // Retrieve the keyword
        $keyword = get_post_meta(get_the_ID(), 'keyword_highlighter_keyword', true);

        // Highlight keyword occurrences
        if (!empty($keyword)) {
            // Exclude headings and image alt text from highlighting
            $content = preg_replace_callback(
                '/<h[1-6][^>]*>.*?<\/h[1-6](*SKIP)(*F)|<img\s[^>]*?alt=[\'"]([^\'"]*)[\'"][^>]*>(*SKIP)(*F)|\b' . preg_quote($keyword) . '\b/i',
                function ($match) use ($keyword) {
                    if (strtolower($match[0]) === strtolower($keyword)) {
                        return '<span class="keyword-highlight">' . $match[0] . '</span>';
                    } else {
                        return $match[0];
                    }
                },
                $content
            );
        }
    }

    return $content;
}
add_filter('the_content', 'keyword_highlighter_highlight_keywords');



// Enqueue CSS styles
function keyword_highlighter_enqueue_styles() {
    echo '<style>
        .keyword-highlight {
            
            font-weight: bold;
        }
    </style>';
}
add_action('wp_head', 'keyword_highlighter_enqueue_styles');


