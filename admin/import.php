<div class="wrap">
    <h1>Import Projects</h1>
    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="ecopower_tracker_import_csv">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="csv_file">CSV File</label></th>
                <td><input type="file" name="csv_file" id="csv_file" required></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="ecopower_tracker_import_submit" id="submit" class="button button-primary" value="Import CSV">
        </p>
    </form>
</div>
