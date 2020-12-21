<div class="postbox wpzinc-vertical-tabbed-ui">
    <!-- Second level tabs -->
    <ul class="wpzinc-nav-tabs wpzinc-js-tabs" data-panels-container="#settings-container" data-panel=".panel" data-active="wpzinc-nav-tab-vertical-active">
        <?php
        // Iterate through this screen's tabs
        foreach ( (array) $tabs as $index => $tab_item ) {
            ?>
            <li class="wpzinc-nav-tab <?php echo ( isset( $tab_item['menu_icon'] ) ? $tab_item['menu_icon'] : 'default' ); ?>">
                <a href="#<?php echo $tab_item['name']; ?>"<?php echo ( $tab_item['name'] == $tab['name'] ? ' class="wpzinc-nav-tab-vertical-active"' : '' ) . ( isset( $tab_item['documentation'] ) ? ' data-documentation="' . $tab_item['documentation'] . '"' : '' ); ?>>
                    <?php
                    echo $tab_item['label'];
                    ?>
                </a>
            </li>
            <?php
        }

        // Iterate through this screen's addon tabs
        foreach ( (array) $addon_tabs as $addon_name => $tab_item ) {
            $css_classes = array();
            if ( $tab_item['name'] == $tab['name'] ) {
                $css_classes[] = 'wpzinc-nav-tab-vertical-active';
            }
            ?>
            <li class="wpzinc-nav-tab <?php echo ( isset( $tab_item['menu_icon'] ) ? $tab_item['menu_icon'] : 'default' ); ?>">
                <a href="#<?php echo $tab_item['name']; ?>" class="<?php echo implode( ' ', $css_classes ); ?>" <?php echo ( isset( $tab_item['documentation'] ) ? ' data-documentation="' . $tab_item['documentation'] . '"' : '' ); ?>>
                    <?php
                    echo $tab_item['label'];

                    if ( isset( $tab_item['is_pro'] ) && $tab_item['is_pro'] ) {
                        ?>
                        <span class="tag"><?php _e( 'Pro', 'media-library-organizer' ); ?></span>
                        <?php
                    }
                    ?>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>

    <!-- Content -->
    <div id="settings-container" class="wpzinc-nav-tabs-content no-padding">
        <!-- General -->
        <div id="general" class="panel">
            <div class="postbox">
                <header>
                    <h3><?php _e( 'Filter Settings', 'media-library-organizer' ); ?></h3>
                    <p class="description">
                        <?php _e( 'Determines which filters should be displayed on list and grid Media Library views.', 'media-library-organizer' ); ?>
                    </p>
                </header>

                <div class="wpzinc-option">
                    <div class="left">
                        <label for="general_taxonomy_enabled"><?php _e( 'Media Categories', 'media-library-organizer' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="general[taxonomy_enabled]" id="general_taxonomy_enabled" size="1">
                            <option value="1"<?php selected( $this->get_setting( 'general', 'taxonomy_enabled' ), 1 ); ?>><?php _e( 'Enabled', 'media-library-organizer' ); ?></option>
                            <option value="0"<?php selected( $this->get_setting( 'general', 'taxonomy_enabled' ), 0 ); ?>><?php _e( 'Disabled', 'media-library-organizer' ); ?></option>
                        </select>

                        <p class="description">
                            <?php _e( 'If enabled, displays a dropdown option to filter Media Library items by Category', 'media-library-organizer' ); ?>
                        </p>
                    </div>
                </div>

                <div class="wpzinc-option">
                    <div class="left">
                        <label for="general_orderby_enabled"><?php _e( 'Sorting', 'media-library-organizer' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="general[orderby_enabled]" id="general_orderby_enabled" size="1">
                            <option value="1"<?php selected( $this->get_setting( 'general', 'orderby_enabled' ), 1 ); ?>><?php _e( 'Enabled', 'media-library-organizer' ); ?></option>
                            <option value="0"<?php selected( $this->get_setting( 'general', 'orderby_enabled' ), 0 ); ?>><?php _e( 'Disabled', 'media-library-organizer' ); ?></option>
                        </select>

                        <p class="description">
                            <?php _e( 'If enabled, displays a dropdown option to select how to order Media Library items', 'media-library-organizer' ); ?>
                        </p>
                    </div>
                </div>

                <div class="wpzinc-option">
                    <div class="left">
                        <label for="general_order_enabled"><?php _e( 'Sort Order', 'media-library-organizer' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="general[order_enabled]" id="general_order_enabled" size="1">
                            <option value="1"<?php selected( $this->get_setting( 'general', 'order_enabled' ), 1 ); ?>><?php _e( 'Enabled', 'media-library-organizer' ); ?></option>
                            <option value="0"<?php selected( $this->get_setting( 'general', 'order_enabled' ), 0 ); ?>><?php _e( 'Disabled', 'media-library-organizer' ); ?></option>
                        </select>

                        <p class="description">
                            <?php _e( 'If enabled, displays a dropdown option to select whether to order Media Library items ascending or descending', 'media-library-organizer' ); ?>
                        </p>
                    </div>
                </div>

                <?php do_action( 'media_library_organizer_admin_output_settings_panel_general' ); ?>
            </div>
        </div>

        <!-- User Options -->
        <div id="user-options" class="panel">
            <div class="postbox">
                <header>
                    <h3><?php _e( 'User Settings', 'media-library-organizer' ); ?></h3>
                    <p class="description">
                        <?php _e( 'Determines which filter settings should persist across different screens for the logged in WordPress User.', 'media-library-organizer' ); ?>
                    </p>
                </header>

                <div class="wpzinc-option">
                    <div class="left">
                        <label for="user_options_orderby_enabled"><?php _e( 'Sorting', 'media-library-organizer' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="user-options[orderby_enabled]" id="user_options_orderby_enabled" size="1">
                            <option value="1"<?php selected( $this->get_setting( 'user-options', 'orderby_enabled' ), 1 ); ?>><?php _e( 'Remember', 'media-library-organizer' ); ?></option>
                            <option value="0"<?php selected( $this->get_setting( 'user-options', 'orderby_enabled' ), 0 ); ?>><?php _e( 'Don\'t Remember', 'media-library-organizer' ); ?></option>
                        </select>

                        <p class="description">
                            <?php _e( 'When set to Remembered, the User\'s last chosen Order By filter option will be remembered across all Media Views.  When set to Don\'t Remember, the filters will reset when switching between WordPress Administration screens.', 'media-library-organizer' ); ?>
                        </p>
                    </div>
                </div>

                <div class="wpzinc-option">
                    <div class="left">
                        <label for="user_options_order_enabled"><?php _e( 'Sort Order', 'media-library-organizer' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="user-options[order_enabled]" id="user_options_order_enabled" size="1">
                            <option value="1"<?php selected( $this->get_setting( 'user-options', 'order_enabled' ), 1 ); ?>><?php _e( 'Remember', 'media-library-organizer' ); ?></option>
                            <option value="0"<?php selected( $this->get_setting( 'user-options', 'order_enabled' ), 0 ); ?>><?php _e( 'Don\'t Remember', 'media-library-organizer' ); ?></option>
                        </select>

                        <p class="description">
                            <?php _e( 'When set to Remembered, the User\'s last chosen Order filter option will be remembered across all Media Views.  When set to Don\'t Remember, the filters will reset when switching between WordPress Administration screens.', 'media-library-organizer' ); ?>
                        </p>
                    </div>
                </div>

                <?php do_action( 'media_library_organizer_admin_output_settings_panel_user_options' ); ?>
            </div>
        </div>

        <?php
        do_action( 'media_library_organizer_admin_output_settings_panels' );
        ?>
    </div>
</div>

<!-- Save -->
<div>
    <input type="submit" name="submit" value="<?php _e( 'Save', $this->base->plugin->name ); ?>" class="button button-primary" />
</div>