<?php 

/**
 * @package CPD Copy Assignments
 * @version 1.0.0
 */

/*
Plugin Name:  CPD Copy Assignments
Plugin URI:   http://makedo.in/products/
Description:  A plug-in that works alongside the CPD Journals plugin (http://wordpress.org/plugins/cpd-journals/) which allows assignments to be copied into participants journals. 
Author:       Make Do
Version:      1.0.0
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


/////////  DEV STRUCTURE

1  - Create options page for CPD Copy Assignments
2  - Enqueue scripts

*/

// 1  - Create page for CPD Copy Assignments
require_once 'admin-menu-page.php';

// 2  - Enqueue scripts
require_once 'admin-scripts.php';