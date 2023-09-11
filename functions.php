<?php
wp_enqueue_script('jquery');
function enqueue_custom_script() {
    // Enqueue your custom script.js file
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/script.js', array('jquery'), '1.0', true);

    // Pass the AJAX URL to the script.js file
    wp_localize_script('custom-script', 'custom_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');


function calculate_delivery_time() {
    // Get the suspension date (if set by admin)
    $suspension_date = get_option('shipping_suspension_date');

    if ($suspension_date && strtotime('today') <= strtotime($suspension_date)) {
        return date('Y-m-d', strtotime($suspension_date . '+2 days'));
    }

    $current_time = strtotime('now');
    $cutoff_time = strtotime('today 14:00:00');

    if ($current_time <= $cutoff_time) {
        return date('d-m-Y', strtotime('tomorrow'));
    } else {
        return date('d-m-Y', strtotime('tomorrow +1 day'));
    }
}

function display_delivery_time_on_product_archive() {
    $delivery_date = calculate_delivery_time();
    echo '<p>Dostawa dnia ' . esc_html($delivery_date) . '</p>';
}
add_action('woocommerce_after_shop_loop_item_title', 'display_delivery_time_on_product_archive');

function display_delivery_time_on_product_page() {
    $delivery_date = calculate_delivery_time();
    if (strtotime('now') <= strtotime('today 14:00:00')) {
        $time_remaining = strtotime('today 14:00:00') - strtotime('now');
        echo '<div class="estimated-delivery">';
        echo '<p>Dostawa dnia ' . esc_html($delivery_date) . '</p>';
        echo '</div>';
    } else {
        echo '<div class="estimated-delivery">';
        echo '<p>Dostawa dnia ' . esc_html($delivery_date) . '</p>';
        echo '</div>';
    }
}

add_action('woocommerce_single_product_summary', 'display_delivery_time_on_product_page', 11);

add_action('woocommerce_before_add_to_cart_form', 'display_delivery_time_on_product_page');



function save_shipping_suspension_date() {
    if (isset($_POST['update_shipping_suspension_date'])) {
        $new_date = sanitize_text_field($_POST['shipping_suspension_date']);
        update_option('shipping_suspension_date', $new_date);
    }
}

add_action('admin_init', 'save_shipping_suspension_date');


function get_delivery_time() {
    // Call the calculate_delivery_time PHP function
    $delivery_time = calculate_delivery_time();

    // Return the delivery time as JSON
    echo json_encode($delivery_time);

    // Always exit to avoid extra output
    wp_die();
}

// Create a custom menu item in the WordPress dashboard
function custom_settings_page() {
    add_menu_page(
        'Shipping Suspension Date', // Page title
        'Shipping Suspension', // Menu title
        'manage_options', // Capability required to access
        'shipping-suspension-settings', // Menu slug
        'admin_shipping_suspension_date_settings_page' // Callback function to display the settings page
    );
}
add_action('admin_menu', 'custom_settings_page');

// Callback function to display the custom settings page
function admin_shipping_suspension_date_settings_page() {
    ?>
    <div class="wrap">
        <h2>Data zawieszenia wysyłki</h2>
        <form method="post" action="options.php">
            <?php settings_fields('shipping_suspension_settings'); ?>
            <?php do_settings_sections('shipping-suspension-settings'); ?>
            <input type="submit" class="button-primary" value="Zapisz">
        </form>
    </div>
    <?php
}

// Register settings and fields
function custom_settings_fields() {
    register_setting('shipping_suspension_settings', 'shipping_suspension_date');
    add_settings_section('shipping_suspension_section', 'Data zawieszenia wysyłki', null, 'shipping-suspension-settings');
    add_settings_field('shipping_suspension_date', 'Wprowadź datę zawieszenia wysyłki', 'shipping_suspension_date_callback', 'shipping-suspension-settings', 'shipping_suspension_section');
}
add_action('admin_init', 'custom_settings_fields');

// Callback function for the input field
function shipping_suspension_date_callback() {
    $date = get_option('shipping_suspension_date');
    echo '<input type="date" name="shipping_suspension_date" value="' . esc_attr($date) . '">';
}
