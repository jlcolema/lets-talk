<div id="media-library-organizer-tree-view">
	<form class="media-library-organizer-tree-view-inner">
		<h2 class="wp-heading-inline"><?php echo Media_Library_Organizer()->get_class( 'taxonomy' )->taxonomy_label_short_plural; ?></h2>
		
		<div class="wp-filter">
		<?php
		if ( current_user_can( 'manage_categories' ) ) {
			?>
			<div class="search-form">
				<button class="button media-library-organizer-tree-view-add"><?php _e( 'Add', 'media-library-organizer-pro' ); ?></button>
				<button class="button media-library-organizer-tree-view-edit" disabled><?php _e( 'Edit', 'media-library-organizer-pro' ); ?></button>
				<button class="button media-library-organizer-tree-view-delete" disabled><?php _e( 'Delete', 'media-library-organizer-pro' ); ?></button>
			</div>
			<?php
		}
		?>
		</div>

		<div id="media-library-organizer-tree-view-list"<?php echo ( $jstree_enabled ? ' class="media-library-organizer-tree-view-enabled"' : '' ); ?>>
			<?php echo $output; ?>
		</div>
	</form>
</div>