<!-- Import from Enhanced Media Library-->
<div id="import_enhanced_media_library" class="panel">
    <div class="postbox">
        <header>
            <h3><?php _e( 'Import from Enhanced Media Library', 'media-library-organizer' ); ?></h3>
            
        </header>

        <div class="wpzinc-option">
			<div class="left">
				<strong><?php _e( 'Taxonomies', 'media-library-organizer' ); ?></strong>
			</div>
			<div class="right">
				<?php
				foreach ( $import_source['data']['taxonomies'] as $taxonomy_name => $taxonomy ) {
					// Skip non-EML categories
					if ( ! $taxonomy['eml_media'] ) {
						continue;
					}
					?>
					<label for="taxonomies_<?php echo $taxonomy_name; ?>">
						<input type="checkbox" name="taxonomies[]" id="taxonomies_<?php echo $taxonomy_name; ?>" value="<?php echo $taxonomy_name; ?>" />
						<?php echo $taxonomy['labels']['name']; ?>
					</label><br />
					<?php
				}
				?>
				
				<p class="description">
					<?php _e( 'Select the Taxonomies to import.  The Terms from the chosen Enhanced Media Library Taxonomies above will be imported into Media Library Organizer\'s Media Categories Taxonomy.', 'media-library-organizer' ); ?>
				</p>
			</div>
		</div>

		<div class="wpzinc-option">
            <input name="import_enhanced_media_library" type="submit" class="button button-primary" value="<?php _e( 'Import', 'media-library-organizer' ); ?>" />              
        </div>
	</div>
</div>