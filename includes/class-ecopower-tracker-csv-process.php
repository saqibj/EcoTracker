// Path: EcoPower-Tracker/includes/class-ecopower-tracker-csv-process.php
// File: class-ecopower-tracker-csv-process.php

<?php
/**
 * CSV Processing functionality
 *
 * @package EcoPowerTracker
 * @since 2.0.1
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'class-ecopower-tracker-error-handler.php';

/**
 * Class EcoPower_Tracker_CSV_Process
 */
class EcoPower_Tracker_CSV_Process {

    /**
     * Required CSV columns with validation rules
     *
     * @var array
     */
    private $field_validation_rules = array(
        'project_number' => array(
            'required' => true,
            'type' => 'string',
            'max_length' => 50,
            'pattern' => '/^[A-Z0-9\-_]+$/i'
        ),
        'project_company' => array(
            'required' => true,
            'type' => 'string',
            'max_length' => 255,
            'min_length' => 2
        ),
        'project_name' => array(
            'required' => true,
            'type' => 'string',
            'max_length' => 255,
            'min_length' => 2
        ),
        'project_location' => array(
            'required' => true,
            'type' => 'string',
            'max_length' => 255,
            'min_length' => 2
        ),
        'type_of_plant' => array(
            'required' => true,
            'type' => 'enum',
            'allowed_values' => array('solar', 'wind', 'hydro', 'biomass', 'geothermal', 'nuclear', 'coal', 'gas', 'other')
        ),
        'project_cuf' => array(
            'required' => true,
            'type' => 'float',
            'min' => 0.0,
            'max' => 100.0
        ),
        'generation_capacity' => array(
            'required' => true,
            'type' => 'float',
            'min' => 0.01,
            'max' => 999999.99
        ),
        'date_of_activation' => array(
            'required' => true,
            'type' => 'date',
            'min_date' => '1900-01-01',
            'max_date' => '2050-12-31'
        )
    );

    /**
     * Processing statistics
     *
     * @var array
     */
    private $processing_stats = array(
        'total_rows' => 0,
        'processed_rows' => 0,
        'skipped_rows' => 0,
        'error_rows' => 0,
        'inserted_rows' => 0,
        'updated_rows' => 0,
        'errors' => array()
    );

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_post_ecopower_tracker_process_csv', array($this, 'process_csv_file'));
    }

    /**
     * Process the uploaded CSV file
     *
     * @return void
     */
    public function process_csv_file() {
        try {
            // Initialize processing stats
            $this->reset_processing_stats();

            // Check system resources
            $system_status = EcoPower_Tracker_Error_Handler::check_system_resources();
            if (!$system_status['memory_ok'] || !$system_status['time_ok']) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'System resources insufficient for CSV processing',
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SYSTEM,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                    $system_status
                );
            }

            // Check capabilities
            if (!current_user_can('manage_options')) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'Unauthorized CSV processing attempt',
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SECURITY,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                    array('user_id' => get_current_user_id())
                );
                wp_die(__('Unauthorized access', 'ecopower-tracker'));
            }

            // Verify nonce
            if (!isset($_POST['ecopower_tracker_nonce']) || 
                !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_process_csv')) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'Nonce verification failed for CSV processing',
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SECURITY,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH
                );
                wp_die(__('Security check failed', 'ecopower-tracker'));
            }

            // Get CSV file path from user meta
            $user_id = get_current_user_id();
            $file_path = get_user_meta($user_id, 'ecopower_tracker_csv_file', true);
            if (empty($file_path)) {
                throw new \Exception(__('No CSV file found for processing', 'ecopower-tracker'));
            }

            // Validate file exists and hasn't been tampered with
            if (!file_exists($file_path)) {
                delete_user_meta($user_id, 'ecopower_tracker_csv_file');
                throw new \Exception(__('CSV file not found', 'ecopower-tracker'));
            }

            // Check file age (prevent processing old files)
            $upload_time = get_user_meta($user_id, 'ecopower_tracker_csv_upload_time', true);
            if ($upload_time && (current_time('timestamp') - $upload_time) > 3600) { // 1 hour
                unlink($file_path);
                delete_user_meta($user_id, 'ecopower_tracker_csv_file');
                delete_user_meta($user_id, 'ecopower_tracker_csv_upload_time');
                throw new \Exception(__('CSV file has expired. Please upload a new file.', 'ecopower-tracker'));
            }

            // Process CSV file with comprehensive error handling
            $processing_result = $this->import_csv_data($file_path);

            // Clean up
            unlink($file_path);
            delete_user_meta($user_id, 'ecopower_tracker_csv_file');
            delete_user_meta($user_id, 'ecopower_tracker_csv_upload_time');

            // Log processing completion
            EcoPower_Tracker_Error_Handler::log_error(
                'CSV processing completed',
                EcoPower_Tracker_Error_Handler::ERROR_TYPE_SYSTEM,
                EcoPower_Tracker_Error_Handler::SEVERITY_LOW,
                $this->processing_stats
            );

            // Determine redirect message based on results
            $message = 'import_success';
            if ($this->processing_stats['error_rows'] > 0) {
                $message = 'import_partial_success';
                
                // Store error details in user meta for display
                update_user_meta($user_id, 'ecopower_tracker_import_errors', $this->processing_stats['errors']);
            }

            // Redirect with appropriate success message
            wp_safe_redirect(add_query_arg(
                array(
                    'page' => 'ecopower-tracker',
                    'message' => $message,
                    'processed' => $this->processing_stats['processed_rows'],
                    'inserted' => $this->processing_stats['inserted_rows'],
                    'updated' => $this->processing_stats['updated_rows'],
                    'errors' => $this->processing_stats['error_rows']
                ),
                admin_url('admin.php')
            ));
            exit;

        } catch (\Exception $e) {
            EcoPower_Tracker_Error_Handler::log_error(
                $e->getMessage(),
                EcoPower_Tracker_Error_Handler::ERROR_TYPE_FILE,
                EcoPower_Tracker_Error_Handler::SEVERITY_CRITICAL,
                array(
                    'processing_stats' => $this->processing_stats,
                    'file_path' => isset($file_path) ? basename($file_path) : null
                )
            );

            // Clean up on error
            if (isset($file_path) && file_exists($file_path)) {
                unlink($file_path);
            }
            if (isset($user_id)) {
                delete_user_meta($user_id, 'ecopower_tracker_csv_file');
                delete_user_meta($user_id, 'ecopower_tracker_csv_upload_time');
            }

            $user_message = EcoPower_Tracker_Error_Handler::format_user_error(
                $e->getMessage(),
                null,
                array(
                    __('Verify your CSV file format matches the sample', 'ecopower-tracker'),
                    __('Check that all required fields are present', 'ecopower-tracker'),
                    __('Ensure data values are within valid ranges', 'ecopower-tracker'),
                    __('Try uploading a smaller file if the issue persists', 'ecopower-tracker')
                )
            );

            wp_die(
                esc_html($user_message),
                esc_html__('Processing Error', 'ecopower-tracker'),
                array('response' => 500, 'back_link' => true)
            );
        }
    }

    /**
     * Import CSV data
     *
     * @param string $file_path Path to CSV file
     * @return array Processing result
     * @throws \Exception If import fails
     */
    private function import_csv_data($file_path) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';

        // Start transaction with proper isolation level
        $wpdb->query('START TRANSACTION');
        $wpdb->query('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');

        try {
            $handle = fopen($file_path, 'r');
            if ($handle === false) {
                throw new \Exception(__('Failed to open CSV file', 'ecopower-tracker'));
            }

            // Validate header row
            $header = fgetcsv($handle);
            $this->validate_csv_headers($header);

            // Create column mapping for case-insensitive matching
            $column_mapping = $this->create_column_mapping($header);

            // Process rows with comprehensive error handling
            $row_number = 1; // Header is row 1
            $batch_size = 50; // Process in batches to manage memory
            $batch_data = array();
            $continue_processing = true;

            while (($data = fgetcsv($handle)) !== false && $continue_processing) {
                $row_number++;
                $this->processing_stats['total_rows']++;

                try {
                    // Skip completely empty rows
                    if (empty(array_filter($data, function($value) { return trim($value) !== ''; }))) {
                        $this->processing_stats['skipped_rows']++;
                        continue;
                    }

                    // Prepare and validate data with line number context
                    $project_data = $this->prepare_and_validate_project_data($data, $column_mapping, $row_number);
                    
                    if ($project_data['valid']) {
                        $batch_data[] = $project_data['data'];
                        
                        // Process batch when it reaches the batch size
                        if (count($batch_data) >= $batch_size) {
                            $this->process_batch($batch_data, $table_name);
                            $batch_data = array();
                            
                            // Check memory usage and break if getting too high
                            if (memory_get_usage(true) > (ini_get('memory_limit') ? $this->convert_memory_limit() * 0.9 : 128 * 1024 * 1024)) {
                                EcoPower_Tracker_Error_Handler::log_error(
                                    'Memory limit approaching during CSV processing',
                                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SYSTEM,
                                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                                    array('row_number' => $row_number, 'memory_usage' => memory_get_usage(true))
                                );
                                break;
                            }
                        }
                        
                        $this->processing_stats['processed_rows']++;
                    } else {
                        $this->processing_stats['error_rows']++;
                        $this->processing_stats['errors'][] = array(
                            'row' => $row_number,
                            'errors' => $project_data['errors']
                        );
                    }

                    // Prevent infinite loops on corrupted files
                    if ($row_number > 50000) {
                        EcoPower_Tracker_Error_Handler::log_error(
                            'CSV processing stopped: too many rows',
                            EcoPower_Tracker_Error_Handler::ERROR_TYPE_VALIDATION,
                            EcoPower_Tracker_Error_Handler::SEVERITY_MEDIUM,
                            array('row_number' => $row_number)
                        );
                        break;
                    }

                } catch (\Exception $row_exception) {
                    $this->processing_stats['error_rows']++;
                    $this->processing_stats['errors'][] = array(
                        'row' => $row_number,
                        'errors' => array($row_exception->getMessage())
                    );

                    EcoPower_Tracker_Error_Handler::log_error(
                        'Row processing error: ' . $row_exception->getMessage(),
                        EcoPower_Tracker_Error_Handler::ERROR_TYPE_VALIDATION,
                        EcoPower_Tracker_Error_Handler::SEVERITY_MEDIUM,
                        array('row_number' => $row_number),
                        $row_number
                    );

                    // Continue processing other rows unless it's a critical error
                    if ($this->processing_stats['error_rows'] > 100) {
                        throw new \Exception(__('Too many row errors. Processing stopped.', 'ecopower-tracker'));
                    }
                }
            }

            // Process remaining batch
            if (!empty($batch_data)) {
                $this->process_batch($batch_data, $table_name);
            }

            fclose($handle);

            // Only commit if we have some successful processing
            if ($this->processing_stats['processed_rows'] > 0) {
                $wpdb->query('COMMIT');
            } else {
                $wpdb->query('ROLLBACK');
                throw new \Exception(__('No valid data rows were found to import', 'ecopower-tracker'));
            }

            return $this->processing_stats;

        } catch (\Exception $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            $wpdb->query('ROLLBACK');
            throw $e;
        }
    }

    /**
     * Validate CSV headers
     *
     * @param array $headers CSV header row
     * @return void
     * @throws \Exception If headers are invalid
     */
    private function validate_csv_headers($headers) {
        if (!$headers || !is_array($headers)) {
            throw new \Exception(__('Invalid CSV headers', 'ecopower-tracker'));
        }

        // Sanitize headers
        $headers = array_map('sanitize_text_field', $headers);
        $headers = array_map('trim', $headers);

        // Get required column names
        $required_columns = array_keys($this->field_validation_rules);

        // Check for required columns (case-insensitive)
        $headers_lower = array_map('strtolower', $headers);
        $missing_columns = array();
        
        foreach ($required_columns as $required_col) {
            if (!in_array(strtolower($required_col), $headers_lower)) {
                $missing_columns[] = $required_col;
            }
        }

        if (!empty($missing_columns)) {
            /* translators: %s: List of missing columns */
            throw new \Exception(sprintf(
                __('Missing required columns: %s', 'ecopower-tracker'),
                implode(', ', $missing_columns)
            ));
        }

        // Check for malicious column names
        $malicious_patterns = array('/\.\./', '/\./', '<', '>', '%', 'script', 'javascript', 'vbscript');
        foreach ($headers as $header) {
            foreach ($malicious_patterns as $pattern) {
                if (is_string($pattern) && stripos($header, $pattern) !== false) {
                    throw new \Exception(__('Invalid column name detected', 'ecopower-tracker'));
                } elseif (preg_match($pattern, $header)) {
                    throw new \Exception(__('Invalid column name detected', 'ecopower-tracker'));
                }
            }
        }

        // Check for duplicate column names
        $header_counts = array_count_values($headers_lower);
        $duplicates = array_filter($header_counts, function($count) { return $count > 1; });
        if (!empty($duplicates)) {
            throw new \Exception(sprintf(
                /* translators: %s: List of duplicate column names */
                __('Duplicate column names found: %s', 'ecopower-tracker'),
                implode(', ', array_keys($duplicates))
            ));
        }
    }

    /**
     * Create column mapping for case-insensitive header matching
     *
     * @param array $headers CSV headers
     * @return array Column mapping
     */
    private function create_column_mapping($headers) {
        $mapping = array();
        $required_columns = array_keys($this->field_validation_rules);
        
        foreach ($required_columns as $required_col) {
            $found = false;
            foreach ($headers as $index => $header) {
                if (strtolower(trim($header)) === strtolower($required_col)) {
                    $mapping[$required_col] = $index;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                throw new \Exception(sprintf(
                    /* translators: %s: Name of the missing required column */
                    __('Required column "%s" not found in CSV headers', 'ecopower-tracker'),
                    $required_col
                ));
            }
        }
        
        return $mapping;
    }

    /**
     * Prepare and validate project data from CSV row
     *
     * @param array $data Row data
     * @param array $column_mapping Column mapping
     * @param int $row_number Row number for error reporting
     * @return array Validation result with data and errors
     */
    private function prepare_and_validate_project_data($data, $column_mapping, $row_number) {
        $project_data = array();
        $errors = array();
        $valid = true;

        foreach ($this->field_validation_rules as $field_name => $rules) {
            $column_index = $column_mapping[$field_name];
            $raw_value = isset($data[$column_index]) ? $data[$column_index] : '';

            // Validate field using centralized error handler
            $validation_result = EcoPower_Tracker_Error_Handler::validate_field(
                $raw_value,
                $field_name,
                $rules['type'],
                $rules
            );

            if ($validation_result['success']) {
                $project_data[$field_name] = $validation_result['value'];
            } else {
                $valid = false;
                $errors = array_merge($errors, $validation_result['errors']);
            }
        }

        // Additional business logic validation
        if ($valid) {
            $business_validation = $this->validate_business_rules($project_data, $row_number);
            if (!$business_validation['success']) {
                $valid = false;
                $errors = array_merge($errors, $business_validation['errors']);
            }
        }

        return array(
            'valid' => $valid,
            'data' => $project_data,
            'errors' => $errors
        );
    }

    /**
     * Validate business rules for project data
     *
     * @param array $project_data Project data
     * @param int $row_number Row number for error reporting
     * @return array Validation result
     */
    private function validate_business_rules($project_data, $row_number) {
        $errors = array();

        // Check for duplicate project number
        global $wpdb;
        $table_name = $wpdb->prefix . 'ecopower_tracker_projects';
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE project_number = %s",
            $project_data['project_number']
        ));

        // Validate CUF ranges by plant type
        $cuf_ranges = array(
            'solar' => array('min' => 10.0, 'max' => 35.0),
            'wind' => array('min' => 20.0, 'max' => 60.0),
            'hydro' => array('min' => 30.0, 'max' => 90.0),
            'biomass' => array('min' => 60.0, 'max' => 85.0),
            'geothermal' => array('min' => 70.0, 'max' => 95.0),
            'nuclear' => array('min' => 75.0, 'max' => 95.0),
            'coal' => array('min' => 40.0, 'max' => 85.0),
            'gas' => array('min' => 35.0, 'max' => 60.0),
            'other' => array('min' => 0.0, 'max' => 100.0)
        );

        $plant_type = strtolower($project_data['type_of_plant']);
        if (isset($cuf_ranges[$plant_type])) {
            $range = $cuf_ranges[$plant_type];
            if ($project_data['project_cuf'] < $range['min'] || $project_data['project_cuf'] > $range['max']) {
                $errors[] = sprintf(
                    /* translators: 1: CUF percentage value, 2: Plant type, 3: Minimum typical range, 4: Maximum typical range */
                    __('CUF value %1$.2f%% is unusual for %2$s plants (typical range: %3$.1f%% - %4$.1f%%)', 'ecopower-tracker'),
                    $project_data['project_cuf'],
                    ucfirst($plant_type),
                    $range['min'],
                    $range['max']
                );
            }
        }

        // Validate date is not in the future
        $activation_date = date_create($project_data['date_of_activation']);
        $today = date_create('today');
        if ($activation_date > $today) {
            $errors[] = __('Date of activation cannot be in the future', 'ecopower-tracker');
        }

        // Validate reasonable capacity ranges
        if ($project_data['generation_capacity'] > 10000) {
            $errors[] = sprintf(
                /* translators: %s: Generation capacity value in KW */
                __('Generation capacity %.2f KW seems unusually high. Please verify.', 'ecopower-tracker'),
                $project_data['generation_capacity']
            );
        }

        return array(
            'success' => empty($errors),
            'errors' => $errors
        );
    }

    /**
     * Process batch of validated project data
     *
     * @param array $batch_data Array of project data
     * @param string $table_name Database table name
     * @return void
     * @throws \Exception If batch processing fails
     */
    private function process_batch($batch_data, $table_name) {
        global $wpdb;

        foreach ($batch_data as $project_data) {
            try {
                $result = $this->insert_or_update_project($project_data, $table_name);
                
                if ($result['action'] === 'insert') {
                    $this->processing_stats['inserted_rows']++;
                } else {
                    $this->processing_stats['updated_rows']++;
                }

            } catch (\Exception $e) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'Database operation failed: ' . $e->getMessage(),
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_DATABASE,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                    array('project_data' => $project_data)
                );
                throw $e;
            }
        }
    }

    /**
     * Insert or update project in database
     *
     * @param array $project_data Project data
     * @param string $table_name Database table name
     * @return array Operation result
     * @throws \Exception If database operation fails
     */
    private function insert_or_update_project($project_data, $table_name) {
        global $wpdb;

        // Check if project exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE project_number = %s",
            $project_data['project_number']
        ));

        $action = '';
        $result = false;

        if ($existing) {
            // Update existing project
            $result = $wpdb->update(
                $table_name,
                $project_data,
                array('project_number' => $project_data['project_number']),
                array('%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s'),
                array('%s')
            );
            $action = 'update';
        } else {
            // Insert new project
            $result = $wpdb->insert(
                $table_name, 
                $project_data,
                array('%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s')
            );
            $action = 'insert';
        }

        if ($result === false) {
            $error_message = $wpdb->last_error ?: __('Database operation failed', 'ecopower-tracker');
            throw new \Exception($error_message);
        }

        return array(
            'action' => $action,
            'result' => $result
        );
    }

    /**
     * Reset processing statistics
     *
     * @return void
     */
    private function reset_processing_stats() {
        $this->processing_stats = array(
            'total_rows' => 0,
            'processed_rows' => 0,
            'skipped_rows' => 0,
            'error_rows' => 0,
            'inserted_rows' => 0,
            'updated_rows' => 0,
            'errors' => array()
        );
    }

    /**
     * Convert memory limit string to bytes
     *
     * @return int Memory limit in bytes
     */
    private function convert_memory_limit() {
        $memory_limit = ini_get('memory_limit');
        if ($memory_limit === '-1') {
            return PHP_INT_MAX;
        }
        
        $unit = strtolower(substr($memory_limit, -1));
        $value = intval($memory_limit);
        
        switch ($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
}

// Initialize the CSV processing functionalities
new EcoPower_Tracker_CSV_Process();

?>