<?php
/*
 * Plugin Name: Blogs to Nav Menus
 * Plugin URI: http://hurtigtechnologies.com/plugins/blogs-to-nav-menus
 * Description: Adds a meta box to the Menu Editor that allows users to add links to specific blogs in the network
 * Author Eddie Hurtig <hurtige@ccs.neu.edu>
 * Version: 0.1-alpha
 * Author URI: http://hurtigtechnologies.com
 */

/**
 * Adds a meta box to the Menu Editor that allows users to add links to specific blogs in the network
 *
 * @author  Eddie Hurtig <hurtige@sudbury.ma.us>
 */

/**
 * Class Sudbury_Nav_Menu_Departments_Meta_Box
 *
 * The class that handles the Nav Menu meta box for adding Sites to a nav menu
 */
class Blogs_To_Nav_Menu_Meta_Box {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pagenow;
		global $blog_id;

		// only register when on nav menus page and only show on subsites (I don't remember why)
		if ( 'nav-menus.php' !== $pagenow && $blog_id != 1 ) {
			return;
		}

		$this->add_meta_box();
	}

	/**
	 * Adds the meta box container
	 */
	public function add_meta_box() {
		add_meta_box(
			'info_meta_box_'
			, 'Sites'
			, array( $this, 'render_add_departments_nav_menu_box' )
			, 'nav-menus' // important !!!
			, 'side' // important, only side seems to work!!!
			, 'default'
		);
	}

	/**
	 * Prints the HTML for the Meta box
	 */
	function render_add_departments_nav_menu_box() {
		$sites = wp_get_sites();

		// Sort Sites by blog_id using insertion sort
		for ( $i = 0; $i < count( $sites ); $i ++ ) {
			for ( $k = $i; $k > 0 && $sites[$k]['path'] > $sites[$k - 1]['path']; $k -- ) {
				$temp          = $sites[$k];
				$sites[$k]     = $sites[$k - 1];
				$sites[$k - 1] = $temp;
			}
		}

		?>
		<div id="posttype-archive" class="posttypediv">
			<ul class="posttype-tabs add-menu-item-tabs">
				<li class="tabs"><a class="nav-tab-link" href="#recent-blogs">All Sites</a></li>
			</ul>
			<div id="recent-blogs" class="tabs-panel tabs-panel-active">
				<ul class="categorychecklist form-no-clear">
					<?php $i = 0;
					foreach ( $sites as $site ) : $i ++;
						switch_to_blog( $site['blog_id'] );
						?>
						<li>
							<label class="menu-item-title"><input type="checkbox" class="menu-item-checkbox" name="menu-item[-<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>"> <?php bloginfo( 'blogname' ); ?>
							</label>
							<input type="hidden" class="menu-item-title" name="menu-item[-<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>">
							<input type="hidden" class="menu-item-url" name="menu-item[-<?php echo esc_attr( $i ); ?>][menu-item-url]" value="<?php echo esc_attr( $site['path'] ); ?>">
							<input type="hidden" value="custom" name="menu-item[-<?php echo esc_attr( $i ); ?>][menu-item-type]">
						</li>
						<?php
						restore_current_blog();
					endforeach; ?>
				</ul>
			</div>

			<p class="button-controls">
	            <span class="list-controls">
	                <a href="/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#posttype-archive" class="select-all">Select All</a>
	            </span>

	            <span class="add-to-menu">
	                <input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-archive">
	                <span class="spinner"></span>
	            </span>
			</p>
		</div>
	<?php
	}
}

/**
 * KickStarts the metabox
 */
function blogs_to_nav_menu() {
	if ( ! is_multisite() || is_large_network() ) {
		return;
	}

	new Blogs_To_Nav_Menu_Meta_Box();
}


add_action( 'admin_init', 'blogs_to_nav_menu' );