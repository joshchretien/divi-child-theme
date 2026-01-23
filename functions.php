<?php
/* --------------------------------------------------
   WP Wizards – Premium Dashboard Widget
   Includes:
   - Update Plugins button
   - Logged-in user/device/IP info
   - Custom WP Footer Branding
   - Forced widget to top
-------------------------------------------------- */


// Lower priority so notices appear above dashboard widget
add_action('wp_dashboard_setup', 'wpwizards_dashboard_widget', 99);

function wpwizards_dashboard_widget() {

    wp_add_dashboard_widget(
        'wpwizards_dashboard_widget',
        'Website Support',
        'wpwizards_dashboard_widget_content'
    );

    // Move widget to top of normal column (but after notices)
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

        <!-- SEO KICKOFF SECTION -->
        <?php
        // Get SEO Kickoff task counts
        $kickoff_tasks = get_posts(array(
            'post_type' => 'seo_kickoff',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids'
        ));
        
        $in_progress_count = 0;
        $done_count = 0;
        
        if (!empty($kickoff_tasks)) {
            foreach ($kickoff_tasks as $task_id) {
                $status = get_post_meta($task_id, '_kickoff_status', true) ?: 'in_progress';
                if ($status === 'in_progress') {
                    $in_progress_count++;
                } elseif ($status === 'done') {
                    $done_count++;
                }
            }
        }
        ?>
        <div style="background:#f8f9fa; border:1px solid #e0e0e0; border-radius:8px; padding:15px; margin:20px 0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <h3 style="margin:0; font-size:16px; color:#333;">SEO Kickoff</h3>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wpwizards-settings#kickoff')); ?>" 
                   class="button button-secondary button-small"
                   style="display:inline-flex; align-items:center;">
                    <span class="dashicons dashicons-list-view" style="margin-right:4px; font-size:16px;"></span>
                    View All Tasks
                </a>
            </div>
            <div style="display:flex; gap:20px; font-size:14px; color:#555; margin-bottom:15px;">
                <div>
                    <strong style="color:#856404;">In Progress:</strong> 
                    <span style="font-weight:bold; color:#856404;"><?php echo esc_html($in_progress_count); ?></span>
                </div>
                <div>
                    <strong style="color:#155724;">Done:</strong> 
                    <span style="font-weight:bold; color:#155724;"><?php echo esc_html($done_count); ?></span>
                </div>
            </div>
            
            <!-- Quick Action Buttons -->
            <div style="margin-top:10px;">
                <?php
                // Find "Create Client Folder" task by exact title
                // Only query if post type exists
                if (post_type_exists('seo_kickoff')) {
                    $all_tasks = get_posts(array(
                        'post_type' => 'seo_kickoff',
                        'post_status' => 'any',
                        'posts_per_page' => -1,
                        'suppress_filters' => false
                    ));
                    
                    $client_folder_task = null;
                    $customer_avatar_task = null;
                    $influencer_avatar_task = null;
                    
                    if (!empty($all_tasks)) {
                        foreach ($all_tasks as $task) {
                            if ($task->post_title === 'Create Client Folder') {
                                $client_folder_task = $task;
                            } elseif ($task->post_title === 'Build Customer Avatar') {
                                $customer_avatar_task = $task;
                            } elseif ($task->post_title === 'Build Influencer Avatar') {
                                $influencer_avatar_task = $task;
                            }
                        }
                    }
                    
                    if ($client_folder_task) {
                        $client_folder_notes = $client_folder_task->post_content;
                        // Check if notes contain a URL
                        $has_url = preg_match('/https?:\/\/[^\s]+/', $client_folder_notes, $matches);
                        $url = $has_url ? $matches[0] : admin_url('admin.php?page=wpwizards-settings#kickoff');
                        ?>
                        <a href="<?php echo esc_url($url); ?>" 
                           target="<?php echo $has_url ? '_blank' : '_self'; ?>"
                           class="button button-secondary button-small"
                           style="display:inline-flex; align-items:center; margin-bottom:10px;">
                            <span class="dashicons dashicons-portfolio" style="margin-right:4px; font-size:16px;"></span>
                            Create Client Folder
                        </a>
                    <?php } ?>
                    
                    <?php
                    if ($customer_avatar_task || $influencer_avatar_task) {
                        ?>
                        <div style="margin-top:15px; padding-top:15px; border-top:1px solid #e0e0e0;">
                            <h4 style="margin:0 0 10px 0; font-size:14px; font-weight:600; color:#333;">Your Avatars</h4>
                            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                <?php if ($customer_avatar_task): 
                                    $customer_notes = $customer_avatar_task->post_content;
                                    // Check if notes contain a URL
                                    $customer_has_url = preg_match('/https?:\/\/[^\s<>"\'\)]+/i', strip_tags($customer_notes), $customer_matches);
                                    $customer_url = $customer_has_url ? $customer_matches[0] : admin_url('admin.php?page=wpwizards-settings#kickoff');
                                    ?>
                                    <a href="<?php echo esc_url($customer_url); ?>" 
                                       target="<?php echo $customer_has_url ? '_blank' : '_self'; ?>"
                                       class="button button-secondary button-small"
                                       style="display:inline-flex; align-items:center;">
                                        <span class="dashicons dashicons-groups" style="margin-right:4px; font-size:16px;"></span>
                                        Customer Avatar
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($influencer_avatar_task): 
                                    $influencer_notes = $influencer_avatar_task->post_content;
                                    // Check if notes contain a URL
                                    $influencer_has_url = preg_match('/https?:\/\/[^\s<>"\'\)]+/i', strip_tags($influencer_notes), $influencer_matches);
                                    $influencer_url = $influencer_has_url ? $influencer_matches[0] : admin_url('admin.php?page=wpwizards-settings#kickoff');
                                    ?>
                                    <a href="<?php echo esc_url($influencer_url); ?>" 
                                       target="<?php echo $influencer_has_url ? '_blank' : '_self'; ?>"
                                       class="button button-secondary button-small"
                                       style="display:inline-flex; align-items:center;">
                                        <span class="dashicons dashicons-groups" style="margin-right:4px; font-size:16px;"></span>
                                        Influencer Avatar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
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
        'name'     => 'Gravity Forms Styler for Divi',
        'slug'     => 'gravity-forms-styler-for-divi', // The slug has to match the extracted folder from the zip.
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
    
    // Enqueue editor scripts for Kickoff tab (rich text editor)
    wp_enqueue_editor();
    wp_enqueue_media();
    
    // Enqueue JavaScript file with jQuery dependency
    wp_enqueue_script(
        'wpwizards-admin',
        get_stylesheet_directory_uri() . '/assets/wpwizards-admin.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/assets/wpwizards-admin.js'), // Use filemtime for cache busting
        true // Load in footer after DOM is ready
    );
    
    // Add inline script to ensure tabs work even if external script fails
    $inline_script = "jQuery(document).ready(function($) {
        // Only add fallback if external script hasn't already set up tabs
        setTimeout(function() {
            if (!$('.wpwizards-tab').data('initialized')) {
                $('.wpwizards-tab').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var target = $(this).attr('data-tab');
                    if (target) {
                        $('.wpwizards-tab').removeClass('active');
                        $('.wpwizards-tab-content').removeClass('active');
                        $(this).addClass('active');
                        $('#' + target).addClass('active');
                    }
                });
                $('.wpwizards-tab').data('initialized', true);
            }
        }, 100);
    });";
    wp_add_inline_script('wpwizards-admin', $inline_script);
    ?>
    <style>
        .wpwizards-wrap {
            max-width: 1200px;
            margin: 20px 0;
        }
        .wpwizards-header {
            background: #381962;
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
            text-align: center;
        }
        .wpwizards-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
            text-align: center;
        }
        .wpwizards-header-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .wpwizards-header-logo img {
            max-width: 300px;
            height: auto;
            display: block;
            margin: 0 auto;
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
            position: relative;
            z-index: 1;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .wpwizards-tab:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
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
        /* Force all admin notices text to black for visibility - regardless of background */
        body.toplevel_page_wpwizards-settings .notice,
        body.toplevel_page_wpwizards-settings .notice *:not(.button):not(.button *):not(a.button),
        body.toplevel_page_wpwizards-settings .notice p,
        body.toplevel_page_wpwizards-settings .notice h1,
        body.toplevel_page_wpwizards-settings .notice h2,
        body.toplevel_page_wpwizards-settings .notice h3,
        body.toplevel_page_wpwizards-settings .notice h4,
        body.toplevel_page_wpwizards-settings .notice div:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice span:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice strong,
        body.toplevel_page_wpwizards-settings .notice em,
        body.toplevel_page_wpwizards-settings .notice b,
        body.toplevel_page_wpwizards-settings .notice i,
        body.toplevel_page_wpwizards-settings .notice,
        body.toplevel_page_wpwizards-settings .notice p,
        body.toplevel_page_wpwizards-settings .notice h1,
        body.toplevel_page_wpwizards-settings .notice h2,
        body.toplevel_page_wpwizards-settings .notice h3,
        body.toplevel_page_wpwizards-settings .notice h4,
        body.toplevel_page_wpwizards-settings .notice div:not(.button),
        body.toplevel_page_wpwizards-settings .notice span:not(.button),
        body.toplevel_page_wpwizards-settings .notice-success,
        body.toplevel_page_wpwizards-settings .notice-success *:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-success p,
        body.toplevel_page_wpwizards-settings .notice-success h1,
        body.toplevel_page_wpwizards-settings .notice-success h2,
        body.toplevel_page_wpwizards-settings .notice-success h3,
        body.toplevel_page_wpwizards-settings .notice-success h4,
        body.toplevel_page_wpwizards-settings .notice-success div:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-success span:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-success strong,
        body.toplevel_page_wpwizards-settings .notice-success em,
        body.toplevel_page_wpwizards-settings .notice-success b,
        body.toplevel_page_wpwizards-settings .notice-success i,
        body.toplevel_page_wpwizards-settings .notice-warning,
        body.toplevel_page_wpwizards-settings .notice-warning *:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-warning p,
        body.toplevel_page_wpwizards-settings .notice-warning h1,
        body.toplevel_page_wpwizards-settings .notice-warning h2,
        body.toplevel_page_wpwizards-settings .notice-warning h3,
        body.toplevel_page_wpwizards-settings .notice-warning h4,
        body.toplevel_page_wpwizards-settings .notice-warning div:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-warning span:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-warning strong,
        body.toplevel_page_wpwizards-settings .notice-warning em,
        body.toplevel_page_wpwizards-settings .notice-warning b,
        body.toplevel_page_wpwizards-settings .notice-warning i,
        body.toplevel_page_wpwizards-settings .notice-error,
        body.toplevel_page_wpwizards-settings .notice-error *:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-error p,
        body.toplevel_page_wpwizards-settings .notice-error h1,
        body.toplevel_page_wpwizards-settings .notice-error h2,
        body.toplevel_page_wpwizards-settings .notice-error h3,
        body.toplevel_page_wpwizards-settings .notice-error h4,
        body.toplevel_page_wpwizards-settings .notice-error div:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-error span:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-error strong,
        body.toplevel_page_wpwizards-settings .notice-error em,
        body.toplevel_page_wpwizards-settings .notice-error b,
        body.toplevel_page_wpwizards-settings .notice-error i,
        body.toplevel_page_wpwizards-settings .notice-info,
        body.toplevel_page_wpwizards-settings .notice-info *:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-info p,
        body.toplevel_page_wpwizards-settings .notice-info h1,
        body.toplevel_page_wpwizards-settings .notice-info h2,
        body.toplevel_page_wpwizards-settings .notice-info h3,
        body.toplevel_page_wpwizards-settings .notice-info h4,
        body.toplevel_page_wpwizards-settings .notice-info div:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-info span:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-info strong,
        body.toplevel_page_wpwizards-settings .notice-info em,
        body.toplevel_page_wpwizards-settings .notice-info b,
        body.toplevel_page_wpwizards-settings .notice-info i,
        body.toplevel_page_wpwizards-settings .update-nag,
        body.toplevel_page_wpwizards-settings .update-nag p,
        body.toplevel_page_wpwizards-settings .update-nag h1,
        body.toplevel_page_wpwizards-settings .update-nag h2,
        body.toplevel_page_wpwizards-settings .update-nag h3,
        body.toplevel_page_wpwizards-settings .update-nag h4,
        body.toplevel_page_wpwizards-settings .update-nag div:not(.button),
        body.toplevel_page_wpwizards-settings .update-nag span:not(.button),
        body.toplevel_page_wpwizards-settings .settings-error,
        body.toplevel_page_wpwizards-settings .settings-error p,
        body.toplevel_page_wpwizards-settings .settings-error h1,
        body.toplevel_page_wpwizards-settings .settings-error h2,
        body.toplevel_page_wpwizards-settings .settings-error h3,
        body.toplevel_page_wpwizards-settings .settings-error h4,
        body.toplevel_page_wpwizards-settings .settings-error div:not(.button),
        body.toplevel_page_wpwizards-settings .settings-error span:not(.button),
        .wpwizards-wrap .notice,
        .wpwizards-wrap .notice p,
        .wpwizards-wrap .notice h1,
        .wpwizards-wrap .notice h2,
        .wpwizards-wrap .notice h3,
        .wpwizards-wrap .notice h4,
        .wpwizards-wrap .notice div:not(.button),
        .wpwizards-wrap .notice span:not(.button),
        .wpwizards-wrap .notice-success,
        .wpwizards-wrap .notice-success p,
        .wpwizards-wrap .notice-success h1,
        .wpwizards-wrap .notice-success h2,
        .wpwizards-wrap .notice-success h3,
        .wpwizards-wrap .notice-success h4,
        .wpwizards-wrap .notice-success div:not(.button),
        .wpwizards-wrap .notice-success span:not(.button),
        .wpwizards-wrap .notice-warning,
        .wpwizards-wrap .notice-warning p,
        .wpwizards-wrap .notice-warning h1,
        .wpwizards-wrap .notice-warning h2,
        .wpwizards-wrap .notice-warning h3,
        .wpwizards-wrap .notice-warning h4,
        .wpwizards-wrap .notice-warning div:not(.button),
        .wpwizards-wrap .notice-warning span:not(.button),
        .wpwizards-wrap .notice-error,
        .wpwizards-wrap .notice-error p,
        .wpwizards-wrap .notice-error h1,
        .wpwizards-wrap .notice-error h2,
        .wpwizards-wrap .notice-error h3,
        .wpwizards-wrap .notice-error h4,
        .wpwizards-wrap .notice-error div:not(.button),
        .wpwizards-wrap .notice-error span:not(.button),
        .wpwizards-wrap .notice-info,
        .wpwizards-wrap .notice-info p,
        .wpwizards-wrap .notice-info h1,
        .wpwizards-wrap .notice-info h2,
        .wpwizards-wrap .notice-info h3,
        .wpwizards-wrap .notice-info h4,
        .wpwizards-wrap .notice-info div:not(.button),
        .wpwizards-wrap .notice-info span:not(.button),
        .wpwizards-wrap .update-nag,
        .wpwizards-wrap .update-nag p,
        .wpwizards-wrap .update-nag h1,
        .wpwizards-wrap .update-nag h2,
        .wpwizards-wrap .update-nag h3,
        .wpwizards-wrap .update-nag h4,
        .wpwizards-wrap .update-nag div:not(.button),
        .wpwizards-wrap .update-nag span:not(.button),
        .wpwizards-wrap .settings-error,
        .wpwizards-wrap .settings-error p,
        .wpwizards-wrap .settings-error h1,
        .wpwizards-wrap .settings-error h2,
        .wpwizards-wrap .settings-error h3,
        .wpwizards-wrap .settings-error h4,
        .wpwizards-wrap .settings-error div:not(.button),
        .wpwizards-wrap .settings-error span:not(.button) {
            color: #000 !important;
        }
        /* Catch-all for any text elements we might have missed - but exclude buttons */
        body.toplevel_page_wpwizards-settings .notice *:not(.button):not(.button-primary):not(.button-secondary):not(a.button):not(a.button-primary):not(a.button-secondary) {
            color: #000 !important;
        }
        /* Links in notices should be blue */
        body.toplevel_page_wpwizards-settings .notice a:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-success a:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-warning a:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-error a:not(.button):not(.button *),
        body.toplevel_page_wpwizards-settings .notice-info a:not(.button):not(.button *),
        .wpwizards-wrap .notice a:not(.button):not(.button *),
        .wpwizards-wrap .notice-success a:not(.button):not(.button *),
        .wpwizards-wrap .notice-warning a:not(.button):not(.button *),
        .wpwizards-wrap .notice-error a:not(.button):not(.button *),
        .wpwizards-wrap .notice-info a:not(.button):not(.button *) {
            color: #0073aa !important;
        }
        /* Primary buttons (blue) should have white text */
        body.toplevel_page_wpwizards-settings .notice .button-primary,
        body.toplevel_page_wpwizards-settings .notice-success .button-primary,
        body.toplevel_page_wpwizards-settings .notice-warning .button-primary,
        body.toplevel_page_wpwizards-settings .notice-error .button-primary,
        body.toplevel_page_wpwizards-settings .notice-info .button-primary,
        .wpwizards-wrap .notice .button-primary,
        .wpwizards-wrap .notice-success .button-primary,
        .wpwizards-wrap .notice-warning .button-primary,
        .wpwizards-wrap .notice-error .button-primary,
        .wpwizards-wrap .notice-info .button-primary {
            color: #fff !important;
        }
        /* Secondary buttons (white/light) should have dark text */
        body.toplevel_page_wpwizards-settings .notice .button-secondary,
        body.toplevel_page_wpwizards-settings .notice-success .button-secondary,
        body.toplevel_page_wpwizards-settings .notice-warning .button-secondary,
        body.toplevel_page_wpwizards-settings .notice-error .button-secondary,
        body.toplevel_page_wpwizards-settings .notice-info .button-secondary,
        .wpwizards-wrap .notice .button-secondary,
        .wpwizards-wrap .notice-success .button-secondary,
        .wpwizards-wrap .notice-warning .button-secondary,
        .wpwizards-wrap .notice-error .button-secondary,
        .wpwizards-wrap .notice-info .button-secondary {
            color: #2271b1 !important;
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
            <div class="wpwizards-header-logo">
                <img src="https://wpwizards.com/wp-content/uploads/2025/11/FC-Trans-Horizontal-scaled.png" 
                     alt="WP Wizards Logo">
            </div>
            <h1>WP Wizards Settings</h1>
            <p>Tools, shortcodes, and resources for your website</p>
        </div>
        
        <div class="wpwizards-tabs">
            <button class="wpwizards-tab active" data-tab="tab-kickoff">Kickoff</button>
            <button class="wpwizards-tab" data-tab="tab-documentation">Documentation</button>
            <button class="wpwizards-tab" data-tab="tab-customize">Customize</button>
            <button class="wpwizards-tab" data-tab="tab-get-help">Get Help</button>
        </div>
        
        <!-- Kickoff Tab -->
        <div id="tab-kickoff" class="wpwizards-tab-content active">
            <?php wpwizards_kickoff_tab_content(); ?>
        </div>
        
        <!-- Documentation Tab -->
        <div id="tab-documentation" class="wpwizards-tab-content">
            <div class="wpwizards-section">
                <h2>Theme Features</h2>
                <p>This child theme includes a comprehensive set of features designed to enhance your WordPress experience and streamline your workflow.</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 30px 0;">
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">🎯 SEO Kickoff System</h3>
                        <p>Complete task management system for SEO projects. Track tasks, assign team members, manage status, and keep detailed notes with rich text editing. Includes bulk actions, auto-sorting by status, collapsible task cards, and quick link buttons for URLs in notes.</p>
                    </div>
                    
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">👥 User Permissions Generator</h3>
                        <p>Automatically create administrator accounts and migrate content from old users. One-click solution to set up team members and transfer all posts, comments, and metadata to new accounts.</p>
                    </div>
                    
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">📸 Featured Image Caption</h3>
                        <p>Simple shortcode to display featured image captions anywhere in your content. Automatically styled and ready to use.</p>
                    </div>
                    
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">✅ Image Attribution Verifier</h3>
                        <p>Built-in verification system for image copyright attribution. Track which posts have verified attribution with user and timestamp tracking. Includes sortable columns and filtering in the Posts list.</p>
                    </div>
                    
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">🛡️ Protected Customizations</h3>
                        <p>Safe customization file that's protected from theme updates. Add your own code without fear of losing it during updates. Automatically migrates custom code from previous themes on activation.</p>
                    </div>
                    
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">🔄 Automatic Updates</h3>
                        <p>Automatic theme updates from GitHub Releases. Get new features and improvements without manual intervention. Pre-configured and ready to use.</p>
                    </div>
                    
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">📊 Dashboard Widget</h3>
                        <p>Custom dashboard widget with quick access to support resources, analytics dashboard, SEO Kickoff task summaries, and quick action buttons for common tasks like Create Client Folder and Avatar management.</p>
                    </div>
                    
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">🔌 Plugin Manager</h3>
                        <p>Built-in plugin installer and recommender. Get notified about recommended plugins and install them with one click. Supports both repository and bundled plugins.</p>
                    </div>
                    
                    <div class="wpwizards-success-box" style="margin: 0;">
                        <h3 style="margin-top: 0;">⚙️ Admin Settings Page</h3>
                        <p>Centralized settings page for all WP Wizards tools and features. Easy access to documentation, customization options, SEO Kickoff tasks, and support resources.</p>
                    </div>
                </div>
            </div>
            
            <div class="wpwizards-section">
                <h2>Shortcodes</h2>
                
                <h3>Featured Image Caption</h3>
                <p>Display the caption of your post's featured image anywhere in your content using a simple shortcode.</p>
                
                <h4>How to Use</h4>
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
                
                <h4>Example Usage</h4>
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
            
            <div class="wpwizards-section">
                <h2>Tools & Customizations</h2>
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
        
        <!-- Customize Tab -->
        <div id="tab-customize" class="wpwizards-tab-content">
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
                    <code><?php echo esc_html(str_replace(ABSPATH, '', get_stylesheet_directory() . '/client-customizations.php')); ?></code>
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
                
                <?php
                $customizations_file = get_stylesheet_directory() . '/client-customizations.php';
                $example_file = get_stylesheet_directory() . '/client-customizations.php.example';
                
                if (file_exists($customizations_file)) {
                    $file_size = filesize($customizations_file);
                    $file_modified = filemtime($customizations_file);
                    ?>
                    <div class="wpwizards-success-box" style="margin-top: 20px;">
                        <strong>File Status:</strong> The customization file exists and is ready to use.
                        <ul style="margin: 10px 0 0 20px;">
                            <li>File size: <?php echo esc_html(size_format($file_size)); ?></li>
                            <li>Last modified: <?php echo esc_html(date('F j, Y g:i a', $file_modified)); ?></li>
                        </ul>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="wpwizards-info-box" style="margin-top: 20px;">
                        <strong>File Status:</strong> The customization file doesn't exist yet. It will be automatically created from the example template when you first load this page, or you can create it manually.
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        
        <!-- Get Help Tab -->
        <div id="tab-get-help" class="wpwizards-tab-content">
            <div class="wpwizards-section">
                <h2>Get Help</h2>
                <p>Need assistance? We're here to help you get the most out of your website.</p>
                
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
   IMAGE ATTRIBUTION VERIFIER
   Adds verification checkbox to Posts to confirm image copyright attribution exists
-------------------------------------------------- */
class WPW_Image_Attribution_Verifier {
	const META_VERIFIED     = '_wpw_attr_verified';
	const META_VERIFIED_BY  = '_wpw_attr_verified_by';
	const META_VERIFIED_UID = '_wpw_attr_verified_uid';
	const META_VERIFIED_AT  = '_wpw_attr_verified_at';

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_metabox' ] );
		add_action( 'save_post', [ $this, 'save_metabox' ] );

		add_filter( 'manage_post_posts_columns', [ $this, 'add_columns' ] );
		add_action( 'manage_post_posts_custom_column', [ $this, 'render_columns' ], 10, 2 );
		add_filter( 'manage_edit-post_sortable_columns', [ $this, 'sortable_columns' ] );
		add_action( 'pre_get_posts', [ $this, 'handle_sorting' ] );

		// Optional: make it easier to filter quickly via "All / Verified / Not Verified"
		add_action( 'restrict_manage_posts', [ $this, 'add_filter_dropdown' ] );
		add_filter( 'parse_query', [ $this, 'filter_query' ] );
	}

	public function add_metabox() {
		add_meta_box(
			'wpw_attr_verifier',
			'WP Wizards — Image Attribution Verification',
			[ $this, 'render_metabox' ],
			'post',
			'side',
			'high'
		);
	}

	public function render_metabox( $post ) {
		wp_nonce_field( 'wpw_attr_verifier_save', 'wpw_attr_verifier_nonce' );

		$verified    = get_post_meta( $post->ID, self::META_VERIFIED, true );
		$verified_by = get_post_meta( $post->ID, self::META_VERIFIED_BY, true );
		$verified_at = get_post_meta( $post->ID, self::META_VERIFIED_AT, true );

		$checked = ( $verified === '1' ) ? 'checked' : '';

		echo '<p style="margin:0 0 10px;">Mark this post only if you confirmed proper image attribution exists (where required).</p>';

		echo '<label style="display:block; margin:10px 0;">';
		echo '<input type="checkbox" name="wpw_attr_verified" value="1" ' . esc_attr( $checked ) . ' /> ';
		echo '<strong>Verified Image Copyright Attribution Exists</strong>';
		echo '</label>';

		if ( $verified === '1' ) {
			$pretty_time = '';
			if ( $verified_at ) {
				$pretty_time = date_i18n( 'M j, Y g:i a', (int) $verified_at );
			}

			echo '<hr style="margin:12px 0;" />';
			echo '<p style="margin:0 0 6px;"><strong>Status:</strong> ✅ Verified</p>';

			if ( $verified_by ) {
				echo '<p style="margin:0 0 6px;"><strong>Verified by:</strong> ' . esc_html( $verified_by ) . '</p>';
			}
			if ( $pretty_time ) {
				echo '<p style="margin:0 0 6px;"><strong>Verified at:</strong> ' . esc_html( $pretty_time ) . '</p>';
			}

			echo '<p style="margin:10px 0 0; font-size:12px; color:#555;">Unchecking will remove verification info.</p>';
		} else {
			echo '<p style="margin:10px 0 0; font-size:12px; color:#555;">Not verified yet.</p>';
		}
	}

	public function save_metabox( $post_id ) {
		// Autosave / revisions
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( wp_is_post_revision( $post_id ) ) return;

		// Nonce check
		if ( ! isset( $_POST['wpw_attr_verifier_nonce'] ) ) return;
		if ( ! wp_verify_nonce( $_POST['wpw_attr_verifier_nonce'], 'wpw_attr_verifier_save' ) ) return;

		// Capability
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		$should_verify = isset( $_POST['wpw_attr_verified'] ) && $_POST['wpw_attr_verified'] === '1';

		if ( $should_verify ) {
			$current_user = wp_get_current_user();
			$display_name = $current_user && $current_user->exists() ? $current_user->display_name : '';

			update_post_meta( $post_id, self::META_VERIFIED, '1' );
			update_post_meta( $post_id, self::META_VERIFIED_BY, sanitize_text_field( $display_name ) );
			update_post_meta( $post_id, self::META_VERIFIED_UID, (string) get_current_user_id() );
			update_post_meta( $post_id, self::META_VERIFIED_AT, (string) time() );
		} else {
			// Unchecked = clear the whole verification state
			delete_post_meta( $post_id, self::META_VERIFIED );
			delete_post_meta( $post_id, self::META_VERIFIED_BY );
			delete_post_meta( $post_id, self::META_VERIFIED_UID );
			delete_post_meta( $post_id, self::META_VERIFIED_AT );
		}
	}

	public function add_columns( $columns ) {
		$new = [];

		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;

			if ( $key === 'title' ) {
				$new['wpw_attribution'] = 'Attribution';
			}
		}

		return $new;
	}

	public function render_columns( $column, $post_id ) {
		if ( $column !== 'wpw_attribution' ) {
			return;
		}

		$verified = get_post_meta( $post_id, self::META_VERIFIED, true );

		if ( $verified === '1' ) {
			$by = get_post_meta( $post_id, self::META_VERIFIED_BY, true );
			$ts = (int) get_post_meta( $post_id, self::META_VERIFIED_AT, true );

			echo '<div style="line-height:1.4;">';
			echo '<strong>Status:</strong> <span style="color:#2e7d32;">✅ Yes</span><br>';

			if ( $by ) {
				echo '<strong>By:</strong> ' . esc_html( $by ) . '<br>';
			}

			if ( $ts ) {
				echo '<strong>On:</strong> ' . esc_html( date_i18n( 'M j, Y', $ts ) );
			}

			echo '</div>';
		} else {
			echo '<div style="line-height:1.4;">';
			echo '<strong>Status:</strong> <span style="color:#c62828;">❌ No</span>';
			echo '</div>';
		}
	}

	public function sortable_columns( $columns ) {
		$columns['wpw_attribution'] = 'wpw_attr_verified';
		return $columns;
	}

	public function handle_sorting( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) return;

		$orderby = $query->get( 'orderby' );
		if ( $orderby === 'wpw_attr_verified' ) {
			$query->set( 'meta_key', self::META_VERIFIED );
			$query->set( 'orderby', 'meta_value' ); // 1 vs empty
		}

		if ( $orderby === 'wpw_attr_verified_at' ) {
			$query->set( 'meta_key', self::META_VERIFIED_AT );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}

	public function add_filter_dropdown() {
		global $typenow;
		if ( $typenow !== 'post' ) return;

		$current = isset( $_GET['wpw_attr_filter'] ) ? sanitize_text_field( $_GET['wpw_attr_filter'] ) : '';

		echo '<select name="wpw_attr_filter" style="margin-left:6px;">';
		echo '<option value="">Attribution: All</option>';
		echo '<option value="verified"' . selected( $current, 'verified', false ) . '>Attribution: Verified</option>';
		echo '<option value="not_verified"' . selected( $current, 'not_verified', false ) . '>Attribution: Not Verified</option>';
		echo '</select>';
	}

	public function filter_query( $query ) {
		global $pagenow;

		if ( ! is_admin() ) return;
		if ( $pagenow !== 'edit.php' ) return;
		if ( ! isset( $query->query['post_type'] ) || $query->query['post_type'] !== 'post' ) return;

		if ( empty( $_GET['wpw_attr_filter'] ) ) return;

		$filter = sanitize_text_field( $_GET['wpw_attr_filter'] );

		if ( $filter === 'verified' ) {
			$query->query_vars['meta_query'] = [
				[
					'key'     => self::META_VERIFIED,
					'value'   => '1',
					'compare' => '='
				]
			];
		} elseif ( $filter === 'not_verified' ) {
			$query->query_vars['meta_query'] = [
				'relation' => 'OR',
				[
					'key'     => self::META_VERIFIED,
					'compare' => 'NOT EXISTS',
				],
				[
					'key'     => self::META_VERIFIED,
					'value'   => '1',
					'compare' => '!=',
				]
			];
		}
	}
}

// Initialize Image Attribution Verifier
if ( is_admin() ) {
	new WPW_Image_Attribution_Verifier();
}

/* --------------------------------------------------
   SEO KICKOFF SYSTEM
   Custom post type and interface for managing SEO tasks
-------------------------------------------------- */

// Register Custom Post Type for SEO Kickoff Tasks
add_action('init', 'wpwizards_register_kickoff_post_type');

function wpwizards_register_kickoff_post_type() {
    $args = array(
        'label' => 'SEO Kickoff Tasks',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Hide from main menu, we'll show in our custom page
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor'),
        'has_archive' => false,
    );
    register_post_type('seo_kickoff', $args);
}

// Add custom meta boxes
add_action('add_meta_boxes', 'wpwizards_add_kickoff_meta_boxes');

function wpwizards_add_kickoff_meta_boxes() {
    add_meta_box(
        'wpwizards_kickoff_meta',
        'Task Details',
        'wpwizards_kickoff_meta_box',
        'seo_kickoff',
        'normal',
        'high'
    );
}

function wpwizards_kickoff_meta_box($post) {
    wp_nonce_field('wpwizards_kickoff_meta', 'wpwizards_kickoff_nonce');
    
    $assigned_to = get_post_meta($post->ID, '_kickoff_assigned_to', true);
    $status = get_post_meta($post->ID, '_kickoff_status', true) ?: 'in_progress';
    $last_updated_by = get_post_meta($post->ID, '_kickoff_last_updated_by', true);
    $last_updated_date = get_post_meta($post->ID, '_kickoff_last_updated_date', true);
    
    // Get all users
    $users = get_users(array('orderby' => 'display_name'));
    
    // Find default users
    $alex_user = get_user_by('email', 'alexandra@wpwizards.com');
    $josh_user = get_user_by('email', 'josh@wpwizards.com');
    
    // Set default assignment based on task title
    if (!$assigned_to) {
        $title_lower = strtolower($post->post_title);
        if (strpos($title_lower, 'alex') !== false || strpos($title_lower, 'alexandra') !== false) {
            $assigned_to = $alex_user ? $alex_user->ID : '';
        } elseif (strpos($title_lower, 'josh') !== false) {
            $assigned_to = $josh_user ? $josh_user->ID : '';
        }
    }
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="kickoff_assigned_to">Assigned To</label></th>
            <td>
                <select name="kickoff_assigned_to" id="kickoff_assigned_to" style="width: 100%;">
                    <option value="">-- Select User --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($assigned_to, $user->ID); ?>>
                            <?php echo esc_html($user->display_name . ' (' . $user->user_email . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="kickoff_status">Status</label></th>
            <td>
                <select name="kickoff_status" id="kickoff_status" style="width: 100%;">
                    <option value="in_progress" <?php selected($status, 'in_progress'); ?>>In Progress</option>
                    <option value="not_applicable" <?php selected($status, 'not_applicable'); ?>>Not Applicable</option>
                    <option value="done" <?php selected($status, 'done'); ?>>Done</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>Last Updated</label></th>
            <td>
                <?php
                if ($last_updated_by) {
                    $updater = get_user_by('ID', $last_updated_by);
                    echo 'By: ' . ($updater ? esc_html($updater->display_name) : 'Unknown') . '<br>';
                }
                if ($last_updated_date) {
                    echo 'Date: <input type="datetime-local" name="kickoff_last_updated_date" value="' . esc_attr(date('Y-m-d\TH:i', strtotime($last_updated_date))) . '" style="margin-top: 5px;">';
                } else {
                    echo '<input type="datetime-local" name="kickoff_last_updated_date" value="" style="margin-top: 5px;">';
                }
                ?>
            </td>
        </tr>
    </table>
    <?php
}

// Save meta box data
add_action('save_post_seo_kickoff', 'wpwizards_save_kickoff_meta');

function wpwizards_save_kickoff_meta($post_id) {
    if (!isset($_POST['wpwizards_kickoff_nonce']) || !wp_verify_nonce($_POST['wpwizards_kickoff_nonce'], 'wpwizards_kickoff_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save assigned to
    if (isset($_POST['kickoff_assigned_to'])) {
        update_post_meta($post_id, '_kickoff_assigned_to', intval($_POST['kickoff_assigned_to']));
    }
    
    // Save status
    if (isset($_POST['kickoff_status'])) {
        $old_status = get_post_meta($post_id, '_kickoff_status', true);
        $new_status = sanitize_text_field($_POST['kickoff_status']);
        update_post_meta($post_id, '_kickoff_status', $new_status);
        
        // If status changed, update timestamp and user
        if ($old_status !== $new_status) {
            update_post_meta($post_id, '_kickoff_last_updated_by', get_current_user_id());
            
            // Handle custom timestamp if provided
            if (isset($_POST['kickoff_last_updated_date']) && !empty($_POST['kickoff_last_updated_date'])) {
                $custom_date = sanitize_text_field($_POST['kickoff_last_updated_date']);
                update_post_meta($post_id, '_kickoff_last_updated_date', date('Y-m-d H:i:s', strtotime($custom_date)));
            } else {
                update_post_meta($post_id, '_kickoff_last_updated_date', current_time('mysql'));
            }
        }
    }
    
    // Always update timestamp if explicitly set
    if (isset($_POST['kickoff_last_updated_date']) && !empty($_POST['kickoff_last_updated_date'])) {
        $custom_date = sanitize_text_field($_POST['kickoff_last_updated_date']);
        update_post_meta($post_id, '_kickoff_last_updated_date', date('Y-m-d H:i:s', strtotime($custom_date)));
        update_post_meta($post_id, '_kickoff_last_updated_by', get_current_user_id());
    }
}

// AJAX handler for quick save from Kickoff tab
add_action('wp_ajax_wpwizards_save_kickoff_task', 'wpwizards_ajax_save_kickoff_task');

function wpwizards_ajax_save_kickoff_task() {
    check_ajax_referer('wpwizards_kickoff_ajax', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'Insufficient permissions'));
    }
    
    $post_id = intval($_POST['task_id']);
    $assigned_to = isset($_POST['assigned_to']) ? intval($_POST['assigned_to']) : 0;
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'in_progress';
    $notes = isset($_POST['notes']) ? wp_kses_post($_POST['notes']) : '';
    $last_updated_date = isset($_POST['last_updated_date']) ? sanitize_text_field($_POST['last_updated_date']) : '';
    
    // Update post content (notes)
    wp_update_post(array(
        'ID' => $post_id,
        'post_content' => $notes
    ));
    
    // Update meta fields
    update_post_meta($post_id, '_kickoff_assigned_to', $assigned_to);
    update_post_meta($post_id, '_kickoff_status', $status);
    update_post_meta($post_id, '_kickoff_last_updated_by', get_current_user_id());
    
    if ($last_updated_date) {
        update_post_meta($post_id, '_kickoff_last_updated_date', date('Y-m-d H:i:s', strtotime($last_updated_date)));
    } else {
        update_post_meta($post_id, '_kickoff_last_updated_date', current_time('mysql'));
    }
    
    wp_send_json_success(array(
        'message' => 'Task saved successfully',
        'last_updated' => get_post_meta($post_id, '_kickoff_last_updated_date', true),
        'last_updated_by' => get_current_user_id()
    ));
}

// AJAX handler for bulk update
add_action('wp_ajax_wpwizards_bulk_update_kickoff_tasks', 'wpwizards_ajax_bulk_update_kickoff_tasks');

function wpwizards_ajax_bulk_update_kickoff_tasks() {
    check_ajax_referer('wpwizards_kickoff_ajax', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'Insufficient permissions'));
    }
    
    if (!isset($_POST['task_ids']) || !is_array($_POST['task_ids'])) {
        wp_send_json_error(array('message' => 'No tasks selected'));
    }
    
    $task_ids = array_map('intval', $_POST['task_ids']);
    $status = isset($_POST['status']) && !empty($_POST['status']) ? sanitize_text_field($_POST['status']) : null;
    $assigned_to = isset($_POST['assigned_to']) && !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null;
    
    if (!$status && !$assigned_to) {
        wp_send_json_error(array('message' => 'No changes specified'));
    }
    
    $updated_count = 0;
    $current_user_id = get_current_user_id();
    $current_time = current_time('mysql');
    
    foreach ($task_ids as $task_id) {
        // Verify task exists and user can edit it
        if (!current_user_can('edit_post', $task_id)) {
            continue;
        }
        
        $updated = false;
        
        // Update status if provided
        if ($status) {
            $old_status = get_post_meta($task_id, '_kickoff_status', true);
            update_post_meta($task_id, '_kickoff_status', $status);
            
            // If status changed, update timestamp and user
            if ($old_status !== $status) {
                update_post_meta($task_id, '_kickoff_last_updated_by', $current_user_id);
                update_post_meta($task_id, '_kickoff_last_updated_date', $current_time);
                $updated = true;
            }
        }
        
        // Update assigned to if provided
        if ($assigned_to) {
            update_post_meta($task_id, '_kickoff_assigned_to', $assigned_to);
            $updated = true;
        }
        
        // If any update was made, ensure timestamp is set
        if ($updated && !get_post_meta($task_id, '_kickoff_last_updated_date', true)) {
            update_post_meta($task_id, '_kickoff_last_updated_by', $current_user_id);
            update_post_meta($task_id, '_kickoff_last_updated_date', $current_time);
        }
        
        if ($updated) {
            $updated_count++;
        }
    }
    
    if ($updated_count > 0) {
        wp_send_json_success(array(
            'message' => sprintf('%d task%s updated successfully', $updated_count, $updated_count > 1 ? 's' : ''),
            'updated_count' => $updated_count
        ));
    } else {
        wp_send_json_error(array('message' => 'No tasks were updated'));
    }
}

// AJAX handler for bulk mark done
add_action('wp_ajax_wpwizards_bulk_mark_done', 'wpwizards_ajax_bulk_mark_done');

function wpwizards_ajax_bulk_mark_done() {
    check_ajax_referer('wpwizards_kickoff_ajax', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'Insufficient permissions'));
    }
    
    if (!isset($_POST['task_ids']) || !is_array($_POST['task_ids'])) {
        wp_send_json_error(array('message' => 'No tasks selected'));
    }
    
    if (!isset($_POST['completion_date']) || empty($_POST['completion_date'])) {
        wp_send_json_error(array('message' => 'Completion date is required'));
    }
    
    $task_ids = array_map('intval', $_POST['task_ids']);
    $completion_date = sanitize_text_field($_POST['completion_date']);
    $completion_timestamp = date('Y-m-d H:i:s', strtotime($completion_date));
    
    $current_user_id = get_current_user_id();
    $updated_count = 0;
    
    foreach ($task_ids as $task_id) {
        // Verify task exists and user can edit it
        if (!current_user_can('edit_post', $task_id)) {
            continue;
        }
        
        // Update status to done
        update_post_meta($task_id, '_kickoff_status', 'done');
        update_post_meta($task_id, '_kickoff_last_updated_by', $current_user_id);
        update_post_meta($task_id, '_kickoff_last_updated_date', $completion_timestamp);
        
        $updated_count++;
    }
    
    if ($updated_count > 0) {
        wp_send_json_success(array(
            'message' => sprintf('%d task%s marked as done', $updated_count, $updated_count > 1 ? 's' : ''),
            'updated_count' => $updated_count
        ));
    } else {
        wp_send_json_error(array('message' => 'No tasks were updated'));
    }
}

// AJAX handler for generating user permissions
add_action('wp_ajax_wpwizards_generate_user_permissions', 'wpwizards_ajax_generate_user_permissions');

function wpwizards_ajax_generate_user_permissions() {
    check_ajax_referer('wpwizards_kickoff_ajax', 'nonce');
    
    if (!current_user_can('create_users') || !current_user_can('delete_users')) {
        wp_send_json_error(array('message' => 'Insufficient permissions'));
    }
    
    $results = array(
        'created' => array(),
        'existing' => array(),
        'migrated' => array(),
        'deleted' => array()
    );
    
    // User mapping: new email => old email
    $user_mapping = array(
        'alexandra@wpwizards.com' => 'alex@pricelessconsultingllc.com',
        'mackenzie@wpwizards.com' => 'mackenzie@pricelessconsultingllc.com',
        'josh@wpwizards.com' => 'info@pricelessconsultingllc.com'
    );
    
    // Step 1: Create new users if they don't exist
    foreach ($user_mapping as $new_email => $old_email) {
        $new_user = get_user_by('email', $new_email);
        
        if ($new_user) {
            $results['existing'][] = $new_email;
        } else {
            // Extract name from email
            $name_parts = explode('@', $new_email);
            $username = $name_parts[0];
            $display_name = ucfirst($username);
            
            // Special handling for display names
            if ($username === 'alexandra') {
                $display_name = 'Alexandra';
            } elseif ($username === 'mackenzie') {
                $display_name = 'Mackenzie';
            } elseif ($username === 'josh') {
                $display_name = 'Josh';
            }
            
            // Ensure unique username
            $original_username = $username;
            $counter = 1;
            while (username_exists($username)) {
                $username = $original_username . $counter;
                $counter++;
            }
            
            // Create user
            $user_id = wp_create_user($username, wp_generate_password(20, true, true), $new_email);
            
            if (!is_wp_error($user_id)) {
                // Set user role to administrator
                $user = new WP_User($user_id);
                $user->set_role('administrator');
                
                // Set display name
                wp_update_user(array(
                    'ID' => $user_id,
                    'display_name' => $display_name,
                    'first_name' => $display_name
                ));
                
                // Send notification email
                wp_new_user_notification($user_id, null, 'both');
                
                $results['created'][] = $new_email;
            }
        }
    }
    
    // Step 2: Migrate content from old users to new users
    foreach ($user_mapping as $new_email => $old_email) {
        $old_user = get_user_by('email', $old_email);
        $new_user = get_user_by('email', $new_email);
        
        if ($old_user && $new_user) {
            $old_user_id = $old_user->ID;
            $new_user_id = $new_user->ID;
            
            // Migrate posts
            $posts = get_posts(array(
                'author' => $old_user_id,
                'post_type' => 'any',
                'posts_per_page' => -1,
                'post_status' => 'any'
            ));
            
            foreach ($posts as $post) {
                wp_update_post(array(
                    'ID' => $post->ID,
                    'post_author' => $new_user_id
                ));
            }
            
            // Migrate comments
            $comments = get_comments(array(
                'author_email' => $old_email,
                'number' => 0
            ));
            
            foreach ($comments as $comment) {
                wp_update_comment(array(
                    'comment_ID' => $comment->comment_ID,
                    'comment_author_email' => $new_email
                ));
            }
            
            // Migrate user meta (except core WordPress fields)
            $meta_keys = get_user_meta($old_user_id);
            foreach ($meta_keys as $key => $values) {
                if (!in_array($key, array('nickname', 'first_name', 'last_name', 'description', 'rich_editing', 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front', 'locale'))) {
                    foreach ($values as $value) {
                        add_user_meta($new_user_id, $key, $value);
                    }
                }
            }
            
            // Migrate SEO Kickoff task assignments
            $kickoff_tasks = get_posts(array(
                'post_type' => 'seo_kickoff',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_kickoff_assigned_to',
                        'value' => $old_user_id,
                        'compare' => '='
                    )
                )
            ));
            
            foreach ($kickoff_tasks as $task) {
                update_post_meta($task->ID, '_kickoff_assigned_to', $new_user_id);
            }
            
            // Migrate last_updated_by in kickoff tasks
            $kickoff_tasks_updated = get_posts(array(
                'post_type' => 'seo_kickoff',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_kickoff_last_updated_by',
                        'value' => $old_user_id,
                        'compare' => '='
                    )
                )
            ));
            
            foreach ($kickoff_tasks_updated as $task) {
                update_post_meta($task->ID, '_kickoff_last_updated_by', $new_user_id);
            }
            
            $results['migrated'][] = array(
                'old' => $old_email,
                'new' => $new_email
            );
            
            // Step 3: Delete old user (reassign content to new user)
            require_once(ABSPATH . 'wp-admin/includes/user.php');
            wp_delete_user($old_user_id, $new_user_id);
            $results['deleted'][] = $old_email;
        }
    }
    
    wp_send_json_success($results);
}

// Function to render Kickoff tab content
function wpwizards_kickoff_tab_content() {
    // Handle default tasks creation
    if (isset($_GET['create_default_tasks']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'create_default_tasks')) {
        wpwizards_create_default_hbcf_tasks();
        echo '<div class="notice notice-success is-dismissible"><p>Default tasks created successfully!</p></div>';
    }
    
    // Get all kickoff tasks
    $tasks = get_posts(array(
        'post_type' => 'seo_kickoff',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'ASC'
    ));
    
    // Sort tasks by status: In Progress first, Done second, Not Applicable last
    usort($tasks, function($a, $b) {
        $status_a = get_post_meta($a->ID, '_kickoff_status', true) ?: 'in_progress';
        $status_b = get_post_meta($b->ID, '_kickoff_status', true) ?: 'in_progress';
        
        // Define sort order: in_progress = 1, done = 2, not_applicable = 3
        $order = array(
            'in_progress' => 1,
            'done' => 2,
            'not_applicable' => 3
        );
        
        $order_a = isset($order[$status_a]) ? $order[$status_a] : 99;
        $order_b = isset($order[$status_b]) ? $order[$status_b] : 99;
        
        if ($order_a === $order_b) {
            // If same status, sort by title
            return strcmp($a->post_title, $b->post_title);
        }
        
        return $order_a - $order_b;
    });
    
    // Get users
    $users = get_users(array('orderby' => 'display_name'));
    
    // Get site URL for default notes
    $site_url = home_url();
    $site_url_clean = str_replace(array('https://', 'http://'), '', $site_url);
    
    ?>
    <div class="wpwizards-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <h2 style="margin: 0;">SEO Kickoff Tasks</h2>
                <label style="display: flex; align-items: center; gap: 5px; font-weight: normal; cursor: pointer;">
                    <input type="checkbox" id="select-all-tasks" style="margin: 0; cursor: pointer;">
                    <span>Select All</span>
                </label>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="button" class="button button-secondary" id="generate-user-permissions-btn">Generate User Permissions</button>
                <a href="<?php echo esc_url(admin_url('post-new.php?post_type=seo_kickoff')); ?>" class="button button-primary">Add New Task</a>
            </div>
        </div>
        
        <!-- User Permissions Modal -->
        <div id="user-permissions-modal">
            <div>
                <h3 style="margin-top: 0;">User Permissions Generation Results</h3>
                <div id="user-permissions-results" style="margin: 20px 0;">
                    <!-- Results will be displayed here -->
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="button button-primary" id="close-user-permissions-modal">Close</button>
                </div>
            </div>
        </div>
        
        <?php if (empty($tasks)): ?>
            <div class="wpwizards-info-box">
                <p>No tasks found. <a href="<?php echo esc_url(admin_url('post-new.php?post_type=seo_kickoff')); ?>">Create your first task</a> or use the button below to generate default tasks.</p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wpwizards-settings&create_default_tasks=1&_wpnonce=' . wp_create_nonce('create_default_tasks'))); ?>" class="button button-secondary" style="margin-top: 10px;">Generate Defaults</a>
            </div>
        <?php else: ?>
            <div class="wpwizards-kickoff-bulk-actions" style="background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; display: none;">
                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <strong style="margin-right: 10px;">Bulk Actions:</strong>
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <span>Status:</span>
                        <select id="bulk-status" style="min-width: 150px;">
                            <option value="">-- No Change --</option>
                            <option value="in_progress">In Progress</option>
                            <option value="not_applicable">Not Applicable</option>
                            <option value="done">Done</option>
                        </select>
                    </label>
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <span>Assign To:</span>
                        <select id="bulk-assigned-to" style="min-width: 150px;">
                            <option value="">-- No Change --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo esc_attr($user->ID); ?>">
                                    <?php echo esc_html($user->display_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <button type="button" class="button button-primary" id="bulk-apply-btn">Apply to Selected</button>
                    <button type="button" class="button button-secondary" id="bulk-mark-done-btn" style="background: #155724; color: #fff; border-color: #155724;">Mark Done</button>
                    <button type="button" class="button" id="bulk-cancel-btn">Cancel</button>
                    <span id="bulk-selected-count" style="color: #666; margin-left: auto;"></span>
                </div>
            </div>
            
            <!-- Date Picker Modal for Mark Done -->
            <div id="mark-done-modal">
                <div>
                    <h3 style="margin-top: 0;">Set Completion Date</h3>
                    <p style="color: #666; margin-bottom: 20px;">Enter the date and time when these tasks were completed:</p>
                    <input type="datetime-local" id="completion-date-input" style="width: 100%; padding: 8px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;" value="<?php echo esc_attr(date('Y-m-d\TH:i')); ?>">
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="button" id="cancel-completion-date">Cancel</button>
                        <button type="button" class="button button-primary" id="confirm-completion-date" style="background: #155724; color: #fff; border-color: #155724;">Mark Done</button>
                    </div>
                </div>
            </div>
            <div class="wpwizards-kickoff-tasks">
                <?php foreach ($tasks as $task): 
                    $assigned_to = get_post_meta($task->ID, '_kickoff_assigned_to', true);
                    $status = get_post_meta($task->ID, '_kickoff_status', true) ?: 'in_progress';
                    $last_updated_by = get_post_meta($task->ID, '_kickoff_last_updated_by', true);
                    $last_updated_date = get_post_meta($task->ID, '_kickoff_last_updated_date', true);
                    $assigned_user = $assigned_to ? get_user_by('ID', $assigned_to) : null;
                    $updater_user = $last_updated_by ? get_user_by('ID', $last_updated_by) : null;
                ?>
                    <div class="wpwizards-kickoff-task" data-task-id="<?php echo esc_attr($task->ID); ?>">
                        <div class="wpwizards-kickoff-task-header" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                                <input type="checkbox" class="wpwizards-task-checkbox" value="<?php echo esc_attr($task->ID); ?>" style="margin: 0; cursor: pointer;" onclick="event.stopPropagation();">
                                <div style="flex: 1;">
                                    <h3 style="margin: 0; display: inline;"><?php echo esc_html($task->post_title); ?></h3>
                                    <?php if ($assigned_user): ?>
                                        <span style="color: #666; font-size: 13px; margin-left: 10px;">• Assigned to: <?php echo esc_html($assigned_user->display_name); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php
                                    // Extract first URL from notes
                                    $notes = $task->post_content;
                                    $link_url = '';
                                    if (!empty($notes)) {
                                        // Look for URLs in the content (handles both plain URLs and HTML links)
                                        if (preg_match('/https?:\/\/[^\s<>"\'\)]+/i', strip_tags($notes), $matches)) {
                                            $link_url = esc_url($matches[0]);
                                        }
                                    }
                                    
                                    // Show link button if URL found
                                    if ($link_url):
                                    ?>
                                        <a href="<?php echo $link_url; ?>" target="_blank" class="button button-small" style="font-size: 11px; padding: 4px 8px; text-decoration: none; white-space: nowrap;" onclick="event.stopPropagation();" title="Open link from notes">
                                            🔗 Open Link
                                        </a>
                                    <?php endif; ?>
                                    <span class="wpwizards-kickoff-status status-<?php echo esc_attr($status); ?>">
                                        <?php 
                                        $status_labels = array(
                                            'in_progress' => 'In Progress',
                                            'not_applicable' => 'Not Applicable',
                                            'done' => 'Done'
                                        );
                                        echo esc_html($status_labels[$status] ?? ucfirst($status));
                                        ?>
                                    </span>
                                    <span class="wpwizards-kickoff-toggle" style="font-size: 14px; color: #666;">▼</span>
                                </div>
                                <?php if ($last_updated_date): ?>
                                    <span style="color: #999; font-size: 11px;">
                                        <?php if ($status === 'done'): ?>
                                            Completed: <?php echo esc_html(date('M j, Y g:i A', strtotime($last_updated_date))); ?>
                                        <?php else: ?>
                                            Updated: <?php echo esc_html(date('M j, Y g:i A', strtotime($last_updated_date))); ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="wpwizards-kickoff-task-body" style="display: none;">
                            <div class="wpwizards-kickoff-field">
                                <label>Assigned To:</label>
                                <select name="assigned_to" class="wpwizards-kickoff-assigned-to" style="width: 100%;">
                                    <option value="">-- Select User --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($assigned_to, $user->ID); ?>>
                                            <?php echo esc_html($user->display_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="wpwizards-kickoff-field">
                                <label>Status:</label>
                                <select name="status" class="wpwizards-kickoff-status-select" style="width: 100%;">
                                    <option value="in_progress" <?php selected($status, 'in_progress'); ?>>In Progress</option>
                                    <option value="not_applicable" <?php selected($status, 'not_applicable'); ?>>Not Applicable</option>
                                    <option value="done" <?php selected($status, 'done'); ?>>Done</option>
                                </select>
                            </div>
                            
                            <div class="wpwizards-kickoff-field">
                                <label>Notes:</label>
                                <?php
                                wp_editor($task->post_content, 'kickoff_notes_' . $task->ID, array(
                                    'textarea_name' => 'notes',
                                    'textarea_rows' => 5,
                                    'media_buttons' => true,
                                    'teeny' => false,
                                    'quicktags' => true
                                ));
                                ?>
                            </div>
                            
                            <div class="wpwizards-kickoff-field">
                                <label>Last Updated:</label>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <input type="datetime-local" 
                                           name="last_updated_date" 
                                           class="wpwizards-kickoff-last-updated" 
                                           value="<?php echo $last_updated_date ? esc_attr(date('Y-m-d\TH:i', strtotime($last_updated_date))) : ''; ?>"
                                           style="flex: 1;">
                                    <?php if ($updater_user): ?>
                                        <span style="color: #666; font-size: 13px;">
                                            by <?php echo esc_html($updater_user->display_name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="wpwizards-kickoff-actions">
                                <button type="button" class="button button-primary wpwizards-save-kickoff-task" data-task-id="<?php echo esc_attr($task->ID); ?>">
                                    Save Changes
                                </button>
                                <a href="<?php echo esc_url(get_edit_post_link($task->ID)); ?>" class="button">Edit Full Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <style>
        .wpwizards-kickoff-tasks {
            display: grid;
            gap: 20px;
        }
        .wpwizards-kickoff-task {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .wpwizards-kickoff-task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            user-select: none;
        }
        .wpwizards-kickoff-task-header:hover {
            background: #f9f9f9;
            margin: -20px -20px 0 -20px;
            padding: 20px 20px 15px 20px;
            border-radius: 8px 8px 0 0;
        }
        .wpwizards-kickoff-task-header h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .wpwizards-kickoff-task.expanded .wpwizards-kickoff-task-body {
            display: block !important;
            margin-top: 20px;
        }
        .wpwizards-kickoff-task.expanded .wpwizards-kickoff-toggle {
            transform: rotate(180deg);
        }
        .wpwizards-kickoff-toggle {
            transition: transform 0.2s ease;
        }
        .wpwizards-kickoff-task-body {
            transition: all 0.2s ease;
        }
        .wpwizards-kickoff-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .wpwizards-kickoff-status.status-in_progress {
            background: #fff3cd;
            color: #856404;
        }
        .wpwizards-kickoff-status.status-not_applicable {
            background: #e2e3e5;
            color: #383d41;
        }
        .wpwizards-kickoff-status.status-done {
            background: #d4edda;
            color: #155724;
        }
        .wpwizards-kickoff-field {
            margin-bottom: 20px;
        }
        .wpwizards-kickoff-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }
        .wpwizards-kickoff-actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
        .wpwizards-kickoff-task .wp-switch-editor {
            display: none !important;
        }
        .wpwizards-kickoff-task.selected {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
        }
        .wpwizards-kickoff-bulk-actions {
            position: sticky;
            top: 32px;
            z-index: 10;
        }
        #mark-done-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 100000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        #mark-done-modal > div {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        #user-permissions-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 100000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        #user-permissions-modal > div {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Toggle task card collapse/expand
        $('.wpwizards-kickoff-task-header').on('click', function(e) {
            // Don't toggle if clicking on checkbox, status badge, or any interactive element
            if ($(e.target).is('input[type="checkbox"]') ||
                $(e.target).hasClass('wpwizards-kickoff-status') || 
                $(e.target).closest('.wpwizards-kickoff-status').length ||
                $(e.target).closest('input[type="checkbox"]').length) {
                return;
            }
            var $task = $(this).closest('.wpwizards-kickoff-task');
            $task.toggleClass('expanded');
            var $body = $task.find('.wpwizards-kickoff-task-body');
            $body.slideToggle(200);
        });
        
        // Prevent task body clicks from toggling the card
        $('.wpwizards-kickoff-task-body').on('click', function(e) {
            e.stopPropagation();
        });
        
        // Bulk selection functionality
        var $bulkActions = $('.wpwizards-kickoff-bulk-actions');
        var $selectAll = $('#select-all-tasks');
        var $taskCheckboxes = $('.wpwizards-task-checkbox');
        
        // Update bulk actions visibility and count
        function updateBulkActions() {
            if (!$bulkActions || $bulkActions.length === 0) {
                console.error('Bulk actions element not found');
                return;
            }
            var selectedCount = $taskCheckboxes.filter(':checked').length;
            if (selectedCount > 0) {
                $bulkActions.slideDown(200);
                $('#bulk-selected-count').text(selectedCount + ' task' + (selectedCount > 1 ? 's' : '') + ' selected');
            } else {
                $bulkActions.slideUp(200);
            }
        }
        
        // Initialize - check if any are already selected on page load
        setTimeout(function() {
            if ($taskCheckboxes.filter(':checked').length > 0) {
                updateBulkActions();
            }
        }, 100);
        
        // Select All checkbox
        $selectAll.on('change', function() {
            var isChecked = $(this).prop('checked');
            $taskCheckboxes.prop('checked', isChecked);
            
            // Update visual selection
            $taskCheckboxes.each(function() {
                var $task = $(this).closest('.wpwizards-kickoff-task');
                if (isChecked) {
                    $task.addClass('selected');
                } else {
                    $task.removeClass('selected');
                }
            });
            
            updateBulkActions();
        });
        
        // Shift-click selection (select range between checkboxes)
        var lastCheckedIndex = -1;
        var lastCheckedState = false;
        
        $taskCheckboxes.on('click', function(e) {
            var $checkbox = $(this);
            var currentIndex = $taskCheckboxes.index(this);
            var currentState = $checkbox.prop('checked');
            
            if (e.shiftKey && lastCheckedIndex !== -1 && lastCheckedIndex !== currentIndex) {
                e.preventDefault();
                e.stopPropagation();
                
                // Use the state of the last clicked checkbox (not the current one)
                var targetState = lastCheckedState;
                
                // Get the range
                var start = Math.min(lastCheckedIndex, currentIndex);
                var end = Math.max(lastCheckedIndex, currentIndex);
                
                // Select all checkboxes in range with the target state
                $taskCheckboxes.slice(start, end + 1).prop('checked', targetState);
                
                // Trigger change event to update visuals
                $taskCheckboxes.slice(start, end + 1).trigger('change');
                
                updateBulkActions();
                
                // Update select all state
                var allChecked = $taskCheckboxes.length === $taskCheckboxes.filter(':checked').length;
                $selectAll.prop('checked', allChecked);
            } else {
                // Update last checked index and state for next shift-click
                lastCheckedIndex = currentIndex;
                lastCheckedState = currentState;
            }
        });
        
        // Individual checkbox change
        $taskCheckboxes.on('change', function() {
            var $checkbox = $(this);
            var $task = $checkbox.closest('.wpwizards-kickoff-task');
            
            // Update visual selection
            if ($checkbox.prop('checked')) {
                $task.addClass('selected');
            } else {
                $task.removeClass('selected');
            }
            
            updateBulkActions();
            
            // Update select all state
            var allChecked = $taskCheckboxes.length === $taskCheckboxes.filter(':checked').length;
            $selectAll.prop('checked', allChecked);
            
            // Update last checked index
            lastCheckedIndex = $taskCheckboxes.index(this);
        });
        
        // Cancel bulk actions
        $('#bulk-cancel-btn').on('click', function() {
            $taskCheckboxes.prop('checked', false);
            $selectAll.prop('checked', false);
            updateBulkActions();
        });
        
        // Mark Done button - show date picker modal
        $('#bulk-mark-done-btn').on('click', function() {
            var selectedTasks = $taskCheckboxes.filter(':checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedTasks.length === 0) {
                alert('Please select at least one task.');
                return;
            }
            
            // Show modal
            $('#mark-done-modal').css('display', 'flex');
            $('#completion-date-input').focus();
        });
        
        // Cancel completion date
        $('#cancel-completion-date').on('click', function() {
            $('#mark-done-modal').hide();
        });
        
        // Close modal when clicking outside
        $('#mark-done-modal').on('click', function(e) {
            if ($(e.target).is('#mark-done-modal')) {
                $(this).hide();
            }
        });
        
        // Confirm completion date and mark tasks as done
        $('#confirm-completion-date').on('click', function() {
            var selectedTasks = $taskCheckboxes.filter(':checked').map(function() {
                return $(this).val();
            }).get();
            
            var completionDate = $('#completion-date-input').val();
            
            if (!completionDate) {
                alert('Please enter a completion date.');
                return;
            }
            
            var $button = $(this);
            $button.prop('disabled', true).text('Marking Done...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpwizards_bulk_mark_done',
                    nonce: '<?php echo wp_create_nonce('wpwizards_kickoff_ajax'); ?>',
                    task_ids: selectedTasks,
                    completion_date: completionDate
                },
                success: function(response) {
                    if (response.success) {
                        // Reload page to show updated statuses
                        location.reload();
                    } else {
                        alert('Error: ' + (response.data.message || 'Failed to mark tasks as done'));
                        $button.prop('disabled', false).text('Mark Done');
                        $('#mark-done-modal').hide();
                    }
                },
                error: function() {
                    alert('An error occurred while marking tasks as done.');
                    $button.prop('disabled', false).text('Mark Done');
                    $('#mark-done-modal').hide();
                }
            });
        });
        
        // Apply bulk actions
        $('#bulk-apply-btn').on('click', function() {
            var selectedTasks = $taskCheckboxes.filter(':checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedTasks.length === 0) {
                alert('Please select at least one task.');
                return;
            }
            
            var bulkStatus = $('#bulk-status').val();
            var bulkAssignedTo = $('#bulk-assigned-to').val();
            
            if (!bulkStatus && !bulkAssignedTo) {
                alert('Please select at least one action (Status or Assign To).');
                return;
            }
            
            var $button = $(this);
            $button.prop('disabled', true).text('Applying...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpwizards_bulk_update_kickoff_tasks',
                    nonce: '<?php echo wp_create_nonce('wpwizards_kickoff_ajax'); ?>',
                    task_ids: selectedTasks,
                    status: bulkStatus,
                    assigned_to: bulkAssignedTo
                },
                success: function(response) {
                    if (response.success) {
                        // Reload page to show updated statuses
                        location.reload();
                    } else {
                        alert('Error: ' + (response.data.message || 'Failed to update tasks'));
                        $button.prop('disabled', false).text('Apply to Selected');
                    }
                },
                error: function() {
                    alert('An error occurred while updating tasks.');
                    $button.prop('disabled', false).text('Apply to Selected');
                }
            });
        });
        
        // Generate User Permissions button
        $('#generate-user-permissions-btn').on('click', function() {
            if (!confirm('This will create administrator accounts and migrate content from old users. Continue?')) {
                return;
            }
            
            var $button = $(this);
            $button.prop('disabled', true).text('Generating...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpwizards_generate_user_permissions',
                    nonce: '<?php echo wp_create_nonce('wpwizards_kickoff_ajax'); ?>'
                },
                success: function(response) {
                    $button.prop('disabled', false).text('Generate User Permissions');
                    
                    if (response.success) {
                        var results = response.data;
                        var html = '<div style="line-height: 1.8;">';
                        
                        if (results.created && results.created.length > 0) {
                            html += '<h4 style="color: #155724; margin-top: 0;">✅ Created Users:</h4><ul style="margin-left: 20px;">';
                            results.created.forEach(function(email) {
                                html += '<li>' + email + '</li>';
                            });
                            html += '</ul>';
                        }
                        
                        if (results.existing && results.existing.length > 0) {
                            html += '<h4 style="color: #856404; margin-top: 15px;">ℹ️ Already Exists:</h4><ul style="margin-left: 20px;">';
                            results.existing.forEach(function(email) {
                                html += '<li>' + email + '</li>';
                            });
                            html += '</ul>';
                        }
                        
                        if (results.migrated && results.migrated.length > 0) {
                            html += '<h4 style="color: #004085; margin-top: 15px;">🔄 Migrated Content:</h4><ul style="margin-left: 20px;">';
                            results.migrated.forEach(function(migration) {
                                html += '<li>' + migration.old + ' → ' + migration.new + '</li>';
                            });
                            html += '</ul>';
                        }
                        
                        if (results.deleted && results.deleted.length > 0) {
                            html += '<h4 style="color: #721c24; margin-top: 15px;">🗑️ Deleted Old Users:</h4><ul style="margin-left: 20px;">';
                            results.deleted.forEach(function(email) {
                                html += '<li>' + email + '</li>';
                            });
                            html += '</ul>';
                        }
                        
                        if (results.created.length === 0 && results.existing.length === 0 && results.migrated.length === 0 && results.deleted.length === 0) {
                            html += '<p>No changes were made. All users already exist and no old users were found to migrate.</p>';
                        }
                        
                        html += '</div>';
                        $('#user-permissions-results').html(html);
                        $('#user-permissions-modal').css('display', 'flex');
                    } else {
                        alert('Error: ' + (response.data.message || 'Failed to generate user permissions'));
                    }
                },
                error: function() {
                    $button.prop('disabled', false).text('Generate User Permissions');
                    alert('An error occurred while generating user permissions.');
                }
            });
        });
        
        // Close user permissions modal
        $('#close-user-permissions-modal, #user-permissions-modal').on('click', function(e) {
            if ($(e.target).is('#user-permissions-modal') || $(e.target).is('#close-user-permissions-modal')) {
                $('#user-permissions-modal').hide();
            }
        });
        
        // Save task via AJAX
        $('.wpwizards-save-kickoff-task').on('click', function(e) {
            e.stopPropagation(); // Prevent header click
            var $button = $(this);
            var taskId = $button.data('task-id');
            var $task = $button.closest('.wpwizards-kickoff-task');
            
            var assignedTo = $task.find('.wpwizards-kickoff-assigned-to').val();
            var status = $task.find('.wpwizards-kickoff-status-select').val();
            var notes = '';
            
            // Get notes from editor
            var editorId = 'kickoff_notes_' + taskId;
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get(editorId)) {
                notes = tinyMCE.get(editorId).getContent();
            } else {
                notes = $('#' + editorId).val();
            }
            
            var lastUpdatedDate = $task.find('.wpwizards-kickoff-last-updated').val();
            
            $button.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpwizards_save_kickoff_task',
                    nonce: '<?php echo wp_create_nonce('wpwizards_kickoff_ajax'); ?>',
                    task_id: taskId,
                    assigned_to: assignedTo,
                    status: status,
                    notes: notes,
                    last_updated_date: lastUpdatedDate
                },
                success: function(response) {
                    if (response.success) {
                        $button.text('Saved!').removeClass('button-primary').addClass('button-secondary');
                        setTimeout(function() {
                            $button.text('Save Changes').removeClass('button-secondary').addClass('button-primary');
                        }, 2000);
                        
                        // Update status badge
                        var statusLabels = {
                            'in_progress': 'In Progress',
                            'not_applicable': 'Not Applicable',
                            'done': 'Done'
                        };
                        $task.find('.wpwizards-kickoff-status')
                            .removeClass('status-in_progress status-not_applicable status-done')
                            .addClass('status-' + status)
                            .text(statusLabels[status]);
                    } else {
                        alert('Error: ' + (response.data.message || 'Failed to save'));
                        $button.prop('disabled', false).text('Save Changes');
                    }
                },
                error: function() {
                    alert('Error: Failed to save task');
                    $button.prop('disabled', false).text('Save Changes');
                }
            });
        });
    });
    </script>
    <?php
}

// Function to create default HBCF tasks
function wpwizards_create_default_hbcf_tasks() {
    // Check if tasks already exist
    $existing_tasks = get_posts(array(
        'post_type' => 'seo_kickoff',
        'posts_per_page' => 1,
        'post_status' => 'any'
    ));
    
    if (!empty($existing_tasks)) {
        return; // Don't create duplicates
    }
    
    $site_url = home_url();
    $site_url_clean = str_replace(array('https://', 'http://'), '', $site_url);
    
    // Get default users - try multiple email variations
    $alex_user = get_user_by('email', 'alexandra@wpwizards.com');
    if (!$alex_user) {
        // Try alternative email format
        $alex_user = get_user_by('email', 'alexandra.crabill@wpwizards.com');
    }
    $josh_user = get_user_by('email', 'josh@wpwizards.com');
    if (!$josh_user) {
        // Try alternative email format
        $josh_user = get_user_by('email', 'josh.chretien@wpwizards.com');
    }
    
    // Default HBCF tasks from spreadsheet
    // Format: 'title' => array('assigned' => 'alex'|'josh', 'notes' => 'default notes', 'status' => 'in_progress'|'done'|'not_applicable')
    $default_tasks = array(
        'Add ALT Tags' => array(
            'assigned' => 'alex',
            'notes' => '',
            'status' => 'done'
        ),
        'Analyze Existing Content for Improvement' => array(
            'assigned' => 'alex',
            'notes' => '',
            'status' => 'done'
        ),
        'Update Meta Page Titles & Descriptions' => array(
            'assigned' => 'alex',
            'notes' => '',
            'status' => 'done'
        ),
        'Setup Blog Email Template - If On Plan' => array(
            'assigned' => 'alex',
            'notes' => 'On Omnisend "Blog Email Template" -- https://docs.google.com/document/d/1-11-PBCG0z4h/fcc0XnKt_MXhJqQZUACIcZQ6tm-6LU/edit?usp=sharing',
            'status' => 'done'
        ),
        'Keywords Research' => array(
            'assigned' => 'alex',
            'notes' => '',
            'status' => 'done'
        ),
        'Competitor Research' => array(
            'assigned' => 'alex',
            'notes' => '',
            'status' => 'done'
        ),
        'Implement Ahrefs Keywords Tracking' => array(
            'assigned' => 'alex',
            'notes' => '',
            'status' => 'done'
        ),
        'Install Instant Indexing Plugin' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Install Instant Images Plugin' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Full SEO Site Audit' => array(
            'assigned' => 'josh',
            'notes' => 'https://www.seoptimer.com/' . $site_url_clean,
            'status' => 'done'
        ),
        'Install Google Analytics' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Implement Heatmaps/Recordings' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Create XML Sitemaps' => array(
            'assigned' => 'josh',
            'notes' => 'https://' . $site_url_clean . '/sitemap_index.xml',
            'status' => 'done'
        ),
        'Update Copyright Footer Dates' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Check Redirect Errors' => array(
            'assigned' => 'josh',
            'notes' => 'https://www.serpworx.com/check-301-redirects/?url=https%3A%2F%2F' . urlencode($site_url_clean) . '%2F',
            'status' => 'done'
        ),
        'Setup Custom SEO Dashboard and Client Access' => array(
            'assigned' => 'josh',
            'notes' => 'https://dashboard.wpwizards.com/',
            'status' => 'done'
        ),
        'Setup Google Search Console' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Setup 303 Redirect (http to https)' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Install RankMath SEO Plugin' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Install Media Library Helper by Codexin Plugin' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Install Wordfence Security Plugin' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Implement Schema Markup' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'in_progress'
        ),
        'Build Customer Avatar' => array(
            'assigned' => 'josh',
            'notes' => 'Customer Avatar: "The Celebration-Driven Local Buyer"',
            'status' => 'done'
        ),
        'Build Influencer Avatar' => array(
            'assigned' => 'josh',
            'notes' => 'Influencer Avatar: "The Local Foodie & Celebration Storyteller"',
            'status' => 'done'
        ),
        'Check Duplicate Headers' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Check Orphan Pages' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Claim your Google My Business Page' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Add Location to Google Maps' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Add Business to Local Directories (per plan)' => array(
            'assigned' => 'josh',
            'notes' => 'Gave Premium',
            'status' => 'done'
        ),
        'Install Facebook Pixel' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'not_applicable'
        ),
        'Improve Page URL\'s' => array(
            'assigned' => 'josh',
            'notes' => 'Permalink structure already well established. Will not alter.',
            'status' => 'in_progress'
        ),
        'Add Social Share Buttons' => array(
            'assigned' => 'josh',
            'notes' => '',
            'status' => 'done'
        ),
        'Setup Auto Posts (Linkedin/Facebook)' => array(
            'assigned' => 'josh',
            'notes' => 'Facebook Done - No Existing LinkedIn',
            'status' => 'in_progress'
        ),
        'Create Client Folder' => array(
            'assigned' => 'josh',
            'notes' => 'https://www.dropbox.com/scl/fo/ulqttjkfkrhz7ocwnq8kt/AGLmtMJq5VdV_gw_eoTalk?rlkey=t7sxghj849vtvn17arzfwgbld&st=0k8rslca&dl=0',
            'status' => 'done'
        ),
    );
    
    foreach ($default_tasks as $title => $task_data) {
        // Assign all tasks to alexandra@wpwizards.com as requested
        $assigned_user_id = 0;
        if ($alex_user) {
            $assigned_user_id = $alex_user->ID;
        } elseif ($josh_user) {
            // Fallback to Josh if Alex user not found
            $assigned_user_id = $josh_user->ID;
        }
        
        // Process notes - replace site URL placeholders
        $notes = $task_data['notes'];
        if (strpos($notes, '{site_url}') !== false) {
            $notes = str_replace('{site_url}', $site_url_clean, $notes);
        }
        
        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_content' => $notes,
            'post_type' => 'seo_kickoff',
            'post_status' => 'publish'
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_kickoff_assigned_to', $assigned_user_id);
            update_post_meta($post_id, '_kickoff_status', $task_data['status']);
            
            // Debug: Log assignment issue if user not found
            if ($assigned_user_id == 0) {
                error_log('WP Wizards: Could not find user alexandra@wpwizards.com for task: ' . $title . '. Alex user exists: ' . ($alex_user ? 'yes (ID: ' . $alex_user->ID . ')' : 'no'));
            }
        }
    }
}

/* --------------------------------------------------
   THEME ACTIVATION - COPY CUSTOM CODE FROM PREVIOUS THEME
-------------------------------------------------- */
add_action('after_switch_theme', 'wpwizards_copy_previous_theme_customizations');

function wpwizards_copy_previous_theme_customizations() {
    // Get the previous theme
    $previous_theme = get_option('theme_switched');
    if (!$previous_theme) {
        return;
    }
    
    // Get previous theme's functions.php path
    $previous_theme_path = get_theme_root() . '/' . $previous_theme;
    $previous_functions = $previous_theme_path . '/functions.php';
    
    // If previous theme's functions.php doesn't exist, skip
    if (!file_exists($previous_functions)) {
        return;
    }
    
    // Read previous theme's functions.php
    $previous_content = file_get_contents($previous_functions);
    if (empty($previous_content)) {
        return;
    }
    
    // Get our current functions.php to compare
    $current_functions = get_stylesheet_directory() . '/functions.php';
    $current_content = file_get_contents($current_functions);
    
    // Extract custom code from previous theme
    $custom_code = wpwizards_extract_custom_code($previous_content, $current_content);
    
    if (empty($custom_code)) {
        return;
    }
    
    // Get client customizations file path
    $client_customizations = get_stylesheet_directory() . '/client-customizations.php';
    $client_customizations_example = get_stylesheet_directory() . '/client-customizations.php.example';
    
    // Create file from example if it doesn't exist
    if (!file_exists($client_customizations) && file_exists($client_customizations_example)) {
        copy($client_customizations_example, $client_customizations);
    }
    
    // If file exists, append custom code
    if (file_exists($client_customizations)) {
        $existing_content = file_get_contents($client_customizations);
        
        // Check if custom code is already there
        if (strpos($existing_content, '// Custom code migrated from previous theme') === false) {
            $migration_header = "\n\n// ============================================\n";
            $migration_header .= "// Custom code migrated from previous theme\n";
            $migration_header .= "// Date: " . date('Y-m-d H:i:s') . "\n";
            $migration_header .= "// Previous theme: " . $previous_theme . "\n";
            $migration_header .= "// ============================================\n\n";
            
            file_put_contents($client_customizations, $existing_content . $migration_header . $custom_code, LOCK_EX);
        }
    }
}

function wpwizards_extract_custom_code($previous_content, $current_content) {
    // Remove PHP opening tag and whitespace
    $previous_content = preg_replace('/^<\?php\s*/', '', $previous_content);
    $previous_content = trim($previous_content);
    
    // Split into lines
    $previous_lines = explode("\n", $previous_content);
    
    // Common WordPress/theme patterns to ignore
    $ignore_patterns = array(
        '/^\s*\/\*/',  // Comments
        '/^\s*\*\//',
        '/^\s*\*/',
        '/^\s*\/\//',
        '/^\s*require_once/',
        '/^\s*require /',
        '/^\s*include_once/',
        '/^\s*include /',
        '/^\s*add_action\(/',
        '/^\s*add_filter\(/',
        '/^\s*function\s+wp_/',
        '/^\s*function\s+get_/',
        '/^\s*function\s+the_/',
        '/^\s*function\s+is_/',
        '/^\s*function\s+wp_enqueue/',
        '/^\s*function\s+wp_register/',
        '/^\s*function\s+register_/',
        '/^\s*function\s+add_theme_support/',
        '/^\s*function\s+add_image_size/',
        '/^\s*function\s+set_post_thumbnail_size/',
        '/^\s*function\s+wp_nav_menu/',
        '/^\s*function\s+wp_head/',
        '/^\s*function\s+wp_footer/',
        '/^\s*function\s+enqueue_/',
        '/^\s*function\s+wpwizards_/',  // Our own functions
        '/^\s*function\s+my_theme_/',  // TGM functions
        '/^\s*function\s+tgmpa_/',
        '/^\s*class\s+WPW_/',  // Our classes
        '/^\s*class\s+TGM_/',  // TGM classes
        '/^\s*if\s*\(!class_exists/',
        '/^\s*if\s*\(!function_exists/',
        '/^\s*if\s*\(!defined/',
        '/^\s*define\s*\(/',
        '/^\s*\$wp_meta_boxes/',
        '/^\s*global\s+\$wp_meta_boxes/',
    );
    
    $custom_lines = array();
    $in_function = false;
    $function_lines = array();
    
    foreach ($previous_lines as $line) {
        $trimmed = trim($line);
        
        // Skip empty lines and comments at start
        if (empty($trimmed) || preg_match('/^\/\//', $trimmed) || preg_match('/^\/\*/', $trimmed)) {
            continue;
        }
        
        // Check if this is a function definition
        if (preg_match('/^function\s+(\w+)\s*\(/', $trimmed, $matches)) {
            $function_name = $matches[1];
            
            // Skip if it's a WordPress core function or our function
            $skip = false;
            foreach ($ignore_patterns as $pattern) {
                if (preg_match($pattern, $trimmed)) {
                    $skip = true;
                    break;
                }
            }
            
            // Also skip if function exists in current theme
            if (!$skip && strpos($current_content, "function {$function_name}") !== false) {
                $skip = true;
            }
            
            if (!$skip) {
                $in_function = true;
                $function_lines = array($line);
            } else {
                $in_function = false;
            }
        } elseif ($in_function) {
            $function_lines[] = $line;
            
            // Check if function ends
            if (preg_match('/^}/', $trimmed)) {
                $custom_lines = array_merge($custom_lines, $function_lines);
                $custom_lines[] = ''; // Add blank line between functions
                $in_function = false;
                $function_lines = array();
            }
        } elseif (!empty($trimmed)) {
            // Check if it's custom code (not in ignore patterns and not in current theme)
            $is_ignored = false;
            foreach ($ignore_patterns as $pattern) {
                if (preg_match($pattern, $trimmed)) {
                    $is_ignored = true;
                    break;
                }
            }
            
            if (!$is_ignored && strpos($current_content, $trimmed) === false) {
                $custom_lines[] = $line;
            }
        }
    }
    
    return implode("\n", $custom_lines);
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