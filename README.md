# EcoPower Tracker

**Version**: 2.4.0  
**Author**: Saqib Jawaid  
**License**: GPL v3  
**WordPress**: 5.0+  
**PHP**: 7.4+

## Overview

EcoPower Tracker is a comprehensive WordPress plugin designed to manage and display data from renewable energy projects, focusing on solar, wind, hydro, biomass, and geothermal energy. It provides powerful tools for tracking power generation, CO2 offset calculations, and project management with modern WordPress features.

## ✨ Key Features

### 🔒 **Security & Performance**
- **Advanced Caching System**: Intelligent caching with versioning and automatic invalidation
- **SQL Injection Protection**: Secure database queries with prepared statements
- **Input Sanitization**: Comprehensive data validation and sanitization
- **Nonce Verification**: Enhanced security for all form submissions

### 📊 **Data Management**
- **Project Management**: Complete CRUD operations for renewable energy projects
- **CSV Import/Export**: Bulk data management with validation and error handling
- **Real-time Statistics**: Live dashboard with project summaries and analytics
- **Multi-format Support**: Support for various renewable energy project types

### 🎨 **User Experience**
- **Modern Admin Interface**: Clean, responsive dashboard with intuitive navigation
- **Accessibility Features**: ARIA attributes and screen reader compatibility
- **Responsive Design**: Mobile-friendly interface for all devices
- **Gutenberg Blocks**: Native block editor support for modern WordPress

### 🔌 **Developer Features**
- **REST API**: Modern API endpoints for external integrations
- **Hook System**: Extensive WordPress hooks for customization
- **Namespace Support**: Clean, organized code structure
- **Error Logging**: Comprehensive debugging and monitoring

### 🌐 **Display Options**
- **Shortcodes**: 15+ shortcodes for flexible data display
- **Frontend Templates**: Customizable project display templates
- **AJAX Filtering**: Dynamic project filtering and pagination
- **Statistics Widgets**: Real-time data visualization

## 🚀 Installation

### Requirements
- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher

### Quick Install
1. **Download & Upload**:
   - Download the latest version from [GitHub](https://github.com/saqibj/EcoTracker)
   - Upload to `/wp-content/plugins/ecopower-tracker/` or install via WordPress admin

2. **Activate**:
   - Go to **Plugins** → **Installed Plugins**
   - Find "EcoPower Tracker" and click **Activate**

3. **Configure**:
   - Navigate to **EcoPower Tracker** in your admin menu
   - Import your project data via CSV or add projects manually
   - Configure cache settings for optimal performance

### First-Time Setup
1. **Import Data**: Use the CSV import feature to bulk upload your renewable energy project data
2. **Configure Cache**: Access the cache management interface to optimize performance
3. **Customize Display**: Use shortcodes to display data on your frontend
4. **Set Permissions**: Ensure proper user capabilities are configured

## 📖 Usage Guide

### 🎛️ Admin Dashboard

The admin dashboard provides comprehensive project management capabilities:

- **📊 Statistics Overview**: Real-time display of total projects, generation capacity, and plant type distribution
- **📋 Project Management**: View, edit, and delete individual projects with full CRUD operations
- **📁 Data Import/Export**: Bulk CSV import with validation and error handling, plus data export functionality
- **⚡ Cache Management**: Monitor and control caching performance with built-in cache administration
- **🔧 Tools & Utilities**: Access to import/export tools and system maintenance functions

### 🎯 Shortcodes Reference

EcoPower Tracker provides 15+ powerful shortcodes for flexible data display:

#### **📈 Total Statistics**
```php
[ecopower_tracker_total_power]     // Total power generated (all projects)
[ecopower_tracker_total_co2]       // Total CO2 offset (all projects)
```

#### **🏢 Individual Project Data**
```php
[ecopower_tracker_project_power project_id="123"]      // Power by specific project
[ecopower_tracker_project_co2 project_id="123"]        // CO2 offset by project
[ecopower_tracker_project_capacity project_id="123"]   // Capacity by project
```

#### **🏭 Company-Based Statistics**
```php
[ecopower_tracker_company_power company="Green Energy Corp"]     // Power by company
[ecopower_tracker_company_co2 company="Green Energy Corp"]       // CO2 by company
[ecopower_tracker_company_capacity company="Green Energy Corp"]  // Capacity by company
```

#### **📍 Location-Based Statistics**
```php
[ecopower_tracker_location_power location="California"]     // Power by location
[ecopower_tracker_location_co2 location="California"]       // CO2 by location
[ecopower_tracker_location_capacity location="California"]  // Capacity by location
```

#### **⚡ Plant Type Statistics**
```php
[ecopower_tracker_type_power type="solar"]     // Power by plant type
[ecopower_tracker_type_co2 type="solar"]       // CO2 by plant type
[ecopower_tracker_type_capacity type="solar"]  // Capacity by plant type
```

### 🎨 Frontend Display

#### **Project Grid Display**
```php
[ecopower_projects]                    // Display all projects in a grid
[ecopower_projects limit="10"]         // Limit number of projects
[ecopower_projects type="solar"]       // Filter by plant type
```

#### **Single Project View**
```php
[ecopower_project id="123"]            // Display specific project details
```

#### **Statistics Dashboard**
```php
[ecopower_stats]                       // Display comprehensive statistics
```

### 🔧 Advanced Features

#### **Cache Management**
- **Automatic Caching**: All shortcodes are automatically cached for optimal performance
- **Cache Warming**: Scheduled cache warming to pre-populate frequently accessed data
- **Cache Invalidation**: Automatic cache clearing when data is updated
- **Admin Interface**: Built-in cache management tools in the admin dashboard

#### **REST API Endpoints**
```php
GET /wp-json/ecopower-tracker/v1/projects     // List all projects
GET /wp-json/ecopower-tracker/v1/stats        // Get statistics
GET /wp-json/ecopower-tracker/v1/projects/{id} // Get specific project
```

#### **Gutenberg Blocks**
- **Project Display Block**: Add project data to any page using the block editor
- **Statistics Block**: Display real-time statistics in your content
- **Customizable**: Full customization options within the block editor

### 💡 Usage Examples

#### **Basic Implementation**
```php
// Display total renewable energy generation
Total Power Generated: [ecopower_tracker_total_power] MW

// Show CO2 offset for a specific company
Green Energy Corp CO2 Offset: [ecopower_tracker_company_co2 company="Green Energy Corp"] tons

// Display solar projects in California
California Solar Capacity: [ecopower_tracker_type_capacity type="solar" location="California"] MW
```

#### **Advanced Dashboard**
```php
// Create a comprehensive energy dashboard
<div class="energy-dashboard">
    <h2>Renewable Energy Overview</h2>
    <div class="stats-grid">
        <div>Total Projects: [ecopower_tracker_total_power]</div>
        <div>Solar Capacity: [ecopower_tracker_type_capacity type="solar"]</div>
        <div>Wind Capacity: [ecopower_tracker_type_capacity type="wind"]</div>
    </div>
    [ecopower_projects limit="5"]
</div>
```

## 🛠️ Development & Support

### **Contributing**
We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

### **Support**
- **GitHub Issues**: [Report bugs and request features](https://github.com/saqibj/EcoTracker/issues)
- **Documentation**: [Full documentation](https://github.com/saqibj/EcoTracker/wiki)
- **Community**: [Join our discussions](https://github.com/saqibj/EcoTracker/discussions)

### **System Requirements**
- **WordPress**: 5.0+ (tested up to 6.5+)
- **PHP**: 7.4+ (recommended 8.0+)
- **MySQL**: 5.6+ or MariaDB 10.1+
- **Memory**: 128MB minimum (256MB recommended)
- **Storage**: 10MB for plugin files + data storage

## 📋 Changelog

### 2.4.0 - 2025-01-27
#### 🔒 Security
- **CRITICAL**: Fixed SQL injection vulnerability in dashboard statistics queries
- **CRITICAL**: Eliminated potential "headers already sent" errors from malformed file structure
- Enhanced input sanitization and validation across all admin functions
- Improved nonce verification for all form submissions

#### 🚀 Performance
- **NEW**: Implemented comprehensive caching system for all shortcodes
- **NEW**: Added cache versioning and automatic invalidation
- **NEW**: Added cache warming functionality with scheduled cron jobs
- **NEW**: Added cache management admin interface
- Optimized database queries with proper indexing
- Reduced server load with intelligent cache strategies

#### 🏗️ Architecture
- **NEW**: Added REST API endpoints for modern integrations
- **NEW**: Added Gutenberg block support for the block editor
- **NEW**: Added cache administration interface
- Fixed direct class instantiation issues in templates
- Improved dependency injection patterns
- Enhanced WordPress hook integration

#### 🎨 User Experience
- **NEW**: Added accessibility improvements with ARIA attributes
- **NEW**: Enhanced admin interface with better error handling
- Fixed asset loading issues with proper WordPress enqueue system
- Improved template structure and organization
- Enhanced responsive design for mobile devices

### 2.3.0 - 2025-01-26
- Modern WordPress features (REST API, Gutenberg blocks)
- Comprehensive caching system for shortcodes
- Cache management admin interface
- Enhanced accessibility features
- Improved error handling and logging

### 2.2.0 - 2025-08-21
#### Added
- Frontend shortcodes for project display
- AJAX-powered project filtering and pagination
- Responsive project grid and single project views
- Statistics dashboard with visual charts
- Sample CSV import/export functionality
- Comprehensive documentation for all shortcodes

#### Changed
- Improved database queries for better performance
- Enhanced security with nonce verification
- Updated admin interface for better usability
- Optimized asset loading with proper versioning

#### Fixed
- Fixed issues with CSV import/export
- Resolved compatibility with WordPress 6.5+
- Fixed responsive layout issues on mobile devices
- Addressed minor UI/UX bugs

### 2.1.0 - 2025-02-09
- Full compliance with WordPress best practices
- Added translators comments for all placeholder strings
- Refactored session usage to user meta for CSV processing
- Improved caching for shortcodes (performance)
- Enhanced CSV header security and validation
- Added minified asset support for frontend performance
- Added composite database indexes for faster queries
- Introduced centralized Config class for settings
- Improved uninstall cleanup (removes all plugin data)
- Updated translation (.pot) workflow and coverage
- General codebase cleanup and modernization

### 2.0.2
- Enhanced security with improved input validation and sanitization
- Added comprehensive error handling throughout the plugin
- Improved JavaScript functionality with better event handling
- Enhanced CSV processing with better validation
- Added detailed admin interface styling
- Improved responsive design for all screen sizes
- Added loading states for better UX
- Enhanced data filtering and sorting capabilities
- Added proper documentation across all files
- Improved translation support with updated POT file
- Added proper namespace implementation
- Enhanced database operations with prepared statements
- Added proper capability checks
- Improved file organization and structure
- Added comprehensive error logging

### 2.0.1
- Added responsive design improvements
- Enhanced CSV import validation
- Added new shortcodes
- Improved error handling
- Added data filtering options

### 2.0.0
- Initial release with core functionalities:
  - Project management and data display.
  - CSV import and export capabilities.
  - Dashboard view with project list and total summaries.
  - Comprehensive shortcode support.

---

## License

This plugin is licensed under the GPL v3 License. See the [LICENSE](LICENSE) file for more details.
