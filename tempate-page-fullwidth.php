<?php
/*
 * Template Name: RAML Page Template
 */

get_header(); ?>

<!--<div ng-app="ramlConsoleApp" class="raml-console-body ng-scope">-->

<div style="padding-left:100px;padding-right:100px;">  
<?php
while ( have_posts() ) : the_post();

    get_template_part( 'content', get_post_format() );

    get_template_part( 'template-parts/content', get_post_format() );

    get_template_part( 'template-parts/post/content', get_post_format() );

    // If comments are open or we have at least one comment, load up the comment template.
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;

endwhile; // End of the loop.
?>
</div>

<?php get_footer();
