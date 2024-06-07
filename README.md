# EcoPower Tracker

**Version:** 2.1e  
**Author:** Saqib Jawaid  
**License:** GPL 3 or later

## Description

EcoPower Tracker is a WordPress plugin designed to help you manage and display data for renewable energy projects. It provides detailed insights into total power generation, CO2 offset, and generation capacity for various renewable energy projects such as solar and wind plants.

## Features

- **Manage Projects**: Add, edit, and delete renewable energy projects from the admin panel.
- **Data Display**: Use shortcodes to display project data, including total power generated, CO2 offset, and generation capacity.
- **Import/Export**: Easily import and export project data using CSV files.
- **Customizable Reporting**: View data aggregated by project, company, location, and type of plant.
- **Localization**: Supports multiple languages to reach a broader audience.
- **Shortcodes**: Multiple shortcodes to display various data views.

## Installation

1. Upload the `ecopower-tracker` folder to the `/wp-content/plugins/` directory or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the EcoPower Tracker menu in the admin dashboard to manage projects and view data.

## Usage

### Available Shortcodes

1. **Total Power Generated**
   - **Shortcode**: `[ecopower_tracker_total_power]`
   - **Description**: Displays the total power generated by all projects.
   - **Output**: E.g., `12345.67 MWh`

2. **Total CO2 Offset**
   - **Shortcode**: `[ecopower_tracker_total_co2]`
   - **Description**: Displays the total CO2 offset by all projects.
   - **Output**: E.g., `10493.82 kg`

3. **All Projects Table**
   - **Shortcode**: `[ecopower_tracker_projects]`
   - **Description**: Displays a table listing all projects with their details.

4. **Total Power Generated (Number Only)**
   - **Shortcode**: `[ecopower_tracker_total_power_number]`
   - **Description**: Displays the total power generated as a plain number.
   - **Output**: E.g., `12345.67`

5. **Total CO2 Offset (Number Only)**
   - **Shortcode**: `[ecopower_tracker_total_co2_number]`
   - **Description**: Displays the total CO2 offset as a plain number.
   - **Output**: E.g., `10493.82`

6. **Specific Project Details**
   - **Shortcode**: `[ecopower_tracker_project project_name="Project Name"]`
   - **Description**: Displays details for a specific project.
   - **Example**: `[ecopower_tracker_project project_name="Solar Plant 1"]`

7. **Grouped by Company**
   - **Shortcode**: `[ecopower_tracker_company company_name="Company Name"]`
   - **Description**: Displays aggregated data for all projects under a specific company.
   - **Example**: `[ecopower_tracker_company company_name="Solar Corp"]`

8. **Grouped by Location**
   - **Shortcode**: `[ecopower_tracker_location location="Location Name"]`
   - **Description**: Displays aggregated data for all projects at a specific location.
   - **Example**: `[ecopower_tracker_location location="Location 1"]`

9. **Grouped by Type of Plant**
   - **Shortcode**: `[ecopower_tracker_type plant_type="Plant Type"]`
   - **Description**: Displays aggregated data for all projects of a specific type.
   - **Example**: `[ecopower_tracker_type plant_type="Solar"]`

10. **Total Generation Capacity**
    - **Shortcode**: `[ecopower_tracker_total_capacity]`
    - **Description**: Displays the total generation capacity of all projects combined.
    - **Output**: E.g., `1500.00 KW`

11. **Generation Capacity for a Specific Project**
    - **Shortcode**: `[ecopower_tracker_project_capacity project_name="Project Name"]`
    - **Description**: Displays the generation capacity for a specified project.
    - **Example**: `[ecopower_tracker_project_capacity project_name="Solar Plant 1"]`

12. **Total Generation Capacity Grouped by Company**
    - **Shortcode**: `[ecopower_tracker_company_capacity company_name="Company Name"]`
    - **Description**: Displays the total generation capacity for all projects under a specified company.
    - **Example**: `[ecopower_tracker_company_capacity company_name="Solar Corp"]`

13. **Total Generation Capacity Grouped by Location**
    - **Shortcode**: `[ecopower_tracker_location_capacity location="Location Name"]`
    - **Description**: Displays the total generation capacity for all projects at a specified location.
    - **Example**: `[ecopower_tracker_location_capacity location="Location 1"]`

14. **Total Generation Capacity Grouped by Type of Plant**
    - **Shortcode**: `[ecopower_tracker_type_capacity plant_type="Plant Type"]`
    - **Description**: Displays the total generation capacity for all projects of a specified type.
    - **Example**: `[ecopower_tracker_type_capacity plant_type="Solar"]`

### CSV Import/Export

You can import and export project data using a CSV file. The required columns are as follows:

| Column Name             | Description                                      |
|-------------------------|--------------------------------------------------|
| Project Company         | Name of the company that owns the project.       |
| Project Name            | Name of the project.                             |
| Project Location        | Location of the project.                         |
| Type of Plant           | Type of the plant (e.g., Solar, Wind).           |
| Project CUF             | Capacity Utilization Factor of the project.      |
| Generation Capacity (KW)| Generation capacity of the project in KW.        |
| Date of Activation      | Date when the project was activated (YYYY-MM-DD).|

Ensure that your CSV file matches the above format for a successful import.

### Support and Links

- **GitHub Repository**: [EcoPower Tracker on GitHub](https://github.com/saqibj/EcoTracker)
- **Report Issues**: [GitHub Issues](https://github.com/saqibj/EcoTracker/issues)

For support, visit our GitHub page and open an issue if you encounter any problems.

## Changelog

### Version 2.1e
- Added new shortcodes for displaying total generation capacity:
  - `[ecopower_tracker_total_capacity]` for total capacity of all projects.
  - `[ecopower_tracker_project_capacity project_name="Project Name"]` for specific project capacity.
  - `[ecopower_tracker_company_capacity company_name="Company Name"]` for capacity by company.
  - `[ecopower_tracker_location_capacity location="Location Name"]` for capacity by location.
  - `[ecopower_tracker_type_capacity plant_type="Plant Type"]` for capacity by plant type.
- Improved dashboard:
  - Added display for total power generated and total CO2 offset.
  - Included project numbers in the dashboard and "All Projects" page.
- Enhanced CSV import functionality:
  - Implemented checks to prevent importing duplicate projects.
  - Added validation for date formats and corrected import issues.
  - Provided feedback on projects that were not imported.
- UI improvements:
  - Updated the menu icon size to fit better within the admin panel.
  - Made the dashboard the default page and removed edit/delete options from it.
- General code improvements and bug fixes to enhance stability and performance.

### Version 2.1d
- Separated CSV handling into import, post-upload processing, and export.
- Added functionality to store uploaded CSV files for debugging.

### Version 2.1c
- Fixed the issue with the oversized menu icon.
- Added a comprehensive about page with usage instructions.

### Version 2.1b
- Resolved issues with duplicate menu items.
- Ensured the "Dashboard" is the default page and provided proper redirection after editing a project.

### Version 2.1a
- Initial release with project management, data display, shortcode generation, CSV import/export, customizable widgets, reporting intervals, and localization support.

## License

This plugin is licensed under the GPL 3 or later. See the [GPL-3.0 License](https://www.gnu.org/licenses/gpl-3.0.html) for more details.