<?php
defined('ABSPATH') || exit;

/**
 * Bulk SEO editor — edit SEO titles and meta descriptions for many posts
 * on a single paginated screen.
 */
class Smart_SEO_Bulk_Editor {

    const NONCE   = 'smart_seo_bulk';
    const PER_PAGE = 20;

    public static function init() {
        add_action('admin_post_smart_seo_bulk_save', [__CLASS__, 'save']);
    }

    public static function save() {
        if ( ! current_user_can('edit_others_posts') ) {
            wp_die( esc_html__('You do not have sufficient permissions.', 'smart-seo-booster') );
        }
        check_admin_referer( self::NONCE, 'smart_seo_bulk_nonce' );

        $paged = isset($_POST['paged']) ? absint( wp_unslash( $_POST['paged'] ) ) : 1;

        // Structure: meta[ post_id ][ title|description ].
        $rows = isset($_POST['meta']) && is_array($_POST['meta'] ) ? wp_unslash( $_POST['meta'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below

        foreach ( $rows as $post_id => $fields ) {
            $post_id = absint( $post_id );
            if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
                continue;
            }
            if ( isset( $fields['title'] ) ) {
                update_post_meta( $post_id, '_smart_seo_title', sanitize_text_field( $fields['title'] ) );
            }
            if ( isset( $fields['description'] ) ) {
                update_post_meta( $post_id, '_smart_seo_description', sanitize_textarea_field( $fields['description'] ) );
            }
        }

        wp_safe_redirect( add_query_arg(
            [ 'page' => 'smart-seo-bulk', 'paged' => $paged, 'saved' => 1 ],
            admin_url('admin.php')
        ) );
        exit;
    }

    public static function render_page() {
        if ( ! current_user_can('edit_others_posts') ) {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster') );
        }

        $paged = isset($_GET['paged']) ? max( 1, absint( wp_unslash( $_GET['paged'] ) ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- pagination only

        $query = new WP_Query([
            'post_type'      => [ 'post', 'page' ],
            'post_status'    => [ 'publish', 'draft', 'pending', 'future' ],
            'posts_per_page' => self::PER_PAGE,
            'paged'          => $paged,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        ]);

        echo '<div class="wrap smart-seo-settings ssb-app ssb-adapt">';
        echo '<h1><span class="dashicons dashicons-edit" aria-hidden="true"></span> ' . esc_html__( 'Bulk SEO Editor', 'smart-seo-booster' ) . '</h1>';
        echo '<p>' . esc_html__( 'Edit SEO titles and meta descriptions across your content. Leave a field blank to use the automatic value.', 'smart-seo-booster' ) . '</p>';

        if ( isset($_GET['saved']) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Changes saved.', 'smart-seo-booster' ) . '</p></div>';
        }

        echo '<form method="post" action="' . esc_url( admin_url('admin-post.php') ) . '">';
        wp_nonce_field( self::NONCE, 'smart_seo_bulk_nonce' );
        echo '<input type="hidden" name="action" value="smart_seo_bulk_save" />';
        echo '<input type="hidden" name="paged" value="' . esc_attr( $paged ) . '" />';

        echo '<table class="widefat striped"><thead><tr>';
        echo '<th style="width:22%;">' . esc_html__( 'Content', 'smart-seo-booster' ) . '</th>';
        echo '<th style="width:33%;">' . esc_html__( 'SEO Title', 'smart-seo-booster' ) . '</th>';
        echo '<th>' . esc_html__( 'Meta Description', 'smart-seo-booster' ) . '</th>';
        echo '</tr></thead><tbody>';

        if ( empty( $query->posts ) ) {
            echo '<tr><td colspan="3">' . esc_html__( 'No content found.', 'smart-seo-booster' ) . '</td></tr>';
        } else {
            foreach ( $query->posts as $post ) {
                $title = get_post_meta( $post->ID, '_smart_seo_title', true );
                $desc  = get_post_meta( $post->ID, '_smart_seo_description', true );
                $base  = 'meta[' . $post->ID . ']';

                echo '<tr>';
                echo '<td><strong>' . esc_html( get_the_title( $post ) ) . '</strong><br><a href="' . esc_url( (string) get_edit_post_link( $post->ID ) ) . '">' . esc_html__( 'Edit', 'smart-seo-booster' ) . '</a></td>';
                echo '<td><input type="text" class="large-text" name="' . esc_attr( $base . '[title]' ) . '" value="' . esc_attr( $title ) . '"></td>';
                echo '<td><textarea class="large-text" rows="2" name="' . esc_attr( $base . '[description]' ) . '">' . esc_textarea( $desc ) . '</textarea></td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table>';
        submit_button( __( 'Save All', 'smart-seo-booster' ) );
        echo '</form>';

        // Pagination.
        $total_pages = (int) $query->max_num_pages;
        if ( $total_pages > 1 ) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo wp_kses_post( paginate_links([
                'base'    => add_query_arg( 'paged', '%#%' ),
                'format'  => '',
                'current' => $paged,
                'total'   => $total_pages,
            ]) );
            echo '</div></div>';
        }

        echo '</div>';
    }
}
