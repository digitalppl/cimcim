<?php /*Template Name: main_page*/
?>

<?php get_header(); ?>

	<?php do_action( 'accelerate_before_body_content' ); ?>

	<div id="primary">
		<div id="content" class="clearfix">		
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("main_banner1") ) : ?>		
<?php endif; ?>
<br /><br />		
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("main_banner2") ) : ?>		
<?php endif; ?>
<br /><br />			
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("main_banner3") ) : ?>		
<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
	
	<?php accelerate_sidebar_select(); ?>

	<?php do_action( 'accelerate_after_body_content' ); ?>

<?php get_footer(); ?>