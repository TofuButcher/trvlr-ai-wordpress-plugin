<?php

/**
 * Single template for trvlr attraction
 */
get_header();

while (have_posts()) : the_post();

	echo trvlr_single_attraction_markup();

endwhile;

get_footer();
