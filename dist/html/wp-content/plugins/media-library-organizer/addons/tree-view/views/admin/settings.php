<div id="tree-view" class="panel">
    <div class="postbox">
        <header>
            <h3><?php _e( 'Tree View Settings', 'media-library-organizer-pro' ); ?></h3>

            <p class="description">
                <?php _e( 'Display a Category Tree sidebar when viewing the Media Library.', 'media-library-organizer-pro' ); ?>
            </p>
        </header>

        <div class="wpzinc-option">
            <div class="left">
                <strong><?php _e( 'Enabled', 'media-library-organizer-pro' ); ?></strong>
            </div>
            <div class="right">
                <select name="tree-view[enabled]" size="1" data-conditional="tree-view-enabled">
                    <option value="1"<?php selected( $this->get_setting( 'tree-view', 'enabled' ), 1 ); ?>>
                        <?php _e( 'Enabled', 'media-library-organizer-pro' ); ?>
                    </option>
                    <option value="0"<?php selected( $this->get_setting( 'tree-view', 'enabled' ), 0 ); ?>>
                        <?php _e( 'Disabled', 'media-library-organizer-pro' ); ?>
                    </option>
                </select>
            </div>
        </div>

        <div id="tree-view-enabled">
            <div class="wpzinc-option">
                <div class="left">
                    <strong><?php _e( 'Expand/Collapse', 'media-library-organizer-pro' ); ?></strong>
                </div>
                <div class="right">
                    <select name="tree-view[jstree_enabled]" size="1">
                        <option value="1"<?php selected( $this->get_setting( 'tree-view', 'jstree_enabled' ), 1 ); ?>>
                            <?php _e( 'Enabled', 'media-library-organizer-pro' ); ?>
                        </option>
                        <option value="0"<?php selected( $this->get_setting( 'tree-view', 'jstree_enabled' ), 0 ); ?>>
                            <?php _e( 'Disabled', 'media-library-organizer-pro' ); ?>
                        </option>
                    </select>

                    <p class="description">
                        <?php _e( 'If enabled, only top level Categories are displayed in the Tree View. Clicking the icon next to them will reveal Subcategories.', 'media-library-organizer' ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>