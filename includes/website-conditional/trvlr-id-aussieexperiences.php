<?php

if (!defined('ABSPATH')) {
  exit;
}

add_action('wp_enqueue_scripts', 'trvlr_website_conditional_aussieexperiences_styles', 20);

function trvlr_website_conditional_aussieexperiences_styles()
{
  $css = <<<'CSS'
  @layer trvlr.custom {
  :root {
    --trvlr-font-family: "Instrument Sans", serif;
    --trvlr-heading-font-family: "DM Serif Display", serif;
    --trvlr-heading-font-weight: 400;
    --trvlr-badge-background: var(--trvlr-color-secondary);
    --trvlr-badge-foreground: #fff;
    --trvlr-color-primary-foreground: #000;
    --trvlr-color-secondary-foreground: #fff;
  }

  }
  .trvlr-attraction-filter {
  --trvlr-color-primary: var(--trvlr-color-secondary);
  --trvlr-color-primary-foreground: var(--trvlr-color-secondary-foreground);
  }
  .trvlr-attraction-filter.tour-filters .filter-btn {
  letter-spacing: 0 !important;
  }
  CSS;

  trvlr_website_conditional_enqueue_css($css);
  if (!trvlr_website_conditional_active_theme_is('theme-3')) {
    return;
  }

  $theme_3_css = <<<'CSS'
  .trvlr-card {
    --trvlr-card-title-font-weight: 700;
  }
  .trvlr-card h3 {
    letter-spacing: 0;
  }
  .trvlr-card.trvlr-card--variant-expanded {
    --trvlr-card-title-font-weight: 400;
    --trvlr-card-title-font-family: "DM Serif Display", serif;
  }

  .trvlr--theme-3 .trvlr-single-attraction .trvlr-sidebar h2 {
    color: var(--trvlr-color-secondary);
  }
CSS;

  trvlr_website_conditional_enqueue_css($theme_3_css);
}
