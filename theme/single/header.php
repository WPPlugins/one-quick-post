<?php do_action( 'oqp_before_post_header' ); ?>
<div id="item-header-avatar" style="width:<?php echo oqp_image_size_thumb_header_w;?>px;height:<?php echo oqp_image_size_thumb_header_h;?>px">
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php oqp_post_the_thumbnail('oqp_thumb_header'); ?></a>
</div><!-- #item-header-avatar -->
<div id="item-header-content">
	<h2><?php the_title(); ?></h2>
	<?php do_action( 'oqp_before_single_item_header' ); ?>
	
	<div id="item-meta">

		<div id="item-buttons">

			<?php do_action( 'oqp_item_header_actions' ); ?>

		</div><!-- #item-buttons -->
		<div class="entry-summary">
			<?php echo get_the_excerpt(); ?>
		</div>
		<?php
		 do_action( 'oqp_item_header_meta' );
		 ?>

		
	</div><!-- #item-meta -->

</div>
<?php do_action( 'bp_after_post_header' ); ?>

