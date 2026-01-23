<?php
/**
 * WP Wizards Theme Updater
 * 
 * Checks for theme updates from GitHub Releases and displays
 * update notifications in WordPress admin.
 */

if (!class_exists('WPW_Theme_Updater')) {
    class WPW_Theme_Updater {
        
        private $theme_slug;
        private $theme_name;
        private $current_version;
        private $github_repo;
        private $github_username;
        private $github_token;
        private $cache_key;
        private $cache_expiration;
        
        public function __construct() {
            $theme = wp_get_theme();
            $this->theme_slug = $theme->get_stylesheet();
            $this->theme_name = $theme->get('Name');
            $this->current_version = $theme->get('Version');
            
            // GitHub configuration - Hardcoded defaults (can be overridden via options)
            $this->github_username = get_option('wpw_github_username', 'joshchretien'); // Default: WP Wizards GitHub
            $this->github_repo = get_option('wpw_github_repo', 'divi-child-theme'); // Default: WP Wizards repo
            $this->github_token = get_option('wpw_github_token', ''); // Optional: for private repos
            
            // Cache settings
            $this->cache_key = 'wpw_theme_update_' . $this->theme_slug;
            $this->cache_expiration = 12 * HOUR_IN_SECONDS; // Check every 12 hours
            
            // Hook into WordPress update system
            add_filter('pre_set_site_transient_update_themes', array($this, 'check_for_updates'));
            add_filter('themes_api', array($this, 'theme_api_call'), 10, 3);
            add_action('admin_notices', array($this, 'update_notice'));
        }
        
        /**
         * Check for theme updates
         */
        public function check_for_updates($transient) {
            if (empty($transient->checked)) {
                return $transient;
            }
            
            $update_data = $this->get_update_data();
            
            if ($update_data && version_compare($this->current_version, $update_data['version'], '<')) {
                $transient->response[$this->theme_slug] = array(
                    'theme' => $this->theme_slug,
                    'new_version' => $update_data['version'],
                    'url' => $update_data['details_url'],
                    'package' => $update_data['download_url'],
                );
            }
            
            return $transient;
        }
        
        /**
         * Get update data from GitHub Releases or cache
         */
        private function get_update_data() {
            // Check if GitHub is configured (should always be set with defaults)
            if (empty($this->github_username) || empty($this->github_repo)) {
                return false;
            }
            
            // Check cache first
            $cached_data = get_transient($this->cache_key);
            if ($cached_data !== false) {
                return $cached_data;
            }
            
            // Fetch latest release from GitHub
            $api_url = sprintf(
                'https://api.github.com/repos/%s/%s/releases/latest',
                $this->github_username,
                $this->github_repo
            );
            
            $headers = array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress-Theme-Updater',
            );
            
            // Add token if provided (for private repos or higher rate limits)
            if (!empty($this->github_token)) {
                $headers['Authorization'] = 'token ' . $this->github_token;
            }
            
            $response = wp_remote_get($api_url, array(
                'timeout' => 10,
                'headers' => $headers,
            ));
            
            if (is_wp_error($response)) {
                return false;
            }
            
            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code !== 200) {
                return false;
            }
            
            $body = wp_remote_retrieve_body($response);
            $release = json_decode($body, true);
            
            if (!$release || !isset($release['tag_name'])) {
                return false;
            }
            
            // Extract version from tag (remove 'v' prefix if present)
            $latest_version = ltrim($release['tag_name'], 'v');
            
            // Find the theme zip file in assets
            $download_url = '';
            $zip_filename = $this->theme_slug . '.zip'; // e.g., divi-child.zip
            
            if (isset($release['assets']) && is_array($release['assets'])) {
                foreach ($release['assets'] as $asset) {
                    // Look for zip file matching theme name or any zip file
                    if (isset($asset['browser_download_url']) && 
                        (strpos($asset['name'], '.zip') !== false)) {
                        $download_url = $asset['browser_download_url'];
                        // Prefer exact match if available
                        if (strpos($asset['name'], $this->theme_slug) !== false) {
                            break;
                        }
                    }
                }
            }
            
            // If no asset found, use the source code zip from GitHub
            if (empty($download_url)) {
                $download_url = sprintf(
                    'https://github.com/%s/%s/archive/refs/tags/%s.zip',
                    $this->github_username,
                    $this->github_repo,
                    $release['tag_name']
                );
            }
            
            // Format changelog from release body
            $changelog = !empty($release['body']) ? $release['body'] : 'No changelog available.';
            // Convert markdown line breaks to HTML
            $changelog = nl2br(esc_html($changelog));
            
            // Build update data array
            $data = array(
                'version' => $latest_version,
                'download_url' => $download_url,
                'details_url' => $release['html_url'],
                'author' => isset($release['author']['login']) ? $release['author']['login'] : 'WP Wizards',
                'requires' => '5.0',
                'tested' => get_bloginfo('version'),
                'requires_php' => '7.4',
                'description' => $this->theme_name . ' - Custom Divi child theme with WP Wizards tools and features.',
                'changelog' => $changelog,
            );
            
            // Cache the result
            set_transient($this->cache_key, $data, $this->cache_expiration);
            
            return $data;
        }
        
        /**
         * Handle theme API calls
         */
        public function theme_api_call($def, $action, $args) {
            if (isset($args->slug) && $args->slug === $this->theme_slug) {
                $update_data = $this->get_update_data();
                
                if ($update_data) {
                    return (object) array(
                        'name' => $this->theme_name,
                        'slug' => $this->theme_slug,
                        'version' => $update_data['version'],
                        'author' => $update_data['author'],
                        'requires' => $update_data['requires'],
                        'tested' => $update_data['tested'],
                        'requires_php' => $update_data['requires_php'],
                        'download_link' => $update_data['download_url'],
                        'sections' => array(
                            'description' => $update_data['description'],
                            'changelog' => $update_data['changelog'],
                        ),
                    );
                }
            }
            
            return $def;
        }
        
        /**
         * Display update notice in admin
         */
        public function update_notice() {
            $update_data = $this->get_update_data();
            
            if (!$update_data || version_compare($this->current_version, $update_data['version'], '>=')) {
                return;
            }
            
            $update_url = wp_nonce_url(
                admin_url('update.php?action=upgrade-theme&theme=' . urlencode($this->theme_slug)),
                'upgrade-theme_' . $this->theme_slug
            );
            
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php echo esc_html($this->theme_name); ?></strong> has a new version available!
                    <a href="<?php echo esc_url($update_url); ?>" class="button button-primary" style="margin-left: 10px;">
                        Update to version <?php echo esc_html($update_data['version']); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button" style="margin-left: 5px;">
                        View Details
                    </a>
                </p>
            </div>
            <?php
        }
        
        /**
         * Clear update cache (useful for testing)
         */
        public function clear_cache() {
            delete_transient($this->cache_key);
        }
    }
}

// Initialize the updater
if (is_admin()) {
    new WPW_Theme_Updater();
}
