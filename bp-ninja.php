<?php
/*
  Plugin Name: BuddyPress Ninja
  Plugin URI: http://flweb.it
  Description: Site admins can use stealth mode to hide their last activity.
  Version: 0.2
  Author: Francesco Laffi
  Author URI: http://flweb.it
  License: GPL2
  Text Domain: bp-ninja
  Domain Path: /lang
 */

/*  Copyright 2011  Francesco Laffi  (email : francesco.laffi@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

add_action('bp_init', 'bp_ninja');

function bp_ninja() {
	global $bp, $bp_ninja_options;

	if (is_super_admin()) {
		$bp_ninja_options = get_usermeta($bp->loggedin_user->id, 'bp_ninja');

		if ($bp_ninja_options === '')
			$bp_ninja_options = array('stealth_mode' => false);

		if (isset($_GET['disable-stealth']) && $bp_ninja_options['stealth_mode'] !== false) {
			$bp_ninja_options['stealth_mode'] = false;
			update_usermeta($bp->loggedin_user->id, 'bp_ninja', $bp_ninja_options);
		}

		if (isset($_GET['enable-stealth']) && $bp_ninja_options['stealth_mode'] !== true) {
			$bp_ninja_options['stealth_mode'] = true;
			update_usermeta($bp->loggedin_user->id, 'bp_ninja', $bp_ninja_options);
		}


		if ($bp_ninja_options['stealth_mode']) {
			//first remove the action that record the last activity
			remove_action('wp_head', 'bp_core_record_activity');

			//then remove the last activity, if present
			delete_usermeta($bp->loggedin_user->id, 'last_activity');
		}

		add_action('bp_adminbar_menus', 'bp_ninja_adminbar_menu', 80);
	}
}

// **** Admin bar menu ********
function bp_ninja_adminbar_menu() {
	global $bp, $bp_ninja_options;
	?>
	<li id="bp-adminbar-ninja-menu">
		<a href="#"><?php _e('Stealth', 'bp-ninja') ?></a>
		<ul>
		<?php if ($bp_ninja_options['stealth_mode']) : ?>

			<li><a href="?disable-stealth"><?php _e('Disable stealth', 'bp-ninja') ?></a></li>

		<?php else: ?>

			<li><a href="?enable-stealth"><?php _e('Enable stealth', 'bp-ninja') ?></a></li>

		<?php endif; ?>

		<?php do_action('bp_adminbar_ninja_menu') ?>
		</ul>
	</li>
	<?php
}

function bp_ninja_delete_user_data($user_id) {
	delete_usermeta($user_id, 'bp_ninja');
}

add_action('wpmu_delete_user', 'bp_ninja_delete_user_data');
add_action('delete_user', 'bp_ninja_delete_user_data');
add_action('bp_make_spam_user', 'bp_ninja_delete_user_data');
?>