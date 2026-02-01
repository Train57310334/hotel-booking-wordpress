<?php
/**
 * Plugin Name: KB Hotel Booking
 * Plugin URI: https://bookingkub.com
 * Description: A customizable booking search widget that redirects to your main booking engine.
 * Version: 1.0.1
 * Author: BookingKub
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Plugin Constants
define('HBW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HBW_PLUGIN_URL', plugin_dir_url(__FILE__));

// 1. Admin Settings Page (To set the Target URL)
add_action('admin_menu', 'hbw_add_admin_menu');
add_action('admin_init', 'hbw_settings_init');

function hbw_add_admin_menu()
{
    add_options_page('Hotel Booking Widget', 'Booking Widget', 'manage_options', 'hotel_booking_widget', 'hbw_options_page');
}

function hbw_settings_init()
{
    register_setting('hbwPlugin', 'hbw_settings');
    add_settings_section('hbw_plugin_page_section', 'General Settings', 'hbw_settings_section_callback', 'hotel_booking_widget');
    add_settings_field('hbw_target_url', 'Booking Engine URL', 'hbw_target_url_render', 'hotel_booking_widget', 'hbw_plugin_page_section');
}

function hbw_settings_section_callback()
{
    echo 'Configure where the search form should redirect users.';
}

function hbw_target_url_render()
{
    $options = get_option('hbw_settings');
    ?>
    <input type='text' name='hbw_settings[hbw_target_url]'
        value='<?php echo isset($options['hbw_target_url']) ? esc_attr($options['hbw_target_url']) : ''; ?>'
        style="width: 400px;" placeholder="https://app.bookingkub.com">
    <p class="description">Enter the full URL of your booking app (without /search).</p>
    <?php
}

function hbw_options_page()
{
    ?>
    <div class="wrap">
        <h2>Hotel Booking Widget Settings</h2>
        <form action='options.php' method='post'>
            <?php
            settings_fields('hbwPlugin');
            do_settings_sections('hotel_booking_widget');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// 2. Register Shortcode [hotel_booking_search]
// 2. Register Shortcode [hotel_booking_search]
// 2. Register Shortcode [hotel_booking_search]
function hbw_render_search_widget($atts)
{
    // Get target URL from settings
    $options = get_option('hbw_settings');
    $target_url = isset($options['hbw_target_url']) ? trim($options['hbw_target_url'], '/') : '';

    // Auto-fallback
    if (empty($target_url)) {
        $target_url = 'https://app.bookingkub.com';
    }

    // Enqueue Styles
    wp_enqueue_style('hbw-style', HBW_PLUGIN_URL . 'assets/style.css', array(), '1.0.3');
    wp_enqueue_script('hbw-script', HBW_PLUGIN_URL . 'assets/script.js', array(), '1.0.3', true);

    // Load Flatpickr from CDN for a Premium Calendar
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), null, true);

    ob_start();
    ?>
    <style>
    /* V11: Flatpickr & Perfect Padding */
    .hbw-wrapper { font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; width: 100%; margin: 0 auto; max-width: 1000px; }
    .hbw-form { 
        background: #ffffff; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.01); 
        border-radius: 1rem; 
        padding: 1.5rem; 
        display: flex; 
        flex-direction: column; 
        gap: 1rem; 
    }
    @media (min-width: 768px) { .hbw-form { flex-direction: row; align-items: center; padding: 0.75rem; gap: 0.75rem; } }
    
    .hbw-field-group { position: relative; flex: 1; min-width: 0; }
    
    /* Expanded Date Group */
    .hbw-flex-group { display: flex; gap: 0.5rem; flex: 3; } 
    @media (max-width: 767px) { .hbw-flex-group { flex-direction: row; } }
    
    .hbw-icon { 
        position: absolute; 
        left: 1.5rem; /* More space from left */
        top: 50%; 
        transform: translateY(-50%); 
        color: #64748b; 
        pointer-events: none; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        z-index: 10; 
        width: 20px;
    }
    
    .hbw-input { 
        width: 100%; 
        padding: 1.125rem 1rem 1.125rem 3.5rem !important; /* Force padding to prevent overlap */
        background-color: #f8fafc; 
        border: 1px solid #e2e8f0; 
        border-radius: 0.75rem; 
        font-size: 1rem; 
        color: #334155; 
        transition: all 0.2s; 
        outline: none; 
        box-sizing: border-box; 
        line-height: 1.5; 
        font-weight: 500;
        -webkit-appearance: none; /* Remove default browser styling */
    }
    .hbw-input:hover { background-color: #ffffff; border-color: #cbd5e1; }
    .hbw-input:focus { 
        background-color: #ffffff; 
        border-color: #0ea5e9; 
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15); 
    }
    
    /* Guests Group */
    .hbw-guests-group { flex: 1; min-width: 140px; }
    
    .hbw-btn { 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        gap: 0.5rem; 
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); 
        color: white; 
        font-weight: 600; 
        padding: 1.125rem 2rem; 
        border-radius: 0.75rem; 
        border: none; 
        cursor: pointer; 
        transition: all 0.2s; 
        white-space: nowrap; 
        font-size: 1rem; 
        box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.2); 
    }
    .hbw-btn:hover { 
        transform: translateY(-1px); 
        box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3); 
        filter: brightness(110%);
    }

    /* Flatpickr Customization */
    .flatpickr-calendar {
        border-radius: 1rem !important;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1) !important;
        border: none !important;
        font-family: 'Inter', sans-serif !important;
    }
    .flatpickr-day.selected {
        background: #0ea5e9 !important;
        border-color: #0ea5e9 !important;
    }
    </style>

    <div class="hbw-wrapper">
        <form method="GET" action="<?php echo esc_url($target_url . '/search'); ?>" class="hbw-form">
            <!-- V10: Destination Removed (Single Hotel Mode) -->

            <!-- Dates -->
            <div class="hbw-flex-group">
                <div class="hbw-field-group hbw-date-group">
                    <div class="hbw-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <!-- Type="text" for Flatpickr -->
                    <input type="text" name="checkIn" id="hbw-checkin" class="hbw-input hbw-date" placeholder="Check-in Date" required>
                </div>
                <div class="hbw-field-group hbw-date-group">
                    <div class="hbw-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <input type="text" name="checkOut" id="hbw-checkout" class="hbw-input hbw-date" placeholder="Check-out Date" required>
                </div>
            </div>

            <!-- Guests -->
            <div class="hbw-field-group hbw-guests-group">
                <div class="hbw-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <!-- Changed to text or simplified number to avoid browser default styling issues -->
                <input type="number" name="guests" class="hbw-input" min="1" value="1" placeholder="Guests">
            </div>

            <!-- Search Button -->
            <button type="submit" class="hbw-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span>Check Availability</span>
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Flatpickr
            if (typeof flatpickr !== 'undefined') {
                const today = new Date();
                const tomorrow = new Date();
                tomorrow.setDate(today.getDate() + 1);

                const checkInPicker = flatpickr("#hbw-checkin", {
                    minDate: "today",
                    defaultDate: today,
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "F j, Y", // Premium readable format (e.g., January 29, 2026)
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates[0]) {
                            const minOutDate = new Date(selectedDates[0]);
                            minOutDate.setDate(minOutDate.getDate() + 1);
                            checkOutPicker.set('minDate', minOutDate);
                            checkOutPicker.setDate(minOutDate);
                        }
                    }
                });

                const checkOutPicker = flatpickr("#hbw-checkout", {
                    minDate: tomorrow,
                    defaultDate: tomorrow,
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "F j, Y"
                });
            } else {
                console.warn('Flatpickr not loaded yet');
            }
        });
    </script>
    <?php

    return ob_get_clean();
}
add_shortcode('hotel_booking_search', 'hbw_render_search_widget');
