<?php
// search-bar.php - The HTML Template for the widget
// $target_url is available here from the main file
?>

<div class="hbw-wrapper">
    <form method="GET" action="<?php echo esc_url($target_url . '/search'); ?>" class="hbw-form">
        
        <!-- Destination -->
        <div class="hbw-field-group">
            <div class="hbw-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            </div>
            <input type="text" name="destination" class="hbw-input" placeholder="Where are you going?" value="Bangkok">
        </div>

        <!-- Dates -->
        <div class="hbw-flex-group">
            <div class="hbw-field-group hbw-date-group">
                <div class="hbw-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <input type="date" name="checkIn" class="hbw-input hbw-date" required placeholder="Check-in">
            </div>
            <div class="hbw-field-group hbw-date-group">
                <div class="hbw-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <input type="date" name="checkOut" class="hbw-input hbw-date" required placeholder="Check-out">
            </div>
        </div>

        <!-- Guests -->
        <div class="hbw-field-group hbw-guests-group">
            <div class="hbw-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <input type="number" name="guests" class="hbw-input" min="1" value="1" placeholder="Guests">
        </div>

        <!-- Search Button -->
        <button type="submit" class="hbw-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <span>Search</span>
        </button>
    </form>
</div>
