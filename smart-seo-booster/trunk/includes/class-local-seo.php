<?php
defined('ABSPATH') || exit;

/**
 * Local SEO — a business card block/shortcode that renders address, phone and
 * hours, and outputs LocalBusiness JSON-LD alongside it.
 */
class Smart_SEO_Local_SEO {

    public static function init() {
        add_shortcode('smart_seo_local_business', [__CLASS__, 'shortcode']);
        add_action('init', [__CLASS__, 'register_block']);
    }

    public static function register_block() {
        if ( ! function_exists('register_block_type') ) {
            return;
        }

        $ver = defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : false;
        wp_register_script(
            'smart-seo-local-block',
            plugin_dir_url(__FILE__) . '../js/local-seo-block.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n'],
            $ver,
            true
        );
        if ( function_exists('wp_set_script_translations') ) {
            wp_set_script_translations('smart-seo-local-block', 'smart-seo-booster');
        }
        wp_localize_script( 'smart-seo-local-block', 'smartSeoLocalBusiness', [
            // Only offer a settings deep link to users who can actually reach it.
            'settingsUrl' => current_user_can( 'manage_options' )
                ? admin_url( 'admin.php?page=smart-seo#panel-smart_seo_schema_data' )
                : '',
        ] );

        register_block_type('smart-seo/local-business', [
            'editor_script'   => 'smart-seo-local-block',
            // Reuses the shared block stylesheet (registered in class-blocks.php)
            // so the editor placeholder gets the same styling as the other blocks.
            'style'           => 'smart-seo-blocks',
            'render_callback' => [__CLASS__, 'render'],
        ]);
    }

    public static function shortcode( $atts ) {
        return self::render();
    }

    /**
     * Render the business card + LocalBusiness schema.
     *
     * @return string
     */
    public static function render() {
        $o = get_option('smart_seo_options', []);

        $name    = get_bloginfo('name');
        $phone   = $o['business_phone']   ?? '';
        $street  = $o['business_street']  ?? '';
        $city    = $o['business_locality'] ?? '';
        $region  = $o['business_region']  ?? '';
        $postal  = $o['business_postal']  ?? '';
        $hours   = ! empty( $o['business_hours'] )
            ? array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $o['business_hours'] ) ) )
            : [];

        $address_line = trim( implode( ', ', array_filter( [ $street, $city, $region, $postal ] ) ), ', ' );

        ob_start();
        ?>
        <div class="smart-seo-local-business">
            <p class="ssb-name"><strong><?php echo esc_html( $name ); ?></strong></p>
            <?php if ( $address_line ) : ?>
                <p class="ssb-address"><?php echo esc_html( $address_line ); ?></p>
            <?php endif; ?>
            <?php if ( $phone ) : ?>
                <p class="ssb-phone"><a href="<?php echo esc_attr( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
            <?php endif; ?>
            <?php if ( ! empty( $hours ) ) : ?>
                <ul class="ssb-hours">
                    <?php foreach ( $hours as $line ) : ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
        $html = ob_get_clean();

        // Emit LocalBusiness schema from the shared template.
        $schema_file = plugin_dir_path(__FILE__) . '../schema/local-business-schema.php';
        if ( file_exists( $schema_file ) ) {
            $schema = require $schema_file;
            if ( is_array( $schema ) ) {
                $html .= "<script type='application/ld+json'>" . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>";
            }
        }

        return $html;
    }
}
