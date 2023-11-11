<?php
/*
Template Name: Full Width Template
*/
?>

<?php get_header(); ?>

<style>
    body.page-template-full-width-template .content-area {
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin: 0;
    }
</style>

<div id="primary" class="content-area full-width-template-scope">
    <main id="main" class="site-main" role="main">
        <?php
        while (have_posts()) : the_post();
            the_content();
        endwhile;
        ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>