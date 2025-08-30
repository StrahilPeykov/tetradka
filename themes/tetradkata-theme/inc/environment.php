<?php
/**
 * Environment & Deployment Management
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Environment Detection and Management
 */
class Tetradkata_Environment {
    
    private static $instance = null;
    private $environment;
    private $config = array();
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->detect_environment();
        $this->load_config();
        $this->apply_environment_settings();
    }
    
    private function detect_environment() {
        // Method 1: Check wp-config constant (preferred)
        if (defined('WP_ENVIRONMENT_TYPE')) {
            $this->environment = WP_ENVIRONMENT_TYPE;
            return;
        }
        
        // Method 2: Check server name patterns
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $server_name = $_SERVER['SERVER_NAME'] ?? '';
        
        // Local development
        $local_patterns = ['localhost', '127.0.0.1', '.local', '.test', '.dev'];
        foreach ($local_patterns as $pattern) {
            if (strpos($host, $pattern) !== false || strpos($server_name, $pattern) !== false) {
                $this->environment = 'local';
                break;
            }
        }
        
        // Staging environment
        if (!isset($this->environment)) {
            $staging_patterns = ['staging', 'dev.', 'test.', 'preview.'];
            foreach ($staging_patterns as $pattern) {
                if (strpos($host, $pattern) !== false) {
                    $this->environment = 'staging';
                    break;
                }
            }
        }
        
        // Production (default)
        if (!isset($this->environment)) {
            $this->environment = 'production';
        }
        
        // Define constant for other code to use
        if (!defined('WP_ENVIRONMENT_TYPE')) {
            define('WP_ENVIRONMENT_TYPE', $this->environment);
        }
    }
    
    private function load_config() {
        $this->config = array(
            'local' => array(
                'debug' => true,
                'debug_log' => true,
                'debug_display' => true,
                'search_engines' => false,
                'ssl_required' => false,
                'cache_enabled' => false,
                'analytics_enabled' => false,
                'payment_mode' => 'test',
                'email_mode' => 'log',
                'error_reporting' => E_ALL,
                'memory_limit' => '256M',
                'minify_assets' => false,
            ),
            'staging' => array(
                'debug' => true,
                'debug_log' => true,
                'debug_display' => false,
                'search_engines' => false,
                'ssl_required' => true,
                'cache_enabled' => false,
                'analytics_enabled' => false,
                'payment_mode' => 'test',
                'email_mode' => 'test',
                'error_reporting' => E_ALL & ~E_NOTICE,
                'memory_limit' => '256M',
                'minify_assets' => false,
            ),
            'production' => array(
                'debug' => false,
                'debug_log' => false,
                'debug_display' => false,
                'search_engines' => true,
                'ssl_required' => true,
                'cache_enabled' => true,
                'analytics_enabled' => true,
                'payment_mode' => 'live',
                'email_mode' => 'live',
                'error_reporting' => E_ERROR | E_WARNING | E_PARSE,
                'memory_limit' => '256M',
                'minify_assets' => true,
            )
        );
    }
    
    private function apply_environment_settings() {
        // Debug settings
        if (!defined('WP_DEBUG')) {
            define('WP_DEBUG', $this->get('debug'));
        }
        
        if (!defined('WP_DEBUG_LOG')) {
            define('WP_DEBUG_LOG', $this->get('debug_log'));
        }
        
        if (!defined('WP_DEBUG_DISPLAY')) {
            define('WP_DEBUG_DISPLAY', $this->get('debug_display'));
        }
        
        // Error reporting
        error_reporting($this->get('error_reporting'));
        
        // Memory limit
        ini_set('memory_limit', $this->get('memory_limit'));
        
        // SSL enforcement
        if ($this->get('ssl_required') && !is_ssl() && !is_admin() && !wp_doing_ajax()) {
            add_action('template_redirect', array($this, 'force_ssl'));
        }
        
        // Disable search engines in non-production
        if (!$this->get('search_engines')) {
            add_action('init', function() {
                update_option('blog_public', '0');
            });
        }
    }
    
    public function force_ssl() {
        if (!is_ssl()) {
            $redirect_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            wp_redirect($redirect_url, 301);
            exit;
        }
    }
    
    public function get($key) {
        return $this->config[$this->environment][$key] ?? null;
    }
    
    public function is($env) {
        return $this->environment === $env;
    }
    
    public function get_environment() {
        return $this->environment;
    }
    
    /**
     * Get environment-specific database table prefix
     */
    public function get_table_prefix() {
        global $wpdb;
        return $wpdb->prefix . $this->environment . '_';
    }
    
    /**
     * Check if current environment allows certain actions
     */
    public function allows($action) {
        $permissions = array(
            'local' => array('debug_queries', 'file_editing', 'plugin_testing'),
            'staging' => array('debug_queries', 'user_testing'),
            'production' => array('analytics', 'real_payments', 'search_indexing')
        );
        
        return in_array($action, $permissions[$this->environment] ?? array());
    }
}

/**
 * Deployment Status and Health Monitoring
 */
class Tetradkata_Deployment {
    
    public static function mark_deployed($version = null) {
        $version = $version ?: (defined('TETRADKATA_VERSION') ? TETRADKATA_VERSION : '1.0.0');
        $env = Tetradkata_Environment::getInstance();
        
        update_option('tetradkata_deployment_info', array(
            'version' => $version,
            'deployed_at' => current_time('mysql'),
            'environment' => $env->get_environment(),
            'php_version' => PHP_VERSION,
            'wp_version' => get_bloginfo('version'),
            'theme_version' => wp_get_theme()->get('Version'),
            'server_info' => array(
                'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'host' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
                'ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown'
            )
        ));
        
        // Log deployment
        if ($env->get('debug_log')) {
            error_log(sprintf(
                'Tetradkata Theme v%s deployed to %s environment at %s',
                $version,
                $env->get_environment(),
                current_time('mysql')
            ));
        }
        
        // Clear any existing caches
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear opcache if available
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
    
    public static function get_deployment_info() {
        return get_option('tetradkata_deployment_info', array());
    }
    
    public static function check_deployment_health() {
        $issues = array();
        $env = Tetradkata_Environment::getInstance();
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $issues[] = "PHP version too old: " . PHP_VERSION . " (minimum 7.4 required)";
        }
        
        // Check WordPress version
        if (version_compare(get_bloginfo('version'), '5.0', '<')) {
            $issues[] = "WordPress version too old: " . get_bloginfo('version');
        }
        
        // Check required plugins
        $required_plugins = array('woocommerce/woocommerce.php');
        foreach ($required_plugins as $plugin) {
            if (!is_plugin_active($plugin)) {
                $issues[] = "Required plugin not active: " . basename(dirname($plugin));
            }
        }
        
        // Check required pages
        $required_pages = array('privacy-policy', 'terms', 'delivery', 'returns');
        foreach ($required_pages as $page_slug) {
            if (!get_page_by_path($page_slug)) {
                $issues[] = "Required page missing: {$page_slug}";
            }
        }
        
        // Check menu setup
        $menu_locations = get_theme_mod('nav_menu_locations');
        if (empty($menu_locations['primary'])) {
            $issues[] = "Primary navigation menu not assigned";
        }
        
        // Check WooCommerce setup
        if (class_exists('WooCommerce')) {
            if (!wc_get_page_id('shop')) {
                $issues[] = "WooCommerce shop page not set";
            }
            
            if (!get_option('woocommerce_store_address')) {
                $issues[] = "WooCommerce store address not configured";
            }
            
            // Check payment methods
            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
            if (empty($available_gateways)) {
                $issues[] = "No payment methods configured";
            }
            
            // Check shipping methods
            $shipping_zones = WC_Shipping_Zones::get_zones();
            if (empty($shipping_zones)) {
                $issues[] = "No shipping methods configured";
            }
        }
        
        // Environment-specific checks
        if ($env->is('production')) {
            // Check SSL
            if (!is_ssl()) {
                $issues[] = "SSL not configured for production";
            }
            
            // Check search engine visibility
            if (!get_option('blog_public')) {
                $issues[] = "Search engines are blocked in production";
            }
            
            // Check debug mode
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $issues[] = "Debug mode is enabled in production";
            }
        }
        
        // Check file permissions
        if (!is_writable(wp_upload_dir()['basedir'])) {
            $issues[] = "Uploads directory is not writable";
        }
        
        // Check memory limit
        $memory_limit = ini_get('memory_limit');
        $memory_in_bytes = wp_convert_hr_to_bytes($memory_limit);
        if ($memory_in_bytes < 134217728) { // 128MB
            $issues[] = "Memory limit too low: {$memory_limit} (minimum 128M recommended)";
        }
        
        return $issues;
    }
    
    /**
     * Generate deployment report
     */
    public static function get_deployment_report() {
        $info = self::get_deployment_info();
        $issues = self::check_deployment_health();
        $env = Tetradkata_Environment::getInstance();
        
        return array(
            'status' => empty($issues) ? 'healthy' : 'issues_detected',
            'environment' => $env->get_environment(),
            'deployment_info' => $info,
            'health_issues' => $issues,
            'system_info' => array(
                'php_version' => PHP_VERSION,
                'wp_version' => get_bloginfo('version'),
                'theme_version' => wp_get_theme()->get('Version'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'is_ssl' => is_ssl(),
                'is_multisite' => is_multisite(),
            ),
            'report_generated' => current_time('mysql')
        );
    }
}

/**
 * Admin Dashboard Integration
 */
function tetradkata_deployment_dashboard_widget() {
    $env = Tetradkata_Environment::getInstance();
    $deployment_info = Tetradkata_Deployment::get_deployment_info();
    $health_issues = Tetradkata_Deployment::check_deployment_health();
    
    echo '<div class="tetradkata-deployment-info">';
    
    // Environment badge
    $env_colors = array(
        'local' => '#00a0d2',
        'staging' => '#ffb900', 
        'production' => '#46b450'
    );
    $env_color = $env_colors[$env->get_environment()] ?? '#666';
    
    echo '<h3>Environment: <span style="background: ' . $env_color . '; color: white; padding: 2px 8px; border-radius: 3px; font-size: 12px;">' . 
         strtoupper($env->get_environment()) . '</span></h3>';
    
    // Deployment info
    if ($deployment_info) {
        echo '<div style="margin: 15px 0;">';
        echo '<p><strong>Version:</strong> ' . esc_html($deployment_info['version'] ?? 'Unknown') . '</p>';
        echo '<p><strong>Deployed:</strong> ' . esc_html($deployment_info['deployed_at'] ?? 'Unknown') . '</p>';
        echo '<p><strong>Theme:</strong> ' . esc_html($deployment_info['theme_version'] ?? 'Unknown') . '</p>';
        echo '</div>';
    }
    
    // Health status
    if (!empty($health_issues)) {
        echo '<div style="background: #ffebe8; padding: 12px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #dc3232;">';
        echo '<h4 style="color: #dc3232; margin: 0 0 10px 0;">⚠️ Issues Detected (' . count($health_issues) . '):</h4>';
        echo '<ul style="margin: 0; font-size: 13px;">';
        foreach (array_slice($health_issues, 0, 5) as $issue) {
            echo '<li style="color: #dc3232; margin-bottom: 3px;">' . esc_html($issue) . '</li>';
        }
        if (count($health_issues) > 5) {
            echo '<li style="color: #666;">... and ' . (count($health_issues) - 5) . ' more</li>';
        }
        echo '</ul></div>';
        
        echo '<p><a href="' . admin_url('admin.php?page=tetradkata-deployment') . '" class="button">View Full Report</a></p>';
    } else {
        echo '<div style="background: #d1edff; padding: 12px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #00a0d2;">';
        echo '<p style="color: #0073aa; margin: 0;">✅ All systems operational</p>';
        echo '</div>';
    }
    
    // Quick actions
    echo '<div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #ddd;">';
    echo '<p><strong>Quick Actions:</strong></p>';
    echo '<a href="' . admin_url('customize.php') . '" class="button button-small">Customize Theme</a> ';
    if (class_exists('WooCommerce')) {
        echo '<a href="' . admin_url('admin.php?page=wc-settings') . '" class="button button-small">WooCommerce Settings</a>';
    }
    echo '</div>';
    
    echo '</div>';
}

function tetradkata_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'tetradkata_deployment',
        'Tetradkata Environment Status',
        'tetradkata_deployment_dashboard_widget'
    );
}
add_action('wp_dashboard_setup', 'tetradkata_add_dashboard_widget');

/**
 * Add admin menu for detailed deployment report
 */
function tetradkata_add_deployment_admin_menu() {
    add_theme_page(
        'Deployment Status',
        'Environment Status',
        'manage_options',
        'tetradkata-deployment',
        'tetradkata_deployment_admin_page'
    );
}
add_action('admin_menu', 'tetradkata_add_deployment_admin_menu');

function tetradkata_deployment_admin_page() {
    $report = Tetradkata_Deployment::get_deployment_report();
    ?>
    <div class="wrap">
        <h1>Tetradkata Environment Status</h1>
        
        <div class="notice notice-info">
            <p><strong>Environment:</strong> <?php echo esc_html(strtoupper($report['environment'])); ?></p>
        </div>
        
        <?php if ($report['status'] === 'issues_detected'): ?>
            <div class="notice notice-warning">
                <h3>Issues Detected (<?php echo count($report['health_issues']); ?>)</h3>
                <ul>
                    <?php foreach ($report['health_issues'] as $issue): ?>
                        <li><?php echo esc_html($issue); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="notice notice-success">
                <p>✅ All systems operational</p>
            </div>
        <?php endif; ?>
        
        <div class="postbox">
            <h3 class="hndle">System Information</h3>
            <div class="inside">
                <table class="form-table">
                    <?php foreach ($report['system_info'] as $key => $value): ?>
                        <tr>
                            <th><?php echo esc_html(ucwords(str_replace('_', ' ', $key))); ?></th>
                            <td><?php echo esc_html($value ? (is_bool($value) ? ($value ? 'Yes' : 'No') : $value) : 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        
        <?php if ($report['deployment_info']): ?>
            <div class="postbox">
                <h3 class="hndle">Deployment Information</h3>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th>Version</th>
                            <td><?php echo esc_html($report['deployment_info']['version']); ?></td>
                        </tr>
                        <tr>
                            <th>Deployed At</th>
                            <td><?php echo esc_html($report['deployment_info']['deployed_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Server</th>
                            <td><?php echo esc_html($report['deployment_info']['server_info']['server'] ?? 'Unknown'); ?></td>
                        </tr>
                        <tr>
                            <th>Host</th>
                            <td><?php echo esc_html($report['deployment_info']['server_info']['host'] ?? 'Unknown'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <p>
            <a href="<?php echo admin_url('admin.php?page=tetradkata-deployment&action=refresh'); ?>" class="button button-primary">Refresh Status</a>
            <a href="<?php echo admin_url('admin.php?page=tetradkata-deployment&action=download-report'); ?>" class="button">Download Report</a>
        </p>
    </div>
    <?php
    
    // Handle actions
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'refresh') {
            // Force health check
            delete_transient('tetradkata_health_check_cache');
            echo '<div class="notice notice-success"><p>Status refreshed!</p></div>';
        } elseif ($_GET['action'] === 'download-report') {
            // Download report as JSON
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="tetradkata-report-' . date('Y-m-d-H-i-s') . '.json"');
            echo json_encode($report, JSON_PRETTY_PRINT);
            exit;
        }
    }
}

/**
 * WP-CLI Integration
 */
if (defined('WP_CLI') && WP_CLI) {
    class Tetradkata_CLI_Deploy extends WP_CLI_Command {
        
        /**
         * Deploy the theme with environment setup
         *
         * ## OPTIONS
         *
         * [<environment>]
         * : The environment (local, staging, production)
         *
         * [--force]
         * : Force deployment even if issues exist
         *
         * ## EXAMPLES
         *
         *     wp tetradkata deploy production
         *     wp tetradkata deploy staging --force
         */
        public function deploy($args, $assoc_args) {
            $environment = $args[0] ?? 'staging';
            $force = isset($assoc_args['force']);
            
            WP_CLI::line("Deploying Tetradkata theme to {$environment} environment...");
            
            // Set environment if not already defined
            if (!defined('WP_ENVIRONMENT_TYPE')) {
                define('WP_ENVIRONMENT_TYPE', $environment);
            }
            
            // Run activation hooks
            do_action('after_switch_theme');
            
            // Check health
            $issues = Tetradkata_Deployment::check_deployment_health();
            
            if (!empty($issues) && !$force) {
                WP_CLI::error("Deployment failed due to issues. Use --force to override:");
                foreach ($issues as $issue) {
                    WP_CLI::line("  - {$issue}");
                }
                return;
            }
            
            if (empty($issues)) {
                WP_CLI::success("Deployment completed successfully!");
            } else {
                WP_CLI::warning("Deployment completed with issues (forced):");
                foreach ($issues as $issue) {
                    WP_CLI::line("  - {$issue}");
                }
            }
            
            // Show next steps
            WP_CLI::line("\nNext steps:");
            WP_CLI::line("1. Upload product images and create products");
            WP_CLI::line("2. Configure payment gateway credentials");
            WP_CLI::line("3. Set up shipping rates for your location");
            WP_CLI::line("4. Test all functionality");
        }
        
        /**
         * Check deployment health
         */
        public function health($args, $assoc_args) {
            $issues = Tetradkata_Deployment::check_deployment_health();
            
            if (empty($issues)) {
                WP_CLI::success("All systems operational!");
            } else {
                WP_CLI::error("Issues detected:");
                foreach ($issues as $issue) {
                    WP_CLI::line("  - {$issue}");
                }
            }
            
            // Show system info
            WP_CLI::line("\nSystem Information:");
            $info = Tetradkata_Deployment::get_deployment_report()['system_info'];
            foreach ($info as $key => $value) {
                WP_CLI::line("  {$key}: " . (is_bool($value) ? ($value ? 'Yes' : 'No') : $value));
            }
        }
        
        /**
         * Show environment configuration
         */
        public function env($args, $assoc_args) {
            $env = Tetradkata_Environment::getInstance();
            
            WP_CLI::line("Current Environment: " . strtoupper($env->get_environment()));
            WP_CLI::line("\nConfiguration:");
            
            $config_keys = ['debug', 'debug_log', 'search_engines', 'ssl_required', 'cache_enabled', 'analytics_enabled', 'payment_mode', 'email_mode'];
            
            foreach ($config_keys as $key) {
                $value = $env->get($key);
                $display_value = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
                WP_CLI::line("  {$key}: {$display_value}");
            }
        }
    }
    
    WP_CLI::add_command('tetradkata', 'Tetradkata_CLI_Deploy');
}

// Initialize environment management
Tetradkata_Environment::getInstance();
?>