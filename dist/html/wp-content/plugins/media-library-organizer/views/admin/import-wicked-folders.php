<!-- Import from Wicked Folders -->
<div id="import_wicked_folders" class="panel">
    <div class="postbox">
        <header>
            <h3><?php _e( 'Import from Wicked Folders', 'media-library-organizer' ); ?></h3>
            
        </header>

        <div class="wpzinc-option">	
			<p class="description">
				<?php 
				_e( 'Wicked Folder\'s folders (categories) will be imported into Media Library Organizer.', 'media-library-organizer' );
				?>
				<br />
				<?php 
				_e( 'Attachments assigned to Wicked Folder\'s folders will be reassigned to the equivalent Categories imported into Media Library Organizer.', 'media-library-organizer' );
				?>
			</p>
		</div>

		<div class="wpzinc-option">
            <input name="import_wicked_folders" type="submit" class="button button-primary" value="<?php _e( 'Import', 'media-library-organizer' ); ?>" />              
        </div>
	</div>
</div>