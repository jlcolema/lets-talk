<!-- Import from HappyFiles -->
<div id="import_happyfiles" class="panel">
    <div class="postbox">
        <header>
            <h3><?php _e( 'Import from HappyFiles', 'media-library-organizer' ); ?></h3>
            
        </header>

        <div class="wpzinc-option">	
			<p class="description">
				<?php 
				_e( 'HappyFiles\'s folders (categories) will be imported into Media Library Organizer.', 'media-library-organizer' );
				?>
				<br />
				<?php 
				_e( 'Attachments assigned to HappyFiles folders will be reassigned to the equivalent Categories imported into Media Library Organizer.', 'media-library-organizer' );
				?>
			</p>
		</div>

		<div class="wpzinc-option">
            <input name="import_happyfiles" type="submit" class="button button-primary" value="<?php _e( 'Import', 'media-library-organizer' ); ?>" />              
        </div>
	</div>
</div>