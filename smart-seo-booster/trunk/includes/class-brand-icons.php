<?php
defined('ABSPATH') || exit;

/**
 * Small library of inline brand-marker SVGs used as recognizable visual
 * anchors next to third-party integration fields (Search Console, social
 * profiles, Analytics, PageSpeed). These are simplified glyphs/shapes built
 * from primitives (not traced reproductions of each company's registered
 * logo artwork), kept deliberately lightweight since they're inlined on
 * every settings-page render.
 */
class Smart_SEO_Brand_Icons {

    /**
     * @param string $key  One of the known service keys, e.g. 'facebook'.
     * @param int    $size Pixel size (square).
     * @return string HTML span wrapping the inline SVG, or '' if unknown.
     */
    public static function icon( $key, $size = 18 ) {
        $svg = self::svg( $key );
        if ( ! $svg ) {
            return '';
        }
        return '<span class="ssb-brand-ico" style="width:' . (int) $size . 'px;height:' . (int) $size . 'px;">' . $svg . '</span>';
    }

    private static function svg( $key ) {
        switch ( $key ) {
            case 'facebook':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#1877F2"/><path d="M15.6 8.4h-1.5c-.5 0-.7.2-.7.7v1.4h2.1l-.3 2.2h-1.8V19h-2.3v-6.3H9.4v-2.2h1.7V9c0-1.8 1-2.9 2.8-2.9h1.7v2.3Z" fill="#fff"/></svg>';
            case 'twitter':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#000"/><path d="M6 6.5 17.8 18M17.8 6.5 6 18" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>';
            case 'linkedin':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#0A66C2"/><text x="12" y="16.5" font-family="Arial,Helvetica,sans-serif" font-size="10" font-weight="700" fill="#fff" text-anchor="middle">in</text></svg>';
            case 'instagram':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><defs><linearGradient id="ssbIgGrad" x1="0" y1="24" x2="24" y2="0"><stop offset="0" stop-color="#FEE411"/><stop offset=".3" stop-color="#FD5949"/><stop offset=".6" stop-color="#D6249F"/><stop offset="1" stop-color="#285AEB"/></linearGradient></defs><rect width="24" height="24" rx="6" fill="url(#ssbIgGrad)"/><rect x="6" y="6" width="12" height="12" rx="4" fill="none" stroke="#fff" stroke-width="1.5"/><circle cx="12" cy="12" r="3" fill="none" stroke="#fff" stroke-width="1.5"/><circle cx="16.2" cy="7.8" r="1" fill="#fff"/></svg>';
            case 'youtube':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#FF0000"/><path d="M10 8.3 16 12l-6 3.7Z" fill="#fff"/></svg>';
            case 'pinterest':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="12" fill="#E60023"/><text x="12" y="17" font-family="Georgia,serif" font-size="14" font-weight="700" fill="#fff" text-anchor="middle">P</text></svg>';
            case 'google':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="#fff" stroke="#e5e7eb"/><path d="M21.6 12.2c0-.7-.06-1.4-.18-2H12v3.8h5.4a4.6 4.6 0 0 1-2 3v2.5h3.2c1.9-1.7 3-4.3 3-7.3Z" fill="#4285F4"/><path d="M12 22c2.7 0 5-.9 6.6-2.4l-3.2-2.5c-.9.6-2 1-3.4 1-2.6 0-4.8-1.8-5.6-4.1H3.1v2.6A10 10 0 0 0 12 22Z" fill="#34A853"/><path d="M6.4 14a6 6 0 0 1 0-4V7.4H3.1a10 10 0 0 0 0 9.2Z" fill="#FBBC05"/><path d="M12 6a5.4 5.4 0 0 1 3.8 1.5l2.9-2.9A9.6 9.6 0 0 0 12 2a10 10 0 0 0-8.9 5.4l3.3 2.6C7.2 7.8 9.4 6 12 6Z" fill="#EA4335"/></svg>';
            case 'bing':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#008373"/><text x="12" y="16.5" font-family="Georgia,serif" font-size="12" font-weight="700" fill="#fff" text-anchor="middle">b</text></svg>';
            case 'yandex':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="12" fill="#FF0000"/><text x="12" y="16.5" font-family="Arial,Helvetica,sans-serif" font-size="11" font-weight="700" fill="#fff" text-anchor="middle">Y</text></svg>';
            case 'baidu':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#2932E1"/><text x="12" y="16.5" font-family="Arial,Helvetica,sans-serif" font-size="11" font-weight="700" fill="#fff" text-anchor="middle">B</text></svg>';
            case 'ga4':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#F9AB00"/><rect x="6" y="13" width="3" height="6" rx="1" fill="#fff"/><rect x="10.5" y="9" width="3" height="10" rx="1" fill="#fff"/><rect x="15" y="5" width="3" height="14" rx="1" fill="#fff"/></svg>';
            case 'clarity':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#243A5E"/><circle cx="12" cy="12" r="5" fill="none" stroke="#fff" stroke-width="1.5"/><circle cx="12" cy="12" r="1.6" fill="#fff"/></svg>';
            case 'pagespeed':
                return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#0CCE6B"/><path d="M6 15a6 6 0 1 1 12 0" fill="none" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><path d="M12 15 15.5 10.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><circle cx="12" cy="15" r="1.2" fill="#fff"/></svg>';
            default:
                return '';
        }
    }
}
