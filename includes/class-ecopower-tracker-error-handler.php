<?php
/**
 * Centralized Error Handling functionality
 *
 * @package EcoPowerTracker
 * @since 2.0.2
 */

namespace EcoPowerTracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class EcoPower_Tracker_Error_Handler
 * 
 * Provides centralized error handling, logging, and user-friendly error reporting
 */
class EcoPower_Tracker_Error_Handler {

    /**
     * Error types
     */
    const ERROR_TYPE_VALIDATION = 'validation';
    const ERROR_TYPE_FILE = 'file';
    const ERROR_TYPE_DATABASE = 'database';
    const ERROR_TYPE_SECURITY = 'security';
    const ERROR_TYPE_SYSTEM = 'system';

    /**
     * Error severity levels
     */
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Maximum number of errors to store per session
     */
    const MAX_ERRORS_PER_SESSION = 100;

    /**
     * Store errors for current session
     *
     * @var array
     */
    private static $session_errors = array();

    /**
     * Log an error with context
     *
     * @param string $message Error message
     * @param string $type Error type
     * @param string $severity Error severity
     * @param array $context Additional context data
     * @param int|null $line_number Line number where error occurred
     * @return void
     */
    public static function log_error($message, $type = self::ERROR_TYPE_SYSTEM, $severity = self::SEVERITY_MEDIUM, $context = array(), $line_number = null) {
        $error_data = array(
            'timestamp' => current_time('mysql'),
            'message' => sanitize_text_field($message),
            'type' => sanitize_text_field($type),
            'severity' => sanitize_text_field($severity),
            'context' => $context,
            'line_number' => $line_number,
            'user_id' => get_current_user_id(),
            'ip_address' => self::get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
            'request_uri' => isset($_SERVER['REQUEST_URI']) ? sanitize_text_field($_SERVER['REQUEST_URI']) : ''
        );

        // Store in session for user feedback
        self::add_session_error($error_data);

        // Log to WordPress error log
        $log_message = sprintf(
            '[EcoPower Tracker] %s: %s | Type: %s | Severity: %s | User: %d | Context: %s',
            $error_data['timestamp'],
            $message,
            $type,
            $severity,
            $error_data['user_id'],
            wp_json_encode($context)
        );

        if ($line_number) {
            $log_message .= ' | Line: ' . $line_number;
        }

        error_log($log_message);

        // Store critical errors in database
        if ($severity === self::SEVERITY_CRITICAL) {
            self::store_critical_error($error_data);
        }
    }

    /**
     * Add error to session storage
     *
     * @param array $error_data Error data
     * @return void
     */
    private static function add_session_error($error_data) {
        // Limit session errors to prevent memory issues
        if (count(self::$session_errors) >= self::MAX_ERRORS_PER_SESSION) {
            array_shift(self::$session_errors);
        }

        self::$session_errors[] = $error_data;
    }

    /**
     * Get all session errors
     *
     * @param string|null $type Filter by error type
     * @return array Session errors
     */
    public static function get_session_errors($type = null) {
        if ($type) {
            return array_filter(self::$session_errors, function($error) use ($type) {
                return $error['type'] === $type;
            });
        }

        return self::$session_errors;
    }

    /**
     * Clear session errors
     *
     * @param string|null $type Clear specific error type or all
     * @return void
     */
    public static function clear_session_errors($type = null) {
        if ($type) {
            self::$session_errors = array_filter(self::$session_errors, function($error) use ($type) {
                return $error['type'] !== $type;
            });
        } else {
            self::$session_errors = array();
        }
    }

    /**
     * Create user-friendly error message
     *
     * @param string $technical_message Technical error message
     * @param string $user_message User-friendly message
     * @param array $suggestions Suggested actions for user
     * @return string Formatted error message
     */
    public static function format_user_error($technical_message, $user_message = null, $suggestions = array()) {
        $formatted_message = $user_message ?: $technical_message;

        if (!empty($suggestions)) {
            $formatted_message .= "\n\n" . __('Suggestions:', 'ecopower-tracker') . "\n";
            foreach ($suggestions as $suggestion) {
                $formatted_message .= "• " . $suggestion . "\n";
            }
        }

        return $formatted_message;
    }

    /**
     * Validate file upload security
     *
     * @param array $file Uploaded file data
     * @return array Validation result with success status and errors
     */
    public static function validate_file_security($file) {
        $errors = array();

        // Check for file upload errors
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = self::get_upload_error_message($file['error']);
        }

        // Check file size (default 5MB limit)
        $max_size = apply_filters('ecopower_tracker_max_file_size', 5 * 1024 * 1024);
        if (isset($file['size']) && $file['size'] > $max_size) {
            $errors[] = sprintf(
                /* translators: 1: Actual file size, 2: Maximum allowed file size */
                __('File size (%1$s) exceeds maximum allowed size (%2$s)', 'ecopower-tracker'),
                size_format($file['size']),
                size_format($max_size)
            );
        }

        // Check for suspicious file names
        if (isset($file['name'])) {
            $filename = sanitize_file_name($file['name']);
            if ($filename !== $file['name']) {
                $errors[] = __('File name contains invalid characters', 'ecopower-tracker');
            }

            // Check for executable file extensions
            $dangerous_extensions = array('php', 'js', 'html', 'htm', 'exe', 'bat', 'cmd', 'scr', 'pif');
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($file_extension, $dangerous_extensions)) {
                $errors[] = __('File type not allowed for security reasons', 'ecopower-tracker');
            }
        }

        // Check file content for malicious patterns
        if (isset($file['tmp_name']) && file_exists($file['tmp_name'])) {
            $file_content = file_get_contents($file['tmp_name'], false, null, 0, 1024); // Read first 1KB
            if ($file_content !== false) {
                $malicious_patterns = array(
                    '/<\?php/i',
                    '/<script/i',
                    '/javascript:/i',
                    '/vbscript:/i',
                    '/onload=/i',
                    '/onerror=/i'
                );

                foreach ($malicious_patterns as $pattern) {
                    if (preg_match($pattern, $file_content)) {
                        $errors[] = __('File content contains potentially malicious code', 'ecopower-tracker');
                        break;
                    }
                }
            }
        }

        return array(
            'success' => empty($errors),
            'errors' => $errors
        );
    }

    /**
     * Validate CSV data field
     *
     * @param mixed $value Field value
     * @param string $field_name Field name
     * @param string $data_type Expected data type
     * @param array $constraints Field constraints
     * @return array Validation result
     */
    public static function validate_field($value, $field_name, $data_type, $constraints = array()) {
        $errors = array();
        $sanitized_value = $value;

        // Check required fields
        if (isset($constraints['required']) && $constraints['required'] && empty($value)) {
            $errors[] = sprintf(
                /* translators: %s: Name of the required field */
                __('Field "%s" is required', 'ecopower-tracker'),
                $field_name
            );
            return array('success' => false, 'errors' => $errors, 'value' => $sanitized_value);
        }

        // Skip validation for empty non-required fields
        if (empty($value) && (!isset($constraints['required']) || !$constraints['required'])) {
            return array('success' => true, 'errors' => array(), 'value' => $sanitized_value);
        }

        // Validate by data type
        switch ($data_type) {
            case 'string':
                $sanitized_value = sanitize_text_field($value);
                
                if (isset($constraints['max_length']) && strlen($sanitized_value) > $constraints['max_length']) {
                    $errors[] = sprintf(
                        /* translators: 1: Field name, 2: Maximum allowed length */
                        __('Field "%1$s" exceeds maximum length of %2$d characters', 'ecopower-tracker'),
                        $field_name,
                        $constraints['max_length']
                    );
                }

                if (isset($constraints['min_length']) && strlen($sanitized_value) < $constraints['min_length']) {
                    $errors[] = sprintf(
                        /* translators: 1: Field name, 2: Minimum required length */
                        __('Field "%1$s" must be at least %2$d characters long', 'ecopower-tracker'),
                        $field_name,
                        $constraints['min_length']
                    );
                }

                if (isset($constraints['pattern']) && !preg_match($constraints['pattern'], $sanitized_value)) {
                    $errors[] = sprintf(
                        /* translators: %s: Name of the field with invalid format */
                        __('Field "%s" has invalid format', 'ecopower-tracker'),
                        $field_name
                    );
                }
                break;

            case 'float':
            case 'decimal':
                if (!is_numeric($value)) {
                    $errors[] = sprintf(
                        /* translators: %s: Name of the field that must be numeric */
                        __('Field "%s" must be a valid number', 'ecopower-tracker'),
                        $field_name
                    );
                } else {
                    $sanitized_value = floatval($value);
                    
                    if (isset($constraints['min']) && $sanitized_value < $constraints['min']) {
                        $errors[] = sprintf(
                            /* translators: 1: Field name, 2: Minimum allowed value */
                            __('Field "%1$s" must be at least %2$s', 'ecopower-tracker'),
                            $field_name,
                            $constraints['min']
                        );
                    }

                    if (isset($constraints['max']) && $sanitized_value > $constraints['max']) {
                        $errors[] = sprintf(
                            /* translators: 1: Field name, 2: Maximum allowed value */
                            __('Field "%1$s" must not exceed %2$s', 'ecopower-tracker'),
                            $field_name,
                            $constraints['max']
                        );
                    }
                }
                break;

            case 'integer':
                if (!is_numeric($value) || floatval($value) != intval($value)) {
                    $errors[] = sprintf(
                        /* translators: %s: Name of the field that must be an integer */
                        __('Field "%s" must be a valid integer', 'ecopower-tracker'),
                        $field_name
                    );
                } else {
                    $sanitized_value = intval($value);
                    
                    if (isset($constraints['min']) && $sanitized_value < $constraints['min']) {
                        $errors[] = sprintf(
                            /* translators: 1: Field name, 2: Minimum allowed integer value */
                            __('Field "%1$s" must be at least %2$d', 'ecopower-tracker'),
                            $field_name,
                            $constraints['min']
                        );
                    }

                    if (isset($constraints['max']) && $sanitized_value > $constraints['max']) {
                        $errors[] = sprintf(
                            /* translators: 1: Field name, 2: Maximum allowed integer value */
                            __('Field "%1$s" must not exceed %2$d', 'ecopower-tracker'),
                            $field_name,
                            $constraints['max']
                        );
                    }
                }
                break;

            case 'date':
                $date = date_create($value);
                if ($date === false) {
                    $errors[] = sprintf(
                        /* translators: %s: Name of the field that must be a valid date */
                        __('Field "%s" must be a valid date', 'ecopower-tracker'),
                        $field_name
                    );
                } else {
                    $sanitized_value = date_format($date, 'Y-m-d');
                    
                    if (isset($constraints['min_date'])) {
                        $min_date = date_create($constraints['min_date']);
                        if ($date < $min_date) {
                            $errors[] = sprintf(
                                /* translators: 1: Field name, 2: Minimum date value */
                                __('Field "%1$s" must be after %2$s', 'ecopower-tracker'),
                                $field_name,
                                $constraints['min_date']
                            );
                        }
                    }

                    if (isset($constraints['max_date'])) {
                        $max_date = date_create($constraints['max_date']);
                        if ($date > $max_date) {
                            $errors[] = sprintf(
                                /* translators: 1: Field name, 2: Maximum date value */
                                __('Field "%1$s" must be before %2$s', 'ecopower-tracker'),
                                $field_name,
                                $constraints['max_date']
                            );
                        }
                    }
                }
                break;

            case 'enum':
                if (isset($constraints['allowed_values']) && !in_array($value, $constraints['allowed_values'])) {
                    $errors[] = sprintf(
                        /* translators: 1: Field name, 2: List of allowed values */
                        __('Field "%1$s" must be one of: %2$s', 'ecopower-tracker'),
                        $field_name,
                        implode(', ', $constraints['allowed_values'])
                    );
                }
                $sanitized_value = sanitize_text_field($value);
                break;
        }

        return array(
            'success' => empty($errors),
            'errors' => $errors,
            'value' => $sanitized_value
        );
    }

    /**
     * Store critical error in database
     *
     * @param array $error_data Error data
     * @return void
     */
    private static function store_critical_error($error_data) {
        global $wpdb;

        // Create error log table if it doesn't exist
        $table_name = $wpdb->prefix . 'ecopower_tracker_error_log';
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            timestamp datetime NOT NULL,
            message text NOT NULL,
            type varchar(50) NOT NULL,
            severity varchar(20) NOT NULL,
            context text,
            line_number int(11),
            user_id int(11),
            ip_address varchar(45),
            user_agent text,
            request_uri text,
            PRIMARY KEY (id),
            KEY timestamp (timestamp),
            KEY severity (severity),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Insert error record
        $wpdb->insert(
            $table_name,
            array(
                'timestamp' => $error_data['timestamp'],
                'message' => $error_data['message'],
                'type' => $error_data['type'],
                'severity' => $error_data['severity'],
                'context' => wp_json_encode($error_data['context']),
                'line_number' => $error_data['line_number'],
                'user_id' => $error_data['user_id'],
                'ip_address' => $error_data['ip_address'],
                'user_agent' => $error_data['user_agent'],
                'request_uri' => $error_data['request_uri']
            ),
            array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s')
        );
    }

    /**
     * Get client IP address
     *
     * @return string Client IP address
     */
    private static function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );

        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = sanitize_text_field($_SERVER[$key]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
    }

    /**
     * Get upload error message
     *
     * @param int $error_code PHP upload error code
     * @return string Error message
     */
    private static function get_upload_error_message($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return __('The uploaded file exceeds the maximum file size allowed by the server', 'ecopower-tracker');
            case UPLOAD_ERR_FORM_SIZE:
                return __('The uploaded file exceeds the maximum file size allowed by the form', 'ecopower-tracker');
            case UPLOAD_ERR_PARTIAL:
                return __('The uploaded file was only partially uploaded', 'ecopower-tracker');
            case UPLOAD_ERR_NO_FILE:
                return __('No file was uploaded', 'ecopower-tracker');
            case UPLOAD_ERR_NO_TMP_DIR:
                return __('Missing temporary folder for file upload', 'ecopower-tracker');
            case UPLOAD_ERR_CANT_WRITE:
                return __('Failed to write file to disk', 'ecopower-tracker');
            case UPLOAD_ERR_EXTENSION:
                return __('File upload stopped by extension', 'ecopower-tracker');
            default:
                return __('Unknown upload error', 'ecopower-tracker');
        }
    }

    /**
     * Generate error summary for display
     *
     * @param array $errors Array of errors
     * @return string HTML formatted error summary
     */
    public static function generate_error_summary($errors) {
        if (empty($errors)) {
            return '';
        }

        $html = '<div class="ecopower-tracker-error-summary">';
        $html .= '<h4>' . __('The following errors occurred:', 'ecopower-tracker') . '</h4>';
        $html .= '<ul>';

        foreach ($errors as $error) {
            $html .= '<li>';
            if (isset($error['line_number']) && $error['line_number']) {
                $html .= sprintf(
                    /* translators: %d: Line number in CSV file */
                    __('Line %d: ', 'ecopower-tracker'),
                    $error['line_number']
                );
            }
            $html .= esc_html($error['message']);
            $html .= '</li>';
        }

        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Check system resources before processing
     *
     * @return array System status
     */
    public static function check_system_resources() {
        $status = array(
            'memory_ok' => true,
            'time_ok' => true,
            'disk_ok' => true,
            'warnings' => array()
        );

        // Check memory limit
        $memory_limit = ini_get('memory_limit');
        $memory_limit_bytes = self::convert_to_bytes($memory_limit);
        $memory_usage = memory_get_usage(true);
        
        if ($memory_limit_bytes > 0 && $memory_usage > ($memory_limit_bytes * 0.8)) {
            $status['memory_ok'] = false;
            $status['warnings'][] = __('Memory usage is high. Consider increasing PHP memory limit.', 'ecopower-tracker');
        }

        // Check execution time
        $max_execution_time = ini_get('max_execution_time');
        if ($max_execution_time > 0 && $max_execution_time < 300) {
            $status['time_ok'] = false;
            $status['warnings'][] = __('PHP execution time limit may be too low for large imports.', 'ecopower-tracker');
        }

        // Check disk space
        $upload_dir = wp_upload_dir();
        if (function_exists('disk_free_space')) {
            $free_space = disk_free_space($upload_dir['basedir']);
            if ($free_space !== false && $free_space < (100 * 1024 * 1024)) { // Less than 100MB
                $status['disk_ok'] = false;
                $status['warnings'][] = __('Low disk space available for file uploads.', 'ecopower-tracker');
            }
        }

        return $status;
    }

    /**
     * Convert PHP memory limit to bytes
     *
     * @param string $val Memory limit value
     * @return int Memory limit in bytes
     */
    private static function convert_to_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = intval($val);
        
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }
}

