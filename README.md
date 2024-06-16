# EcoPower Tracker

**Version**: 2.0.1  
**Author**: Saqib Jawaid  
**License**: GPL v3

## Overview

EcoPower Tracker is a WordPress plugin designed to manage and display data from renewable energy projects, focusing on solar and wind energy. It allows users to track power generation and CO2 offset for individual projects or groups of projects.

## Features

- **Data Management**: Manage renewable energy project data including project number, company, location, type, CUF, capacity, and activation date.
- **Data Display**: Display total and individual project data for power generation and CO2 offset using shortcodes.
- **CSV Import/Export**: Import project data via CSV upload and export data to CSV.
- **Admin Dashboard**: View project summaries and detailed lists in a user-friendly admin dashboard.
- **Customizable Widgets**: Display selected data points on the website's front end.
- **Localization Support**: The plugin is ready for translation into different languages.
- **Shortcodes**: Use a variety of shortcodes to display data on your site.

## Installation

1. **Upload the Plugin**:
   - Download the EcoPower Tracker plugin.
   - Upload the plugin files to the `/wp-content/plugins/ecopower-tracker` directory, or install the plugin through the WordPress plugins screen directly.

2. **Activate the Plugin**:
   - Activate the plugin through the 'Plugins' screen in WordPress.

3. **Set Up the Plugin**:
   - Navigate to the EcoPower Tracker menu in the WordPress admin dashboard.
   - Upload your project data via CSV or add projects manually.

## Usage

### Admin Dashboard

- **Dashboard View**: The dashboard displays the total power generated and CO2 offset, along with a list of all projects. Each project can be edited or deleted.
- **CSV Upload**: Use the form on the dashboard to upload a CSV file containing project data.
- **CSV Export**: Export all project data to a CSV file using the provided export button.

### Shortcodes

EcoPower Tracker provides a variety of shortcodes to display project data on your site. Below is a list of available shortcodes and their descriptions:

- **Total Data**
  - `[ecopower_tracker_total_power]`: Displays the total power generated by all projects combined.
  - `[ecopower_tracker_total_co2]`: Displays the total CO2 offset by all projects combined.

- **Individual Project Data**
  - `[ecopower_tracker_project_power project_id="X"]`: Displays the power generated by a specific project (replace `X` with the project ID).
  - `[ecopower_tracker_project_co2 project_id="X"]`: Displays the CO2 offset by a specific project.
  - `[ecopower_tracker_project_capacity project_id="X"]`: Displays the generation capacity of a specific project.

- **Subgroup Data (Company, Location, Type)**
  - **By Company**:
    - `[ecopower_tracker_company_power company="Company Name"]`: Displays the total power generated by all projects from a specific company.
    - `[ecopower_tracker_company_co2 company="Company Name"]`: Displays the total CO2 offset by all projects from a specific company.
    - `[ecopower_tracker_company_capacity company="Company Name"]`: Displays the total generation capacity of all projects from a specific company.
  - **By Location**:
    - `[ecopower_tracker_location_power location="Location"]`: Displays the total power generated by all projects in a specific location.
    - `[ecopower_tracker_location_co2 location="Location"]`: Displays the total CO2 offset by all projects in a specific location.
    - `[ecopower_tracker_location_capacity location="Location"]`: Displays the total generation capacity of all projects in a specific location.
  - **By Type**:
    - `[ecopower_tracker_type_power type="Type"]`: Displays the total power generated by all projects of a specific type (Wind or Solar).
    - `[ecopower_tracker_type_co2 type="Type"]`: Displays the total CO2 offset by all projects of a specific type.
    - `[ecopower_tracker_type_capacity type="Type"]`: Displays the total generation capacity of all projects of a specific type.

### Examples

To use a shortcode, simply place it in any page or post where you want the data to appear. For example:

- To display the total power generated by all projects:  
  `[ecopower_tracker_total_power]`

- To display the power generated by a project with ID 5:  
  `[ecopower_tracker_project_power project_id="5"]`

- To show the total CO2 offset by projects from "Green Energy Corp":  
  `[ecopower_tracker_company_co2 company="Green Energy Corp"]`

## Support

For support and to report issues, visit our [GitHub page](https://github.com/saqibj/EcoTracker).

## Changelog

### Version 2.0.1
- Initial release with core functionalities:
  - Project management and data display.
  - CSV import and export capabilities.
  - Dashboard view with project list and total summaries.
  - Comprehensive shortcode support.

---

## License

This plugin is licensed under the GPL v3 License. See the [LICENSE](LICENSE) file for more details.
