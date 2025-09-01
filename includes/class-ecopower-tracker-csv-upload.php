// Path: EcoPower-Tracker/includes/class-ecopower-tracker-csv-upload.php
// File: class-ecopower-tracker-csv-upload.php

<?php
/**
 * CSV Upload functionality
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
 * Class EcoPower_Tracker_CSV_Upload
 */
class EcoPower_Tracker_CSV_Upload {

    /**
     * Allowed mime types for upload
     *
     * @var array
     */
    private $allowed_mime_types = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
    );

    /**
     * Maximum file size in bytes (default 5MB)
     *
     * @var int
     */
    private $max_file_size = 5242880;

    /**
     * Allowed file encodings
     *
     * @var array
     */
    private $allowed_encodings = array(
        'UTF-8',
        'ISO-8859-1',
        'Windows-1252',
        'ASCII'
    );

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_post_ecopower_tracker_upload_csv', array($this, 'handle_csv_upload'));
        
        // Allow filtering of max file size
        $this->max_file_size = apply_filters('ecopower_tracker_max_file_size', $this->max_file_size);
    }

    /**
     * Handle CSV file upload
     *
     * @return void
     */
    public function handle_csv_upload() {
        try {
            // Check system resources before processing
            $system_status = EcoPower_Tracker_Error_Handler::check_system_resources();
            if (!empty($system_status['warnings'])) {
                foreach ($system_status['warnings'] as $warning) {
                    EcoPower_Tracker_Error_Handler::log_error(
                        $warning,
                        EcoPower_Tracker_Error_Handler::ERROR_TYPE_SYSTEM,
                        EcoPower_Tracker_Error_Handler::SEVERITY_MEDIUM
                    );
                }
            }

            // Check capabilities
            if (!current_user_can('manage_options')) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'Unauthorized CSV upload attempt',
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SECURITY,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                    array('user_id' => get_current_user_id())
                );
                wp_die(__('Unauthorized access', 'ecopower-tracker'));
            }

            // Verify nonce
            if (!isset($_POST['ecopower_tracker_nonce']) || 
                !wp_verify_nonce($_POST['ecopower_tracker_nonce'], 'ecopower_tracker_upload_csv')) {
                EcoPower_Tracker_Error_Handler::log_error(
                    'Nonce verification failed for CSV upload',
                    EcoPower_Tracker_Error_Handler::ERROR_TYPE_SECURITY,
                    EcoPower_Tracker_Error_Handler::SEVERITY_HIGH
                );
                wp_die(__('Security check failed', 'ecopower-tracker'));
            }

            // Check file upload
            if (!isset($_FILES['ecopower_tracker_csv']) || 
                empty($_FILES['ecopower_tracker_csv']['tmp_name'])) {
                throw new \Exception(__('No file uploaded', 'ecopower-tracker'));
            }

            $file = $_FILES['ecopower_tracker_csv'];

            // Comprehensive file validation
            $validation_result = $this->comprehensive_file_validation($file);
            if (!$validation_result['success']) {
                $error_message = implode(' ', $validation_result['errors']);
                throw new \Exception($error_message);
            }

            // Create upload directory if it doesn't exist
            $upload_dir = $this->get_upload_directory();
            if (!file_exists($upload_dir)) {
                if (!wp_mkdir_p($upload_dir)) {
                    throw new \Exception(__('Failed to create upload directory', 'ecopower-tracker'));
                }
                
                // Secure the upload directory
                $this->secure_upload_directory($upload_dir);
            }

            // Generate unique filename with timestamp
            $filename = sprintf(
                'ecopower_%s_%s.csv',
                get_current_user_id(),
                date('Y-m-d_H-i-s')
            );
            $destination = $upload_dir . '/' . $filename;

            // Move uploaded file with additional security checks
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new \Exception(__('Failed to move uploaded file', 'ecopower-tracker'));
            }

            // Set proper file permissions
            chmod($destination, 0644);

            // Validate CSV structure before processing
            $csv_validation = $this->validate_csv_structure($destination);
            if (!$csv_validation['success']) {
                unlink($destination); // Clean up invalid file
                throw new \Exception($csv_validation['error']);
            }

            // Set user meta variable for processing
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'ecopower_tracker_csv_file', $destination);
            update_user_meta($user_id, 'ecopower_tracker_csv_upload_time', current_time('timestamp'));

            // Log successful upload
            EcoPower_Tracker_Error_Handler::log_error(
                'CSV file uploaded successfully',
                EcoPower_Tracker_Error_Handler::ERROR_TYPE_SYSTEM,
                EcoPower_Tracker_Error_Handler::SEVERITY_LOW,
                array(
                    'filename' => $filename,
                    'file_size' => $file['size'],
                    'row_count' => $csv_validation['row_count']
                )
            );

            // Redirect to processing page
            wp_safe_redirect(add_query_arg(
                array(
                    'page' => 'ecopower-tracker',
                    'action' => 'process_csv',
                    '_wpnonce' => wp_create_nonce('ecopower_tracker_process_csv')
                ),
                admin_url('admin.php')
            ));
            exit;

        } catch (\Exception $e) {
            EcoPower_Tracker_Error_Handler::log_error(
                $e->getMessage(),
                EcoPower_Tracker_Error_Handler::ERROR_TYPE_FILE,
                EcoPower_Tracker_Error_Handler::SEVERITY_HIGH,
                array(
                    'file_info' => isset($file) ? array(
                        'name' => $file['name'],
                        'size' => $file['size'],
                        'type' => $file['type']
                    ) : null
                )
            );

            $user_message = EcoPower_Tracker_Error_Handler::format_user_error(
                $e->getMessage(),
                null,
                array(
                    __('Ensure your file is a valid CSV format', 'ecopower-tracker'),
                    __('Check that your file is not corrupted', 'ecopower-tracker'),
                    __('Verify the file size is within limits', 'ecopower-tracker'),
                    __('Make sure the file contains the required columns', 'ecopower-tracker')
                )
            );

            wp_die(
                esc_html($user_message),
                esc_html__('Upload Error', 'ecopower-tracker'),
                array('response' => 500, 'back_link' => true)
            );
        }
    }

    /**
     * Comprehensive file validation
     *
     * @param array $file Uploaded file information
     * @return array Validation result with success status and errors
     */
    private function comprehensive_file_validation($file) {
        $errors = array();

        // Use centralized security validation
        $security_result = EcoPower_Tracker_Error_Handler::validate_file_security($file);
        if (!$security_result['success']) {
            $errors = array_merge($errors, $security_result['errors']);
        }

        // Additional CSV-specific validations
        if (isset($file['name'])) {
            // Check file extension
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'csv') {
                $errors[] = __('File must have a .csv extension', 'ecopower-tracker');
            }
        }

        // Check file size against our limit
        if (isset($file['size']) && $file['size'] > $this->max_file_size) {
            $errors[] = sprintf(
                /* translators: 1: Actual file size, 2: Maximum allowed file size */
                __('File size (%1$s) exceeds maximum allowed size (%2$s)', 'ecopower-tracker'),
                size_format($file['size']),
                size_format($this->max_file_size)
            );
        }

        // Validate mime type
        if (isset($file['tmp_name']) && file_exists($file['tmp_name'])) {
            $mime_type = mime_content_type($file['tmp_name']);
            if (!in_array($mime_type, $this->allowed_mime_types, true)) {
                $errors[] = sprintf(
                    /* translators: %s: Detected file MIME type */
                    __('Invalid file type (%s). Please upload a CSV file.', 'ecopower-tracker'),
                    $mime_type
                );
            }

            // Validate file encoding
            $encoding_result = $this->validate_file_encoding($file['tmp_name']);
            if (!$encoding_result['success']) {
                $errors[] = $encoding_result['error'];
            }
        }

        return array(
            'success' => empty($errors),
            'errors' => $errors
        );
    }

    /**
     * Validate file encoding
     *
     * @param string $file_path Path to uploaded file
     * @return array Validation result
     */
    private function validate_file_encoding($file_path) {
        // Read first 1KB to check encoding
        $sample = file_get_contents($file_path, false, null, 0, 1024);
        if ($sample === false) {
            return array(
                'success' => false,
                'error' => __('Unable to read file for encoding validation', 'ecopower-tracker')
            );
        }

        // Detect encoding
        $detected_encoding = mb_detect_encoding($sample, $this->allowed_encodings, true);
        if ($detected_encoding === false) {
            return array(
                'success' => false,
                'error' => sprintf(
                    /* translators: %s: List of supported file encodings */
                    __('File encoding not supported. Supported encodings: %s', 'ecopower-tracker'),
                    implode(', ', $this->allowed_encodings)
                )
            );
        }

        return array(
            'success' => true,
            'encoding' => $detected_encoding
        );
    }

    /**
     * Validate CSV file structure
     *
     * @param string $file_path Path to CSV file
     * @return array Validation result with row count
     */
    private function validate_csv_structure($file_path) {
        $handle = fopen($file_path, 'r');
        if ($handle === false) {
            return array(
                'success' => false,
                'error' => __('Unable to open CSV file for validation', 'ecopower-tracker')
            );
        }

        try {
            // Check if file is empty
            if (filesize($file_path) === 0) {
                return array(
                    'success' => false,
                    'error' => __('CSV file is empty', 'ecopower-tracker')
                );
            }

            // Read and validate header
            $header = fgetcsv($handle);
            if ($header === false || empty($header)) {
                return array(
                    'success' => false,
                    'error' => __('CSV file has no header row', 'ecopower-tracker')
                );
            }

            // Check for required columns (basic check)
            $required_columns = array(
                'project_number',
                'project_company', 
                'project_name',
                'project_location',
                'type_of_plant',
                'project_cuf',
                'generation_capacity',
                'date_of_activation'
            );

            $header_lower = array_map('strtolower', array_map('trim', $header));
            $missing_columns = array();
            
            foreach ($required_columns as $required_col) {
                if (!in_array(strtolower($required_col), $header_lower)) {
                    $missing_columns[] = $required_col;
                }
            }

            if (!empty($missing_columns)) {
                return array(
                    'success' => false,
                    'error' => sprintf(
                        /* translators: %s: List of missing required columns */
                        __('Missing required columns: %s', 'ecopower-tracker'),
                        implode(', ', $missing_columns)
                    )
                );
            }

            // Count rows (excluding header)
            $row_count = 0;
            while (fgetcsv($handle) !== false) {
                $row_count++;
                
                // Prevent processing extremely large files during validation
                if ($row_count > 10000) {
                    break;
                }
            }

            if ($row_count === 0) {
                return array(
                    'success' => false,
                    'error' => __('CSV file contains no data rows', 'ecopower-tracker')
                );
            }

            return array(
                'success' => true,
                'row_count' => $row_count
            );

        } finally {
            fclose($handle);
        }
    }

    /**
     * Secure upload directory
     *
     * @param string $upload_dir Upload directory path
     * @return void
     */
    private function secure_upload_directory($upload_dir) {
        // Create .htaccess file to prevent direct access
        $htaccess_content = "# EcoPower Tracker Upload Security\n";
        $htaccess_content .= "Options -Indexes\n";
        $htaccess_content .= "Options -ExecCGI\n";
        $htaccess_content .= "<Files *.php>\n";
        $htaccess_content .= "    Require all denied\n";
        $htaccess_content .= "</Files>\n";
        $htaccess_content .= "<Files *.js>\n";
        $htaccess_content .= "    Require all denied\n";
        $htaccess_content .= "</Files>\n";
        
        file_put_contents($upload_dir . '/.htaccess', $htaccess_content);

        // Create index.php to prevent directory listing
        $index_content = "<?php\n// Silence is golden.\n";
        file_put_contents($upload_dir . '/index.php', $index_content);
    }

    /**
     * Get upload directory path
     *
     * @return string
     */
    private function get_upload_directory() {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/ecopower_tracker_csv';
    }
}

// Initialize the CSV upload functionalities
new EcoPower_Tracker_CSV_Upload();

?>