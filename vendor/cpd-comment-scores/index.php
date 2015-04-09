<?php 

/**
 * @package CPD Comment Scores
 * @version 1.0.1
 */

/*
Plugin Name:  CPD Comment Scores
Plugin URI:   http://makedo.in/products/
Description:  A plug-in that works alongside the CPD Journals plugin (http://wordpress.org/plugins/cpd-journals/) which allows supervisors to score a participants assignment through the comments system.
Author:       Make Do
Version:      1.0.1
Author URI:   http://makedo.in/
Licence:      GPLv2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.


/////////  VERSION HISTORY

1.0.0			First development version
1.0.1			Prevent participant from disabling comments by removing the menu


/////////  DEV STRUCTURE

1  - Add additional fields to comments box
2  - Add the additional meta to the comment admin screens

*/

// 1  - Add additional fields to comments box
require_once 'ui-additional-fields.php';

// 2  - Enqueue scripts
require_once 'admin-comment-meta.php';