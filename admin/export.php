<div class="wrap">
    <h1>Export Projects</h1>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="ecopower_tracker_export_csv">
        <p class="submit">
            <input type="submit" name="ecopower_tracker_export_submit" id="submit" class="button button-primary" value="Export CSV">
        </p>
    </form>
</div>