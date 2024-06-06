# EcoPower Tracker

A WordPress plugin to manage and display renewable energy project data.

## Plugin Information

- **Plugin Name**: EcoPower Tracker
- **Version**: 2.1d
- **Author**: Saqib Jawaid
- **License**: GPL 3 or later

## Features

1. Project Fields:
   - Project# (self-generated primary key)
   - Project Company
   - Project Name
   - Project Location
   - Type of Plant (Wind or Solar)
   - Project CUF
   - Generation Capacity (in KWs)
   - Date of Activation

2. Functionalities:
   - **Data Display Options**:
     - Show combined results from the date of activation to the present.
     - Display power generated and carbon offset for individual projects.
     - Option to choose individual projects or subgroups for a detailed view.
   - **Shortcode Generation**:
     - Shortcodes for displaying various data views: total power generated, total carbon offset, individual project power generated, individual project carbon offset, subgroup project power generated, and subgroup project carbon offset.
     - Include shortcode information in the add/view form, not in the plugin description.
     - Display the list of shortcodes on the main EcoPower Tracker page in the admin area.
     - The output of the shortcode data that is displayed on the webpage should be formattable (text styles can be changed).
   - **Data Management**:
     - View all projects in the admin panel.
     - Export all project data as a CSV file.
     - Import data by uploading a CSV file.
     - Edit and remove existing data.
   - **CSV File Formatting**:
     - Fields and order: Project Company, Project Name, Project Location, Type of Plant (Wind or Solar), Project CUF, Generation Capacity (in KWs), Date of Activation.
     - Ensure proper formatting for seamless import/export.
   - **Output Format**:
     - Use 1,000 delimiters (",") for numerical outputs to enhance readability.
     - Convert power generated values to MWh if equal to or greater than 1000 KWh, and vice versa.
     - Display CO2 offset values accurately.

3. Form to Enter Data:
   - Ensure there is a form to enter project data directly in the admin interface.

4. Edit and Delete Individual Records:
   - Implement the ability to edit and delete individual project records from the admin interface.

5. Date Format for CSV:
   - Ensure dates are converted to the format required for the CSV during the import process.

6. View Imported Data:
   - Provide functionality to view data that has been imported via CSV.

7. Localization and Translation:
   - Add support for multiple languages to make the plugin accessible to a wider audience.
   - Use WordPress's localization functions to enable easy translation of the plugin's text.

8. Customizable Widgets:
   - Create customizable widgets that users can place on their site’s front end to display real-time power generation and CO2 offset data.
   - Allow users to configure the appearance and behavior of these widgets.

9. Customizable Reporting Intervals:
   - Allow users to customize reporting intervals for power generation and CO2 offset (e.g., daily, weekly, monthly).
   - Provide a summary view for each reporting interval.

## Changelog

- **Version 2.1d**: A total rewrite with project management, data display, shortcode generation, CSV import/export, customizable widgets, reporting intervals, and localization.

## FAQ

**Q: What type of CSV format is supported?**
A: The CSV file should be comma-separated with the following fields in order: Project Company, Project Name, Project Location, Type of Plant (Wind or Solar), Project CUF, Generation Capacity (in KWs), Date of Activation.

**Q: What is the sample format of the CSV?**

Example in CSV format:
Project Company, Project Name, Project Location, Type of Plant, Project CUF, Generation Capacity (KWs), Date of Activation
Company A, Project Alpha, Location X, Wind, 30.5, 1500, 2021-01-01
Company B, Project Beta, Location Y, Solar, 25.0, 1200, 2020-06-15


Example in table format:

| Project Company | Project Name | Project Location | Type of Plant | Project CUF | Generation Capacity (KWs) | Date of Activation |
|-----------------|--------------|------------------|---------------|-------------|---------------------------|--------------------|
| Company A       | Project Alpha| Location X       | Wind          | 30.5        | 1500                      | 2021-01-01         |
| Company B       | Project Beta | Location Y       | Solar         | 25.0        | 1200                      | 2020-06-15         |

## Support

For support, please visit the [GitHub repository](https://github.com/saqibj/EcoTracker/) and open an issue on the [GitHub issues page](https://github.com/saqibj/EcoTracker/issues).

![EcoPower Tracker Logo](img/EcoTracker-Logo.webp)