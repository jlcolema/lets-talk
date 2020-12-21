<?php
// Order By Filter
if ( $this->base->get_class( 'settings' )->get_setting( 'general', 'orderby_enabled' ) ) {
    $orderby = $this->base->get_class( 'common' )->get_orderby_options();
    if ( ! empty( $orderby ) ) {
        ?>
        <select name="orderby" id="mlo-orderby" size="1">
            <?php
            foreach ( $orderby as $key => $value ) {
                ?>
                <option value="<?php echo $key; ?>"<?php selected( $current_orderby, $key ); ?>><?php echo $value; ?></option>
                <?php
            }
            ?>
        </select>
        <?php
    }
}

// Order Filter
if ( $this->base->get_class( 'settings' )->get_setting( 'general', 'order_enabled' ) ) {
    $order = $this->base->get_class( 'common' )->get_order_options();
    if ( ! empty( $order ) ) {
        ?>
        <select name="order" id="mlo-order" size="1">
            <?php
            foreach ( $order as $key => $value ) {
                ?>
                <option value="<?php echo $key; ?>"<?php selected( $current_order, $key ); ?>><?php echo $value; ?></option>
                <?php
            }
            ?>
        </select>
        <?php
    }
}