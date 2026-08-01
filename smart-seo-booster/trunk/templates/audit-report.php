<?php
/**
 * Site-wide SEO audit dashboard.
 *
 * Included from Smart_SEO_Admin_UI::render_audit_report(); all variables are
 * prefixed so static analysis does not treat them as unprefixed globals.
 */
defined('ABSPATH') || exit;

// Refresh cache when requested (read-only, nonce-guarded).
$smart_seo_fresh = false;
if ( isset( $_GET['refresh'], $_GET['_ssbnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_ssbnonce'] ) ), 'smart_seo_audit_refresh' ) ) {
    $smart_seo_fresh = true;
}

$smart_seo_data = Smart_SEO_Audit::gather( $smart_seo_fresh );

$smart_seo_avg   = (int) $smart_seo_data['avg'];
$smart_seo_color = $smart_seo_avg >= 80 ? 'var(--ssb-good)' : ( $smart_seo_avg >= 60 ? '#22c55e' : ( $smart_seo_avg >= 40 ? 'var(--ssb-warn)' : 'var(--ssb-bad)' ) );
$smart_seo_r     = 52;
$smart_seo_circ  = 2 * M_PI * $smart_seo_r;
$smart_seo_off   = $smart_seo_circ * ( 1 - $smart_seo_avg / 100 );

$smart_seo_pct = static function ( $smart_seo_n, $smart_seo_total ) {
    return $smart_seo_total > 0 ? round( ( $smart_seo_n / $smart_seo_total ) * 100 ) : 0;
};

/** Small stat-tile printer. */
$smart_seo_tile = static function ( $smart_seo_icon, $smart_seo_num, $smart_seo_label, $smart_seo_mod = '' ) {
    printf(
        '<div class="ssb-card"><div class="ssb-stat %s"><span class="ssb-ico"><span class="dashicons dashicons-%s"></span></span><div><div class="ssb-num">%s</div><div class="ssb-lbl">%s</div></div></div></div>',
        esc_attr( $smart_seo_mod ),
        esc_attr( $smart_seo_icon ),
        esc_html( $smart_seo_num ),
        esc_html( $smart_seo_label )
    );
};

$smart_seo_refresh_url = wp_nonce_url( admin_url( 'admin.php?page=smart-seo-audit&refresh=1' ), 'smart_seo_audit_refresh', '_ssbnonce' );
?>
<div class="wrap smart-seo-audit">
    <div class="ssb-app ssb-adapt">

        <div class="ssb-head">
            <h1><img src="<?php echo esc_url( SMART_SEO_BOOSTER_LINE_ICON_URL ); ?>" width="26" height="26" alt="" class="ssb-h1-icon" /> <?php esc_html_e( 'SEO Audit', 'smart-seo-booster' ); ?></h1>
            <div>
                <span class="ssb-sub">
                    <?php
                    /* translators: %d: number of items scanned */
                    printf( esc_html( _n( 'Scanned %d item', 'Scanned %d items', (int) $smart_seo_data['total'], 'smart-seo-booster' ) ), (int) $smart_seo_data['total'] );
                    ?>
                </span>
                <a href="<?php echo esc_url( $smart_seo_refresh_url ); ?>" class="button" style="margin-inline-start:10px;">
                    <span class="dashicons dashicons-update" style="vertical-align:text-bottom;"></span> <?php esc_html_e( 'Refresh', 'smart-seo-booster' ); ?>
                </a>
            </div>
        </div>

        <div class="ssb-two-col">
            <!-- Average score gauge -->
            <div class="ssb-card">
                <h2><?php esc_html_e( 'Average SEO Score', 'smart-seo-booster' ); ?></h2>
                <div class="ssb-gauge">
                    <svg width="120" height="120" viewBox="0 0 120 120" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: score */ __( 'Average score %d out of 100', 'smart-seo-booster' ), $smart_seo_avg ) ); ?>">
                        <circle cx="60" cy="60" r="<?php echo esc_attr( $smart_seo_r ); ?>" fill="none" stroke="var(--ssb-line)" stroke-width="12" />
                        <circle cx="60" cy="60" r="<?php echo esc_attr( $smart_seo_r ); ?>" fill="none" stroke="<?php echo esc_attr( $smart_seo_color ); ?>" stroke-width="12" stroke-linecap="round"
                            stroke-dasharray="<?php echo esc_attr( $smart_seo_circ ); ?>" stroke-dashoffset="<?php echo esc_attr( $smart_seo_off ); ?>"
                            transform="rotate(-90 60 60)" />
                    </svg>
                    <div>
                        <div class="ssb-gauge-num" style="color:<?php echo esc_attr( $smart_seo_color ); ?>;"><?php echo esc_html( $smart_seo_avg ); ?><span style="font-size:16px;color:var(--ssb-muted);">/100</span></div>
                        <div class="ssb-gauge-cap"><?php esc_html_e( 'Across published posts &amp; pages', 'smart-seo-booster' ); ?></div>
                    </div>
                </div>
            </div>

            <!-- Score distribution -->
            <div class="ssb-card">
                <h2><?php esc_html_e( 'Score Distribution', 'smart-seo-booster' ); ?></h2>
                <div class="ssb-bars">
                    <?php
                    $smart_seo_rows = [
                        'excellent' => __( 'Excellent', 'smart-seo-booster' ),
                        'good'      => __( 'Good', 'smart-seo-booster' ),
                        'needs'     => __( 'Needs work', 'smart-seo-booster' ),
                        'poor'      => __( 'Poor', 'smart-seo-booster' ),
                    ];
                    foreach ( $smart_seo_rows as $smart_seo_key => $smart_seo_lbl ) :
                        $smart_seo_count = (int) $smart_seo_data['buckets'][ $smart_seo_key ];
                        $smart_seo_bpct  = $smart_seo_pct( $smart_seo_count, $smart_seo_data['total'] );
                        ?>
                        <div class="ssb-bar-row">
                            <span><?php echo esc_html( $smart_seo_lbl ); ?></span>
                            <span class="ssb-bar-track"><span class="ssb-bar-fill <?php echo esc_attr( $smart_seo_key ); ?>" style="inline-size:<?php echo esc_attr( $smart_seo_bpct ); ?>%;"></span></span>
                            <span><?php echo esc_html( $smart_seo_count ); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Stat tiles -->
        <div class="ssb-grid">
            <?php
            $smart_seo_tile( 'admin-page', (int) $smart_seo_data['total'], __( 'Published items', 'smart-seo-booster' ) );
            $smart_seo_tile( 'format-image', (int) $smart_seo_data['images_no_alt'], __( 'Images missing alt', 'smart-seo-booster' ), $smart_seo_data['images_no_alt'] > 0 ? 'is-warn' : 'is-good' );
            $smart_seo_tile( 'editor-alignleft', (int) $smart_seo_data['missing_desc'], __( 'No meta description', 'smart-seo-booster' ), $smart_seo_data['missing_desc'] > 0 ? 'is-warn' : 'is-good' );
            $smart_seo_tile( 'text-page', (int) $smart_seo_data['thin_content'], __( 'Thin content', 'smart-seo-booster' ), $smart_seo_data['thin_content'] > 0 ? 'is-warn' : 'is-good' );
            $smart_seo_tile( 'hidden', (int) $smart_seo_data['noindex'], __( 'No-indexed', 'smart-seo-booster' ) );
            $smart_seo_tile( 'images-alt2', (int) $smart_seo_data['images_total'], __( 'Total images', 'smart-seo-booster' ) );
            ?>
        </div>

        <div class="ssb-two-col">
            <!-- Opportunities -->
            <div class="ssb-card">
                <h2><?php esc_html_e( 'Opportunities', 'smart-seo-booster' ); ?></h2>
                <ul class="ssb-opps">
                    <?php foreach ( $smart_seo_data['opportunities'] as $smart_seo_op ) : ?>
                        <li class="<?php echo esc_attr( $smart_seo_op['level'] ); ?>">
                            <span class="dashicons dashicons-<?php echo esc_attr( $smart_seo_op['icon'] ); ?>"></span>
                            <span><?php echo esc_html( $smart_seo_op['text'] ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Lowest scoring -->
            <div class="ssb-card">
                <h2><?php esc_html_e( 'Needs attention first', 'smart-seo-booster' ); ?></h2>
                <table class="ssb-table">
                    <thead><tr><th><?php esc_html_e( 'Content', 'smart-seo-booster' ); ?></th><th><?php esc_html_e( 'Score', 'smart-seo-booster' ); ?></th></tr></thead>
                    <tbody>
                        <?php if ( empty( $smart_seo_data['lowest'] ) ) : ?>
                            <tr><td colspan="2"><?php esc_html_e( 'No published content yet.', 'smart-seo-booster' ); ?></td></tr>
                        <?php else : ?>
                            <?php foreach ( $smart_seo_data['lowest'] as $smart_seo_row ) :
                                $smart_seo_bucket = $smart_seo_row['score'] >= 80 ? 'excellent' : ( $smart_seo_row['score'] >= 60 ? 'good' : ( $smart_seo_row['score'] >= 40 ? 'needs' : 'poor' ) );
                                ?>
                                <tr>
                                    <td><a href="<?php echo esc_url( (string) get_edit_post_link( $smart_seo_row['id'] ) ); ?>"><?php echo esc_html( $smart_seo_row['title'] ); ?></a></td>
                                    <td><span class="ssb-pill <?php echo esc_attr( $smart_seo_bucket ); ?>"><?php echo esc_html( $smart_seo_row['score'] ); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
