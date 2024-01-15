<?php
/*
Template Name: Full Width Template
*/
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        body.page-template-full-width-template .content-area {
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body <?php body_class(); ?>>
    <div id="primary" class="content-area full-width-template-scope">
        <main id="main" class="site-main" role="main">
            <?php
            while (have_posts()) : the_post();
                the_content();
            endwhile;
            ?>
        </main><!-- #main -->
    </div><!-- #primary -->
    <?php wp_footer(); ?>
</body>

</html>