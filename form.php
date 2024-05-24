<?php
// form.php

// Check if this file is called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Form content
?>
<div class="wrap">
    <h1>EcoPower Tracker</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields( 'ecopower_tracker_options_group' );
        do_settings_sections( 'ecopower-tracker' );
        submit_button();
        ?>
    </form>
</div>
