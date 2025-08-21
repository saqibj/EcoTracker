# EcoPower Tracker - Admin Interface

This directory contains the admin interface templates for the EcoPower Tracker WordPress plugin.

## Files

### project-list.php
Displays a table of all projects with options to view, edit, and delete projects.

### project-form.php
A form for adding new projects or editing existing ones.

## Features

- **Project Management**: Add, edit, and delete renewable energy projects
- **Data Validation**: Client and server-side validation for all form fields
- **Responsive Design**: Works on all device sizes
- **Date Picker**: Easy date selection for project activation dates
- **Bulk Actions**: Perform actions on multiple projects at once
- **Search & Filter**: Find projects quickly with search and filter options

## Usage

1. **Viewing Projects**
   - Navigate to "EcoPower Tracker" in the WordPress admin menu
   - View all projects in a sortable table
   - Use the search box to find specific projects

2. **Adding a New Project**
   - Click "Add New" from the EcoPower Tracker menu or the button at the top of the projects list
   - Fill in the project details
   - Click "Add Project" to save

3. **Editing a Project**
   - Find the project in the projects list
   - Click the "Edit" button
   - Make your changes and click "Update Project"

4. **Deleting a Project**
   - Find the project in the projects list
   - Click the "Delete" button
   - Confirm the deletion when prompted

## Form Fields

- **Project Number**: A unique identifier for the project (required)
- **Company**: The company that owns or operates the project (required)
- **Project Name**: The name of the project (required)
- **Location**: The physical location of the project (required)
- **Type of Plant**: The type of renewable energy plant (solar, wind, etc.) (required)
- **Generation Capacity**: The power generation capacity in MW (required)
- **Capacity Utilization Factor (CUF)**: The capacity factor as a percentage (0-100)
- **Date of Activation**: When the project became operational

## Security

- All form submissions are verified with nonces
- User capabilities are checked before performing actions
- All data is properly escaped before output
- Input is sanitized before database operations

## Browser Support

The admin interface is tested and works in all modern browsers including:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (basic functionality)
