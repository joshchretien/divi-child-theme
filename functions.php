<?php
/* --------------------------------------------------
   WP Wizards – Premium Dashboard Widget
   Includes:
   - Update Plugins button
   - Logged-in user/device/IP info
   - Custom WP Footer Branding
   - Forced widget to top
-------------------------------------------------- */


add_action('wp_dashboard_setup', 'wpwizards_dashboard_widget');

function wpwizards_dashboard_widget() {

    wp_add_dashboard_widget(
        'wpwizards_dashboard_widget',
        'Website Support',
        'wpwizards_dashboard_widget_content'
    );

    // Force widget to top
    global $wp_meta_boxes;
    $normal = $wp_meta_boxes['dashboard']['normal']['core'];
    $widget = ['wpwizards_dashboard_widget' => $normal['wpwizards_dashboard_widget']];
    $wp_meta_boxes['dashboard']['normal']['core'] = $widget + $normal;
}

function wpwizards_dashboard_widget_content() {

    $logo = 'https://www.wpwizards.com/wp-content/uploads/2025/11/FC-Standard-scaled.png';

    // User data
    $current_user = wp_get_current_user();
    $username = $current_user->display_name;

    // Detect Browser + OS
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $os = 'Unknown OS';
    $browser = 'Unknown Browser';

    if (preg_match('/Windows/i', $ua)) $os = 'Windows';
    elseif (preg_match('/Macintosh|Mac OS X/i', $ua)) $os = 'macOS';
    elseif (preg_match('/Linux/i', $ua)) $os = 'Linux';
    elseif (preg_match('/iPhone/i', $ua)) $os = 'iPhone';
    elseif (preg_match('/Android/i', $ua)) $os = 'Android';

    if (preg_match('/Chrome/i', $ua)) $browser = 'Chrome';
    elseif (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) $browser = 'Safari';
    elseif (preg_match('/Firefox/i', $ua)) $browser = 'Firefox';
    elseif (preg_match('/Edge/i', $ua)) $browser = 'Edge';

    // IP Address
    $ip = $_SERVER['REMOTE_ADDR'];
    ?>

    <div style="padding: 20px 25px; background:#ffffff; border-radius:10px;">

        <!-- LOGO -->
        <div style="text-align:center; margin-bottom:25px;">
            <img src="<?php echo esc_url($logo); ?>" 
                 alt="WP Wizards Logo"
                 style="max-width:250px; height:auto; margin-bottom:12px;" />
        </div>

        <!-- SUPPORT MESSAGE -->
        <p style="font-size:16px; text-align:center; margin:0 0 15px;">
            Your website is actively maintained for performance, security, and reliability.
        </p>

        <p style="font-size:14px; text-align:center; color:#555; margin:0 0 25px;">
            Need updates, enhancements, bug fixes, or custom development?<br>
            We're here to help.
        </p>

        <!-- BUTTONS -->
        <div style="text-align:center; margin-bottom:25px;">

            <!-- Analytics Dashboard -->
            <a href="https://dashboard.wpwizards.com/"
               target="_blank"
               class="button button-primary button-large"
               style="margin:6px 10px; display:inline-flex; align-items:center;">
                <span class="dashicons dashicons-chart-area" style="margin-right:6px;"></span>
                Analytics Dashboard
            </a>

            <!-- Book a Call -->
            <a href="https://calendly.com/wpwizards"
               target="_blank"
               class="button button-primary button-large"
               style="margin:6px 10px; display:inline-flex; align-items:center;">
                <span class="dashicons dashicons-calendar-alt" style="margin-right:6px;"></span>
                Book a Call
            </a>

            <!-- Open a Ticket -->
            <a href="https://pricelessconsultingllc.atlassian.net/servicedesk/customer/portal/1/group/1/create/17"
               target="_blank"
               class="button button-primary button-large"
               style="margin:6px 10px; display:inline-flex; align-items:center;">
                <span class="dashicons dashicons-sos" style="margin-right:6px;"></span>
                Open a Ticket
            </a>

            <!-- Update Plugins -->
            <a href="<?php echo admin_url('plugins.php?plugin_status=upgrade'); ?>"
               class="button button-secondary button-large"
               style="margin:6px 10px; display:inline-flex; align-items:center;">
                <span class="dashicons dashicons-update" style="margin-right:6px;"></span>
                Update Plugins
            </a>

        </div>

        <!-- USER / DEVICE INFO -->
        <p style="text-align:center; font-size:13px; color:#666; margin-top:10px;">
            Logged in as <strong><?php echo esc_html($username); ?></strong><br>
            <span style="font-size:12px; color:#888;">
                <?php echo $os; ?> • <?php echo $browser; ?> • <?php echo $ip; ?>
            </span>
        </p>

        <!-- FOOTER -->
        <p style="text-align:center; font-size:12px; color:#999; margin-top:20px;">
            Child Theme Version 1.2.1
        </p>

    </div>

    <?php
}

/* --------------------------------------------------
   CUSTOM WP ADMIN FOOTER BRANDING
-------------------------------------------------- */
add_filter('admin_footer_text', 'wpwizards_footer_branding');

function wpwizards_footer_branding() {
    echo "Thank you for choosing <a href='https://www.wpwizards.com' target='_blank'>WP Wizards</a>.";
}

/** Disable Gutenberg **/
add_filter('use_block_editor_for_post', '__return_false', 10);


///////////// Theme Updater

require_once dirname( __FILE__ ) . '/class-theme-updater.php';

///////////// Install Plugins

require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', 'my_theme_register_required_plugins' );
function my_theme_register_required_plugins() {
        /** Array of repository plugins  **/
  $plugins = array(
    array( 
      'name'     => 'WooCommerce', 
      'slug'     => 'woocommerce', 
      'required' => false,
    ),
    array( 
      'name'     => 'WP Activity Log', 
      'slug'     => 'wp-security-audit-log', 
      'required' => true,
    ),
    array( 
      'name'     => 'Rank Math SEO – AI SEO Tools to Dominate SEO Rankings', 
      'slug'     => 'seo-by-rank-math', 
      'required' => true,
    ),
    array( 
      'name'     => 'Surfer – WordPress Plugin', 
      'slug'     => 'surferseo', 
      'required' => false,
    ),
    array( 
        'name'     => 'WP AutoTerms: Privacy Policy Generator (GDPR & CCPA), Terms & Conditions Generator, Cookie Notice Banner', 
        'slug'     => 'auto-terms-of-service-and-privacy-policy', 
        'required' => false,
      ),
      array( 
        'name'     => 'WPCode – Insert Headers and Footers + Custom Code Snippets – WordPress Code Manager', 
        'slug'     => 'insert-headers-and-footers', 
        'required' => false,
      ),
      array( 
        'name'     => 'Newsletters, Email Marketing, SMS and Popups by Omnisend', 
        'slug'     => 'omnisend', 
        'required' => false,
      ),
      array( 
        'name'     => 'Omnisend for Gravity Forms Add-On', 
        'slug'     => 'omnisend-for-gravity-forms-add-on', 
        'required' => false,
      ),
      array( 
        'name'     => 'Instant Indexing for Google', 
        'slug'     => 'fast-indexing-api', 
        'required' => true,
      ),
      array( 
        'name'     => 'Instant Images – One Click Image Uploads from Unsplash, Openverse, Pixabay and Pexels', 
        'slug'     => 'instant-images', 
        'required' => true,
      ),
      array( 
        'name'     => 'Imagify – Optimize Images & Convert WebP & AVIF | Compress Images Easily', 
        'slug'     => 'imagify', 
        'required' => false,
      ),
      array( 
        'name'     => 'Duplicate Page', 
        'slug'     => 'duplicate-page', 
        'required' => true,
      ),
      array( 
        'name'     => 'Wordfence Security – Firewall, Malware Scan, and Login Security', 
        'slug'     => 'wordfence', 
        'required' => false,
      ),
        /** Array of bundled plugins  **/
    array( 
      'name'     => 'Divi Library Shortcodes',
      'slug'     => 'divi-library-shortcodes', // The slug has to match the extracted folder from the zip.
      'source'   => get_stylesheet_directory() . '/bundled-plugins/Divi-Library-Shortcodes-1.2.2.zip',
      'required' => true,
    ),
    array( 
        'name'     => 'Divi Mobile Menu',
        'slug'     => 'divi-mobile-menu', // The slug has to match the extracted folder from the zip.
        'source'   => get_stylesheet_directory() . '/bundled-plugins/Divi-Mobile-Menu-1.1.zip',
        'required' => false,
      ),
      array( 
        'name'     => 'Divi Table of Contents Maker',
        'slug'     => 'divi-table-of-contents-maker', // The slug has to match the extracted folder from the zip.
        'source'   => get_stylesheet_directory() . '/bundled-plugins/divi-table-of-contents-maker.zip',
        'required' => true,
      ),
      array( 
        'name'     => 'Gravity Forms Code Chest',
        'slug'     => 'gf-code-chest', // The slug has to match the extracted folder from the zip.
        'source'   => get_stylesheet_directory() . '/bundled-plugins/gf-code-chest.zip',
        'required' => false,
      ),
      array( 
        'name'     => 'Gravity Forms',
        'slug'     => 'gravityforms_2.8.14', // The slug has to match the extracted folder from the zip.
        'source'   => get_stylesheet_directory() . '/bundled-plugins/gravityforms_2.8.14.zip',
        'required' => false,
      ),
      array( 
        'name'     => 'Gravity Forms Styler for Divi',
        'slug'     => 'ds-gravity-forms-for-divi', // The slug has to match the extracted folder from the zip.
        'source'   => get_stylesheet_directory() . '/bundled-plugins/gravity-forms-styler-for-divi.zip',
        'required' => false,
      ),
      array( 
        'name'     => 'SEO by Rank Math Pro',
        'slug'     => 'seo-by-rank-math-pro', // The slug has to match the extracted folder from the zip.
        'source'   => get_stylesheet_directory() . '/bundled-plugins/seo-by-rank-math-pro.zip',
        'required' => true,
      ),
      array( 
        'name'     => 'WP Mail SMTP Pro',
        'slug'     => 'wp-mail-smtp-pro', // The slug has to match the extracted folder from the zip.
        'source'   => get_stylesheet_directory() . '/bundled-plugins/wp-mail-smtp-pro.zip',
        'required' => false,
      ),
      array( 
        'name'     => 'WPRocket',
        'slug'     => 'wp-rocket', // The slug has to match the extracted folder from the zip.
        'source'   => get_stylesheet_directory() . '/bundled-plugins/wp-rocket.zip',
        'required' => false,
      ),
  );

  /*
   * Array of configuration settings.
  */
  $config = array(
    'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
    'default_path' => '',                      // Default absolute path to bundled plugins.
    'menu'         => 'tgmpa-install-plugins', // Menu slug.
    'parent_slug'  => 'themes.php',            // Parent menu slug.
    'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
    'has_notices'  => true,                    // Show admin notices or not.
    'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
    'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
    'is_automatic' => false,                   // Automatically activate plugins after installation or not.
    'message'      => '',                      // Message to output right before the plugins table.
    /*
    'strings'      => array(
      'page_title'                      => __( 'Install Required Plugins', 'theme-slug' ),
      'menu_title'                      => __( 'Install Plugins', 'theme-slug' ),
      // <snip>...</snip>
      'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
    )
    */
  );
  tgmpa( $plugins, $config );
}

/* --------------------------------------------------
   FEATURED IMAGE CAPTION SHORTCODE
-------------------------------------------------- */
function wpw_featured_image_caption() {
    if (!is_singular()) return '';

    $thumbnail_id = get_post_thumbnail_id();
    if (!$thumbnail_id) return '';

    $caption = wp_get_attachment_caption($thumbnail_id);
    if (!$caption) return '';

    return '<figcaption class="featured-image-caption">' . wp_kses_post($caption) . '</figcaption>';
}
add_shortcode('featured_image_caption', 'wpw_featured_image_caption');

/* --------------------------------------------------
   WP WIZARDS ADMIN MENU & SETTINGS PAGE
-------------------------------------------------- */
add_action('admin_menu', 'wpwizards_admin_menu');
add_action('admin_enqueue_scripts', 'wpwizards_admin_styles');

function wpwizards_admin_menu() {
    // Use PHP file that outputs SVG with proper headers
    // This is the most reliable way to use SVG icons in WordPress admin menu
    $icon_path = get_stylesheet_directory() . '/assets/icon.php';
    $icon_url = get_stylesheet_directory_uri() . '/assets/icon.php';
    
    // Use custom icon if file exists, otherwise use dashicon
    $menu_icon = file_exists($icon_path) ? $icon_url : 'dashicons-admin-tools';
    
    add_menu_page(
        'WP Wizards Settings',
        'WP Wizards',
        'manage_options',
        'wpwizards-settings',
        'wpwizards_settings_page',
        $menu_icon,
        1 // Position 1 = very top of menu (right after Dashboard)
    );
}

function wpwizards_admin_styles($hook) {
    if ($hook !== 'toplevel_page_wpwizards-settings') {
        return;
    }
    ?>
    <style>
        .wpwizards-wrap {
            max-width: 1200px;
            margin: 20px 0;
        }
        .wpwizards-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .wpwizards-header h1 {
            margin: 0 0 10px 0;
            color: white;
            font-size: 28px;
        }
        .wpwizards-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .wpwizards-tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .wpwizards-tab {
            padding: 12px 24px;
            background: #f5f5f5;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            transition: all 0.3s;
            margin-right: 5px;
        }
        .wpwizards-tab:hover {
            background: #e9e9e9;
            color: #333;
        }
        .wpwizards-tab.active {
            background: white;
            border-bottom-color: #667eea;
            color: #667eea;
        }
        .wpwizards-tab-content {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .wpwizards-tab-content.active {
            display: block;
        }
        .wpwizards-section {
            margin-bottom: 40px;
        }
        .wpwizards-section h2 {
            font-size: 22px;
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            color: #333;
        }
        .wpwizards-section h3 {
            font-size: 18px;
            margin: 25px 0 15px 0;
            color: #555;
        }
        .wpwizards-section p {
            font-size: 14px;
            line-height: 1.6;
            color: #666;
        }
        .wpwizards-code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.5;
        }
        .wpwizards-code-block code {
            background: transparent;
            color: #f8f8f2;
            padding: 0;
        }
        .wpwizards-info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .wpwizards-success-box {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .wpwizards-warning-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .wpwizards-copy-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .wpwizards-copy-btn:hover {
            background: #5568d3;
        }
        .wpwizards-copy-btn.copied {
            background: #4CAF50;
        }
        .wpwizards-feature-list {
            list-style: none;
            padding: 0;
        }
        .wpwizards-feature-list li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        .wpwizards-feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
    <script>
        jQuery(document).ready(function($) {
            // Tab switching
            $('.wpwizards-tab').on('click', function() {
                var target = $(this).data('tab');
                $('.wpwizards-tab').removeClass('active');
                $('.wpwizards-tab-content').removeClass('active');
                $(this).addClass('active');
                $('#' + target).addClass('active');
            });
            
            // Copy to clipboard
            $('.wpwizards-copy-btn').on('click', function() {
                var codeBlock = $(this).siblings('.wpwizards-code-block');
                var text = codeBlock.find('code').text();
                
                var temp = $('<textarea>');
                $('body').append(temp);
                temp.val(text).select();
                document.execCommand('copy');
                temp.remove();
                
                $(this).text('Copied!').addClass('copied');
                setTimeout(function() {
                    $('.wpwizards-copy-btn').text('Copy Code').removeClass('copied');
                }, 2000);
            });
        });
    </script>
    <?php
}

function wpwizards_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    
    $logo = 'https://www.wpwizards.com/wp-content/uploads/2025/11/FC-Standard-scaled.png';
    ?>
    <div class="wrap wpwizards-wrap">
        <div class="wpwizards-header">
            <h1>WP Wizards Settings</h1>
            <p>Tools, shortcodes, and resources for your website</p>
        </div>
        
        <div class="wpwizards-tabs">
            <button class="wpwizards-tab active" data-tab="tab-shortcodes">Shortcodes</button>
            <button class="wpwizards-tab" data-tab="tab-tools">Tools</button>
            <button class="wpwizards-tab" data-tab="tab-updates">Theme Updates</button>
            <button class="wpwizards-tab" data-tab="tab-resources">Resources</button>
        </div>
        
        <!-- Shortcodes Tab -->
        <div id="tab-shortcodes" class="wpwizards-tab-content active">
            <div class="wpwizards-section">
                <h2>Featured Image Caption Shortcode</h2>
                <p>Display the caption of your post's featured image anywhere in your content using a simple shortcode.</p>
                
                <h3>How to Use</h3>
                <p>Simply add the shortcode <code>[featured_image_caption]</code> anywhere in your post or page content where you want the caption to appear.</p>
                
                <div class="wpwizards-code-block">
                    <code>[featured_image_caption]</code>
                </div>
                <button class="wpwizards-copy-btn">Copy Code</button>
                
                <div class="wpwizards-info-box">
                    <strong>Note:</strong> This shortcode only works on singular posts and pages (not archive pages). It will only display if:
                    <ul style="margin: 10px 0 0 20px;">
                        <li>The post/page has a featured image set</li>
                        <li>The featured image has a caption in the media library</li>
                    </ul>
                </div>
                
                <h3>Example Usage</h3>
                <p>In your Divi builder or classic editor, you might structure it like this:</p>
                
                <div class="wpwizards-code-block">
                    <code>&lt;figure&gt;
    &lt;?php the_post_thumbnail('large'); ?&gt;
    [featured_image_caption]
&lt;/figure&gt;</code>
                </div>
                <button class="wpwizards-copy-btn">Copy Code</button>
                
                <div class="wpwizards-success-box">
                    <strong>Tip:</strong> The caption will automatically be styled with italic text and a subtle gray color. The styling is already included in your child theme.
                </div>
            </div>
        </div>
        
        <!-- Tools Tab -->
        <div id="tab-tools" class="wpwizards-tab-content">
            <div class="wpwizards-section">
                <h2>Client Customizations</h2>
                <p>Add your own custom code without worrying about theme updates overwriting it.</p>
                
                <div class="wpwizards-success-box">
                    <strong>✅ Safe Customization File:</strong> Use <code>client-customizations.php</code> for all your custom code. This file is protected from theme updates!
                </div>
                
                <h3>How to Use</h3>
                <p>Edit the file <code>client-customizations.php</code> in your theme folder. You can add:</p>
                <ul class="wpwizards-feature-list">
                    <li>Custom functions</li>
                    <li>Custom hooks and filters</li>
                    <li>Custom shortcodes</li>
                    <li>Custom CSS and JavaScript</li>
                    <li>Any WordPress PHP code</li>
                </ul>
                
                <h3>File Location</h3>
                <div class="wpwizards-code-block">
                    <code>wp-content/themes/divi-child/client-customizations.php</code>
                </div>
                <button class="wpwizards-copy-btn">Copy Path</button>
                
                <h3>Example Usage</h3>
                <p>Here's an example of what you can add to <code>client-customizations.php</code>:</p>
                
                <div class="wpwizards-code-block">
                    <code>// Add custom CSS
function client_custom_css() {
    echo '&lt;style&gt;
        .my-custom-class { color: #ff0000; }
    &lt;/style&gt;';
}
add_action('wp_head', 'client_custom_css');

// Add custom shortcode
function client_custom_shortcode($atts) {
    return '&lt;p&gt;My custom content&lt;/p&gt;';
}
add_shortcode('my_shortcode', 'client_custom_shortcode');</code>
                </div>
                <button class="wpwizards-copy-btn">Copy Code</button>
                
                <div class="wpwizards-info-box">
                    <strong>Important Notes:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <li>This file is <strong>protected from updates</strong> - your code will never be overwritten</li>
                        <li>The file is included at the end of <code>functions.php</code>, so all WordPress functions are available</li>
                        <li><strong>Auto-created:</strong> If the file doesn't exist, it will be created automatically from the example template on first page load</li>
                        <li>Always test your customizations on a staging site first</li>
                        <li>If you make a mistake, you can delete the file and it will be recreated from the template</li>
                    </ul>
                </div>
                
                <h3>Access the File</h3>
                <p>You can edit this file via:</p>
                <ul style="margin-left: 20px;">
                    <li><strong>FTP/SFTP:</strong> Navigate to the theme folder and edit <code>client-customizations.php</code></li>
                    <li><strong>File Manager:</strong> Use your hosting control panel's file manager</li>
                    <li><strong>Code Editor:</strong> Use a code editor like VS Code, Cursor, or similar</li>
                    <li><strong>WordPress Plugin:</strong> Use a file editor plugin (not recommended for production)</li>
                </ul>
            </div>
        </div>
        
        <!-- Theme Updates Tab -->
        <div id="tab-updates" class="wpwizards-tab-content">
            <?php
            // Handle form submission
            if (isset($_POST['wpw_save_github_settings']) && check_admin_referer('wpw_github_settings')) {
                update_option('wpw_github_username', sanitize_text_field($_POST['github_username']));
                update_option('wpw_github_repo', sanitize_text_field($_POST['github_repo']));
                update_option('wpw_github_token', sanitize_text_field($_POST['github_token']));
                
                // Clear cache when settings are updated
                $theme = wp_get_theme();
                $theme_slug = $theme->get_stylesheet();
                delete_transient('wpw_theme_update_' . $theme_slug);
                delete_site_transient('update_themes');
                
                echo '<div class="notice notice-success is-dismissible"><p>GitHub settings saved and cache cleared!</p></div>';
            }
            
            // Handle reset to defaults
            if (isset($_GET['reset_github']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'reset_github')) {
                delete_option('wpw_github_username');
                delete_option('wpw_github_repo');
                delete_option('wpw_github_token');
                
                $theme = wp_get_theme();
                $theme_slug = $theme->get_stylesheet();
                delete_transient('wpw_theme_update_' . $theme_slug);
                delete_site_transient('update_themes');
                
                echo '<div class="notice notice-success is-dismissible"><p>GitHub settings reset to defaults!</p></div>';
            }
            
            // Handle cache clear request
            if (isset($_GET['clear_update_cache']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'clear_update_cache')) {
                $theme = wp_get_theme();
                $theme_slug = $theme->get_stylesheet();
                delete_transient('wpw_theme_update_' . $theme_slug);
                delete_site_transient('update_themes');
                echo '<div class="notice notice-success is-dismissible"><p>Update cache cleared! WordPress will check for updates on the next page load.</p></div>';
            }
            
            $theme = wp_get_theme();
            $current_version = $theme->get('Version');
            // Get values with defaults (hardcoded in theme)
            $github_username = get_option('wpw_github_username', 'joshchretien');
            $github_repo = get_option('wpw_github_repo', 'divi-child-theme');
            $github_token = get_option('wpw_github_token', '');
            ?>
            
            <div class="wpwizards-section">
                <h2>Theme Update System</h2>
                <p>Your child theme automatically checks for updates from GitHub Releases. When a new release is created, all sites will see the update notification.</p>
                
                <h3>Current Version</h3>
                <p>You are currently running version <strong><?php echo esc_html($current_version); ?></strong> of <?php echo esc_html($theme->get('Name')); ?>.</p>
                
                <div class="wpwizards-success-box">
                    <strong>✅ Pre-Configured:</strong> This theme is already configured to check for updates from:
                    <br><br>
                    <strong>GitHub Repository:</strong> 
                    <a href="https://github.com/<?php echo esc_attr($github_username); ?>/<?php echo esc_attr($github_repo); ?>" target="_blank">
                        <?php echo esc_html($github_username); ?>/<?php echo esc_html($github_repo); ?>
                    </a>
                    <br><br>
                    <em>No configuration needed! Updates will work automatically.</em>
                </div>
                
                <details style="margin-top: 20px;">
                    <summary style="cursor: pointer; font-weight: 600; color: #555; margin-bottom: 10px;">Advanced: Override GitHub Settings (Optional)</summary>
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-top: 10px;">
                        <p style="margin-top: 0;">Only change these if you need to use a different repository or private repo with a token.</p>
                        <form method="post" action="" style="margin-top: 15px;">
                            <?php wp_nonce_field('wpw_github_settings'); ?>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="github_username">GitHub Username/Organization</label>
                                    </th>
                                    <td>
                                        <input type="text" id="github_username" name="github_username" 
                                               value="<?php echo esc_attr($github_username); ?>" 
                                               class="regular-text" />
                                        <p class="description">Leave as default unless using a different repo</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="github_repo">Repository Name</label>
                                    </th>
                                    <td>
                                        <input type="text" id="github_repo" name="github_repo" 
                                               value="<?php echo esc_attr($github_repo); ?>" 
                                               class="regular-text" />
                                        <p class="description">Leave as default unless using a different repo</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="github_token">GitHub Token (Optional)</label>
                                    </th>
                                    <td>
                                        <input type="password" id="github_token" name="github_token" 
                                               value="<?php echo esc_attr($github_token); ?>" 
                                               class="regular-text" 
                                               placeholder="ghp_xxxxxxxxxxxx" />
                                        <p class="description">
                                            Only needed for private repos or higher API rate limits. 
                                            <a href="https://github.com/settings/tokens" target="_blank">Create one here</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <p class="submit">
                                <input type="submit" name="wpw_save_github_settings" class="button button-primary" value="Save Override Settings" />
                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('reset_github', '1'), 'reset_github')); ?>" class="button" onclick="return confirm('Reset to default GitHub settings?');">Reset to Defaults</a>
                            </p>
                        </form>
                    </div>
                </details>
                
                <div class="wpwizards-info-box">
                    <strong>How It Works:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <li>The theme checks GitHub Releases every 12 hours</li>
                        <li>When you create a new release on GitHub, all sites will see the update</li>
                        <li>You can update directly from WordPress admin without manual file uploads</li>
                        <li>Release notes from GitHub are displayed as the changelog</li>
                    </ul>
                </div>
                
                <h3>Manual Update Check</h3>
                <p>
                    <a href="<?php echo esc_url(admin_url('themes.php')); ?>" class="button button-primary">Check for Updates</a>
                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('clear_update_cache', '1'), 'clear_update_cache')); ?>" class="button">Clear Update Cache</a>
                </p>
                
                <div class="wpwizards-info-box" style="margin-top: 20px;">
                    <strong>How It Works:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <li>The theme automatically checks GitHub Releases every 12 hours</li>
                        <li>When you create a new release on GitHub, all sites will see the update</li>
                        <li>You can update directly from WordPress admin without manual file uploads</li>
                        <li>Release notes from GitHub are displayed as the changelog</li>
                        <li><strong>No configuration needed</strong> - it works out of the box!</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Resources Tab -->
        <div id="tab-resources" class="wpwizards-tab-content">
            <div class="wpwizards-section">
                <h2>Helpful Resources</h2>
                <p>Links and information to help you get the most out of your website.</p>
                
                <h3>Support & Contact</h3>
                <p>
                    <a href="https://dashboard.wpwizards.com/" target="_blank" class="button button-primary">Analytics Dashboard</a>
                    <a href="https://calendly.com/wpwizards" target="_blank" class="button button-primary">Book a Call</a>
                    <a href="https://pricelessconsultingllc.atlassian.net/servicedesk/customer/portal/1/group/1/create/17" target="_blank" class="button button-primary">Open a Ticket</a>
                </p>
                
                <h3>Documentation</h3>
                <p>Documentation and guides will be added here as they become available.</p>
                
                <div class="wpwizards-info-box">
                    <strong>Need Help?</strong> If you have questions about any of the tools or features in this child theme, don't hesitate to reach out through one of the support channels above.
                </div>
            </div>
        </div>
    </div>
    <?php
}

/* --------------------------------------------------
   CLIENT CUSTOMIZATIONS
   This file is included at the end so client code runs after theme code.
   This file is in .gitignore and will NOT be overwritten during updates.
-------------------------------------------------- */
$client_customizations = get_stylesheet_directory() . '/client-customizations.php';
$client_customizations_example = get_stylesheet_directory() . '/client-customizations.php.example';

// If client file doesn't exist but example does, create it from example
if (!file_exists($client_customizations) && file_exists($client_customizations_example)) {
    copy($client_customizations_example, $client_customizations);
}

// Include client customizations if it exists
if (file_exists($client_customizations)) {
    require_once $client_customizations;
}