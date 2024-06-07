# EcoPower Tracker

**Version:** 2.1e  
**Author:** Saqib Jawaid  
**License:** GPL 3 or later

EcoPower Tracker is a comprehensive plugin designed to manage and display data for renewable energy projects. It tracks total power generation and CO2 offset for solar and wind energy projects.

## Features

- **Project Management:** Add, edit, and delete renewable energy projects.
- **Data Display:** View total power generated and CO2 offset since the project's start.
- **Shortcodes:** Display project information on your site with various shortcodes.
- **CSV Import/Export:** Seamlessly import and export project data.
- **Customizable Widgets:** Place real-time data widgets on your site.
- **Reporting Intervals:** Customize reporting intervals for power generation and CO2 offset.
- **Localization:** Available in multiple languages for global accessibility.

## Installation

1. Download the plugin zip file.
2. Go to the WordPress Dashboard.
3. Navigate to Plugins > Add New.
4. Click 'Upload Plugin' and choose the downloaded zip file.
5. Click 'Install Now' and then activate the plugin.

## Usage

### Adding and Managing Projects

- Navigate to the **EcoPower Tracker** menu.
- Use the **All Projects** page to view and manage your projects.
- Use the **Add New Project** page to add new projects.

### Shortcodes

Use the following shortcodes to display project data on your WordPress site:

- `[ecopower_tracker_projects]` - Displays a table of all projects.
- `[ecopower_tracker_total_power]` - Displays the total power generated by all projects.
- `[ecopower_tracker_total_co2]` - Displays the total CO2 offset by all projects.
- `[ecopower_tracker_total_power_number]` - Displays the total power generated number only.
- `[ecopower_tracker_total_co2_number]` - Displays the total CO2 offset number only.

### Importing and Exporting Data

- Use the **Import/Export** page to upload or download project data via CSV files.
- Ensure the CSV file is formatted correctly:
  - **Comma-separated** format.
  - **Columns:** `Project Company`, `Project Name`, `Project Location`, `Type of Plant`, `Project CUF`, `Generation Capacity (KW)`, `Date of Activation`.

#### Sample CSV Format

| Project Company | Project Name       | Project Location | Type of Plant | Project CUF | Generation Capacity (KW) | Date of Activation |
|-----------------|--------------------|------------------|---------------|-------------|---------------------------|--------------------|
| Solar Corp      | Solar Plant 1      | Location 1       | Solar         | 0.20        | 1000                      | 2023-01-01         |
| Wind Energy Inc | Wind Farm Alpha    | Location 2       | Wind          | 0.25        | 1500                      | 2023-02-15         |

### Customizing Widgets

- Use the **Widgets** page to configure and place widgets displaying real-time data on your site.

### Reporting Intervals

- Use the **Reporting Intervals** page to set up intervals for summarizing power generation and CO2 offset (e.g., daily, weekly, monthly).

### Support

For support, visit our GitHub page: [EcoPower Tracker on GitHub](https://github.com/saqibj/EcoTracker).

To report issues or request features, open an issue on our GitHub Issues page: [GitHub Issues](https://github.com/saqibj/EcoTracker/issues).

## Changelog

### 2.1e
- Added detailed usage instructions.
- Ensured menu icon size is restricted to 20x20 pixels.
- Fixed project management and CSV import/export functionalities.

### 2.1d
- Separated CSV import, export, and post-import processing into different files.
- Added functionality to check for existing projects before importing.

### 2.1c
- Added support for detailed error messages and output buffering.
- Resolved submenu duplication issues.

### 2.1b
- Major update with enhanced project management, detailed dashboard, and real-time data display.
- Added shortcodes for flexible data presentation.

### 2.1a
- Initial version with basic project management and data tracking features.
