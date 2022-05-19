<?php
/**
 * Server-side rendering of the `core/post-comments` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-comments` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post comments for the current post wrapped inside "p" tags.
 */
function render_block_core_post_comments($attributes, $content, $block)
{
    global $post;

    $post_id = $block->context['postId'];
    if (! isset($post_id)) {
        return '';
    }

    $comment_args = array(
        'post_id' => $post_id,
        'count'   => true,
    );
    // Return early if there are no comments and comments are closed.
    if (! comments_open($post_id) && get_comments($comment_args) === 0) {
        return '';
    }

    $post_before = $post;
    $post        = get_post($post_id);
    setup_postdata($post);

    ob_start();
    // There's a deprecation warning generated by WP Core.
    // Ideally this deprecation is removed from Core.
    // In the meantime, this removes it from the output.
    add_filter('deprecated_file_trigger_error', '__return_false');
    comments_template();
    remove_filter('deprecated_file_trigger_error', '__return_false');
    $post = $post_before;

    $classes = '';
    if (isset($attributes['textAlign'])) {
        $classes .= 'has-text-align-' . $attributes['textAlign'];
    }

    $wrapper_attributes = get_block_wrapper_attributes(array( 'class' => $classes ));
    $output             = ob_get_clean();

    return sprintf('<div %1$s>%2$s</div>', $wrapper_attributes, $output);
}

/**
 * Registers the `core/post-comments` block on the server.
 */
function register_block_core_post_comments()
{
    register_block_type_from_metadata(
        __DIR__ . '/post-comments',
        array(
            'render_callback' => 'render_block_core_post_comments',
        )
    );
}
add_action('init', 'register_block_core_post_comments');

/**
 * Use the button block classes for the form-submit button.
 *
 * @param array $fields The default comment form arguments.
 *
 * @return array Returns the modified fields.
 */
function post_comments_block_form_defaults($fields)
{
    if (wp_is_block_theme()) {
        $fields['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s wp-block-button__link" value="%4$s" />';
        $fields['submit_field']  = '<p class="form-submit wp-block-button">%1$s %2$s</p>';
    }

    return $fields;
}
add_filter('comment_form_defaults', 'post_comments_block_form_defaults');
