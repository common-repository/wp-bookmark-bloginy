<?php
/*
Plugin Name: WP bookmark bloginy
Plugin URI: http://blog.bloginy.com
Description:  Add a bloginy button to vote for your post
Author: Inal Djafar
Version: 1.1
Author URI: http://www.inaldjafar.com

*/
/*  Copyright 2009 Djafar Inal (email : djafar.inal[at]gmail[dot]com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function wp_bookmark_bloginy($content='')
{
	global $wp_query;
	$post = $wp_query->post;
    $id = $post->ID;
    $postlink = get_permalink($id);
    $xml_file ='http://www.bloginy.com/bloginy/plugin?url='.$postlink;
    $posToBookmark = simplexml_load_file($xml_file);
    $found = $posToBookmark->bloginy_bookmark->found;
    $proposed = $posToBookmark->bloginy_bookmark->proposed;
    $votes = $posToBookmark->bloginy_bookmark->votes;
    $link = $posToBookmark->bloginy_bookmark->link;
    $displayType = get_option('wp_bookmark_bloginy');
	if (empty($displayType)) {
		$displayType ="after_left";
	}
    $position = (ereg("after",$displayType)) ? "after" : "before";
    if( is_single() && $found) 
    {
		if (!$proposed)
	 		{
	 			$bookmarkbloginy = '<a href="'.$link.'" class="blgoiny_btn wp_bookmark_blgoiny_proposer"></a><br />';
	 			$content =  ($position == 'after') ? $content . $bookmarkbloginy : $bookmarkbloginy . $content;
	 			return $content;
	 			exit;
	 		}
	 		elseif($proposed)
	 		{
	 			$bookmarkbloginy = '<a href="'.$link.'" class="blgoiny_btn wp_bookmark_blgoiny_vote" ><span class="wp_bookmark_blgoiny_vote_number">'.$votes.'</span></a><br />';
	 			$content =  ($position == 'after') ? $content . $bookmarkbloginy : $bookmarkbloginy . $content;
	 			return $content;
	 			exit;
	 		}
    }
    
    return $content;
    
}


function wp_bookmark_bloginy_menu($displayType='')
{
	
	if ($_REQUEST['displayTypeSubmit']) {
		update_option('wp_bookmark_bloginy', $_REQUEST['displayType']);
		echo '<div id="message" class="updated fade"><p>Saved changes.</p></div>';
	}
	$displayType = get_option('wp_bookmark_bloginy');
	if (empty($displayType)) {
		$displayType ="after_left";
	}
?>
	<div class="wrap">
		<h2>WP bookmark bloginy</h2>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<p>Select your display mode :</p>
			<select name="displayType">
				<option value="before_left"  <?php echo ($displayType == "before_left") ? " selected" : "";  ?> >Before content, at left</option>
				<option value="before_right" <?php echo ($displayType == "before_right") ? " selected" : ""; ?> >Before content, at right</option>
				<option value="after_left"   <?php echo ($displayType == "after_left") ? " selected" : ""; 	 ?> >After content, at left</option>
				<option value="after_right"  <?php echo ($displayType == "after_right") ? " selected" : "";  ?> >After content, at right</option>
			</select>
			<p class="submit">
				<input type="submit" name="displayTypeSubmit" value="Update options"  />
			</p>
		</form>
	</div>
<?php
}


function wp_bookmark_bloginy_admin_menu()
{
	add_options_page('Wp bookmark bloginy','Wp bookmark bloginy',10,__FILE__,'wp_bookmark_bloginy_menu');
}

function wp_bookmark_blgoiny_style(){

	$displayType = get_option('wp_bookmark_bloginy');
	$float = (ereg("left",$displayType)) ? "left" : "right";
	echo '
	<style type ="text/css">
		.blgoiny_btn{width:60px;height:70px;text-align:center;display:block;float:'.$float.';text-decoration:none;}
		.blgoiny_btn:hover{text-decoration:none !important;}
		.wp_bookmark_blgoiny_vote{background:url('.WP_PLUGIN_URL.'/wp-bookmark-bloginy/images/btn_vote.png);}
		.wp_bookmark_blgoiny_proposer{background:url('.WP_PLUGIN_URL.'/wp-bookmark-bloginy/images/btn_proposer.png);}
		.wp_bookmark_blgoiny_vote_number{width:60px;height:70px;line-height:70px;font-size:30px;color:#2687ab;display:block;font-family:"Trebuchet MS",Tahoma,sans-serif;}
	</style>
	';
}

add_action('admin_menu','wp_bookmark_bloginy_admin_menu');
add_filter('wp_head','wp_bookmark_blgoiny_style');
add_filter('the_content','wp_bookmark_bloginy');
?>
