<?php

if (!defined('ABSPATH')) {
	exit;
}

add_action('wp_enqueue_scripts', 'trvlr_website_conditional_reefinfo_styles', 20);

function trvlr_website_conditional_reefinfo_styles()
{
	if (!trvlr_website_conditional_active_theme_is('theme-2')) {
		return;
	}

	$css = <<<'CSS'
@layer trvlr.custom {
  :root {
    --trvlr-content-max-width: 1280px;
    --trvlr-color-foreground-on-image: var(--trvlr-color-primary);
    --trvlr-font-family: inherit;
    --trvlr-heading-font-family: "Inter", sans-serif;
    --trvlr-heading-letter-spacing: -0.04em;
    --trvlr-content-gutter: 24px;
    --trvlr-price-font-size: 20px;
    --trvlr-price-font-weight: 800;
    --trvlr-price-type-font-weight: 600;
  }
  @media (min-width: 768px) {
    :root {
      --trvlr-content-gutter: 30px;
    }
  }
  @media (min-width: 1200px) {
    :root {
      --trvlr-content-gutter: 40px;
    }
  }
  :root:has(.trvlr--theme-2) {
    --trvlr-color-border: #ddd;
    --trvlr-badge-background: var(--trvlr-color-accent);
    --trvlr-badge-foreground: var(--trvlr-color-accent-foreground);
    --trvlr-card-font-family: var(--trvlr-heading-font-family);
    --trvlr-card-title-font-weight: 700;
    --trvlr-badge-padding: 0.4em 1em;
    --trvlr-badge-font-size: 12px;
    --trvlr-badge-font-weight: 600;
    --trvlr-badge-letter-spacing: -0.02em;
    --trvlr-card-badge-padding: 0.4em 1em;
    --trvlr-card-sale-badge-padding: 0.4em 0.8em;
    --trvlr-card-sale-badge-font-weight: 700;
    --trvlr-card-badge-border-radius: 0;
    --trvlr-card-badge-font-size: 10px;
    --trvlr-card-badge-font-weight: 600;
    --trvlr-card-badge-letter-spacing: -0.02em;
    --trvlr-card-popular-badge-background: #000;
    --trvlr-card-popular-badge-foreground: #fff;
    --trvlr-card-cta-background: #000;
    --trvlr-card-cta-color: #fff;
  }
}
.trvlr-badge {
  font-family: var(--trvlr-heading-font-family);
}
.trvlr-attraction-filter .filter-btn {
  border-radius: 50px;
}
.trvlr-popular-badge, .trvlr-sale__badge {
  text-transform: uppercase;
  line-height: 1.7;
}
.trvlr-card .trvlr-popular-badge .trvlr-icon {
  width: 10px;
  height: 10px;
}
CSS;

	trvlr_website_conditional_enqueue_css($css);
}
