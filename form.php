<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( isset($_POST['submit']) ) {
    ecopower_tracker_add_project();
}

if ( isset($_POST['upload_csv']) ) {
    ecopower_tracker_handle_csv_upload();
}
?>

<div class="wrap">
    <h1>EcoPower Tracker</h1>
    <form method="post">
        <h2>Manual Data Entry</h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="project_company">Project Company</label></th>
                <td><input name="project_company" type="text" id="project_company" value="" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="project_name">Project Name</label></th>
                <td><input name="project_name" type="text" id="project_name" value="" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="project_location">Project Location</label></th>
                <td><input name="project_location" type="text" id="project_location" value="" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="type_of_plant">Type of Plant</label></th>
                <td>
                    <select name="type_of_plant" id="type_of_plant" required>
                        <option value="Wind">Wind</option>
                        <option value="Solar">Solar</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="project_cuf">Project CUF</label></th>
                <td><input name="project_cuf" type="number" step="0.01" id="project_cuf" value="" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="generation_capacity">Generation Capacity (KW)</label></th>
                <td><input name="generation_capacity" type="number" id="generation_capacity" value="" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="date_of_activation">Date of Activation</label></th>
                <td><input name="date_of_activation" type="date" id="date_of_activation" value="" class="regular-text" required></td>
            </tr>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save"></p>
    </form>

    <h2>CSV File Upload</h2>
    <form method="post" enctype="multipart/form-data">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">CSV File Upload</th>
                <td><input type="file" name="csv_file" accept=".csv" required /></td>
            </tr>
        </table>
        <p>
            <input type="submit" name="upload_csv" class="button-primary" value="Upload CSV">
        </p>
    </form>
    <h2>Sample CSV Format</h2>
    <pre>
Project Company,Project Name,Project Location,Type of Plant,Project CUF,Generation Capacity (KW),Date of Activation
Company A,Project X,Location X,Wind,0.25,500,2023-01-01
Company B,Project Y,Location Y,Solar,0.20,300,2023-03-15
    </pre>
</div>
