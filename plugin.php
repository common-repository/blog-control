<?php
/*
Plugin Name: Blog Control
Plugin URI: http://www.satollo.com/english/wordpress/blog-control
Description: Blog Control is a set of "parameters" to better control your blog
Version: 1.1.2
Author: Satollo
Author URI: http://www.satollo.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
*/

/*	Copyright 2008  Satollo  (email : satollo@gmail.com)

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

$bc_options = get_option('bc');

function bc_activate()
{
	global $wpdb, $table_prefix;

	$wpdb->query('ALTER TABLE ' . $table_prefix . 'posts ADD FULLTEXT bc_index (post_name, post_content, post_title)');
  
  mkdir(ABSPATH . '/wp-content/bc-cache', 0766);	
}

function bc_deactivate()
{
	global $wpdb, $table_prefix;

	$wpdb->query('DROP INDEX bc_index ON ' . $table_prefix . 'posts');
}

function bc_admin_head()
{
    add_options_page('Blog Control', 'Blog Control', 'manage_options', 'blog-control/options.php');
}

function bc_wp_head()
{
    global $bc_options;

    echo $bc_options['head'];
	if ($bc_options['bot_search'] && is_search())
		echo '<meta name="robots" content="noindex,nofollow"/>';
	if ($bc_options['bot_category'] && is_category())
		echo '<meta name="robots" content="noindex,nofollow"/>';
}

function bc_the_content(&$content)
{
    global $bc_options;
    
    if ($bc_options['link_nofollow'])
      $content = str_replace('<a', '<a rel="nofollow"', $content);

    if (!is_single() && !is_page()) return $content;

    $title = get_the_title();
    $title_encoded = urlencode($title);
    $link = get_permalink();
    $link_encoded = urlencode($link);

    if (is_single() || (is_page() && $bc_options['page_post']))
    {
        $before = $bc_options['post_before'];
        $after = $bc_options['post_after'];
        $more = $bc_options['post_more'];
		$more = str_replace('[author_aim]', get_the_author_aim(), $more);

        if (strpos($after, '[related]') !== false)
        {
          $related = bc_related();
        }
    }
    else if (is_page())
    {
        $before = $bc_options['page_before'];
        $after = $bc_options['page_after'];
    }

    $before = str_replace('[title]', $title, $before);
    $before = str_replace('[title_encoded]', $title_encoded, $before);
    $before = str_replace('[link]', $link, $before);
    $before = str_replace('[link_encoded]', $link_encoded, $before);
    $before = str_replace('[author_aim]', get_the_author_aim(), $before);

    $after = str_replace('[title]', $title, $after);
    $after = str_replace('[title_encoded]', $title_encoded, $after);
    $after = str_replace('[link]', $link, $after);
    $after = str_replace('[link_encoded]', $link_encoded, $after);
    $after = str_replace('[related]', $related, $after);
    $after = str_replace('[author_aim]', get_the_author_aim(), $after);
	
    $x = strpos($content, 'id="more');
    if ($x !== false) 
    {
        $x = strpos($content, '</p>', $x+4);
        if ($x !== false) $content = substr($content, 0, $x+4) . $more . substr($content, $x+4);
    }    

    return $before . $content . $after;
}

function bc_block($n)
{
    global $bc_options;
    echo $bc_options['block' . $n];
}

function bc_related()
{
  global $wpdb, $post, $bc_options;
  
  $before = &$bc_options['related_before'];
  $separator = &$bc_options['related_separator'];
  $after = &$bc_options['related_after'];
  $max = $bc_options['related_max'];
  if ($max == '') $max = 5;
  
  $terms = preg_replace('/[^a-z0-9]/i', ' ', $post->post_title);
  $now = gmdate("Y-m-d H:i:s", time() + get_settings('gmt_offset')*3600);
  
  $query = 'select id, match(post_title, post_name, post_content) against (\'' . $terms . '\') as score from ' . $wpdb->posts  . 
    ' where match(post_title, post_name, post_content) against (\'' . 
    $terms . "') and post_date<='" . $now . "'" .
    ' and post_status in (\'publish\') and id!=' . $post->ID . 
    ' order by score desc limit ' . $max;

  $results = $wpdb->get_results($query);

  $buffer = '';  
  if ($results)
  {
	$buffer = $bc_options['related_label'] . $bc_options['related_open'];
    $c = count($results);
    for ($i=0; $i<$c; $i++)
    {
      $r = &$results[$i];
      $title = get_the_title($r->id);
      $link = get_permalink($r->id);
      
      $buffer .= $before . '<a href="' . $link . '">' . $title . '</a>' . $after;
	  if ($i < $c-1) $buffer .= $separator;
    }
  }
  return $buffer . $bc_options['related_close'];
}

function bc_wp_footer()
{
    global $bc_options;
    echo $bc_options['footer'];
    if ($bc_options['link_blank'] || $bc_options['link_track_urchin'])
    {
        echo '<script type="text/javascript">' . "\n";
        echo 'var a=document.getElementsByTagName("a");var d=/^(http|https):\/\/([a-z-.0-9]+)[\/]{0,1}/i.exec(window.location);var il=new RegExp("^(http|https):\/\/"+d[2], "i");for(var i=0; i<a.length; i++) {if (!il.test(a[i].href)) {' .
            ($bc_options['link_blank']?'a[i].target="_blank";':'') .
            ($bc_options['link_track_urchin']?'a[i].onclick=function(){urchinTracker("/out/"+this.href.replace(/^http:\/\/|https:\/\//i, "").split("/").join("|"))};':'') .
            '}}' . "\n";
        echo '</script>';
  }
}

$bc_cache_key = '';

function bc_init()
{
  global $bc_options;
  
  // Do not act on wordpress admin pages
  if ($bc_options['compress'] && strpos($_SERVER['REQUEST_URI'], 'wp-') === false)
  {
    ob_start('bc_callback');
  }
}

function bc_callback($buffer)
{  
  $buffer = bc_compress($buffer);

  return $buffer;
}

function bc_cache_invalidate()
{
	$path = ABSPATH . 'wp-content/' . time();
	rename(ABSPATH . 'wp-content/bc-cache', $path);
	mkdir(ABSPATH . 'wp-content/bc-cache', 0766);

    if ($handle = opendir($path)) 
	{
		while ($file = readdir($handle)) 
		{
			if ($file != '.' && $file != '..') 
			{
				unlink($path."/".$file);
			}
		}
		closedir($handle);
		rmdir($path);
	}	
}

add_action('admin_head', 'bc_admin_head');
add_action('wp_head', 'bc_wp_head');
add_action('wp_footer', 'bc_wp_footer');
add_action('the_content', 'bc_the_content');
add_action('init', 'bc_init', 0);
add_action('activate_blog-control/plugin.php', 'bc_activate');
add_action('deactivate_blog-control/plugin.php', 'bc_deactivate');

if ($bc_options['cache']) 
{
	add_action('publish_post', 'bc_cache_invalidate', 0);
	add_action('edit_post', 'bc_cache_invalidate', 0);
	add_action('delete_post', 'bc_cache_invalidate', 0);
	add_action('publish_phone', 'bc_cache_invalidate', 0);
	// Coment ID is received
	add_action('trackback_post', 'bc_cache_invalidate', 0);
	add_action('pingback_post', 'bc_cache_invalidate', 0);
	add_action('comment_post', 'bc_cache_invalidate', 0);
	add_action('edit_comment', 'bc_cache_invalidate', 0);
	add_action('wp_set_comment_status', 'bc_cache_invalidate', 0);
	// No post_id is available
	add_action('delete_comment', 'bc_cache_invalidate', 0);
	add_action('switch_theme', 'bc_cache_invalidate', 0); 
}

// From Smarty engine
function bc_compress($source)
{
  // Pull out the script blocks
  preg_match_all("!<script[^>]+>.*?</script>!is", $source, $match);
  $_script_blocks = $match[0];
  $source = preg_replace("!<script[^>]+>.*?</script>!is", '@@@SMARTY:TRIM:SCRIPT@@@', $source);

  // Pull out the style
  preg_match_all("!<style[^>]+>.*?</style>!is", $source, $match);
  $_style_blocks = $match[0];
  $source = preg_replace("!<style[^>]+>.*?</style>!is", '@@@SMARTY:TRIM:STYLE@@@', $source);
  
  // Pull out the pre blocks
  preg_match_all("!<pre>.*?</pre>!is", $source, $match);
  $_pre_blocks = $match[0];
  $source = preg_replace("!<pre>.*?</pre>!is", '@@@SMARTY:TRIM:PRE@@@', $source);
  
  // Pull out the textarea blocks
  preg_match_all("!<textarea[^>]+>.*?</textarea>!is", $source, $match);
  $_textarea_blocks = $match[0];
  $source = preg_replace("!<textarea[^>]+>.*?</textarea>!is", '@@@SMARTY:TRIM:TEXTAREA@@@', $source);
  
  // remove all leading spaces, tabs and carriage returns NOT
  // preceeded by a php close tag.
  $source = trim(preg_replace('/((?<!\?>)\n)[\s]+/m', '\1', $source));
	
	// remove the comments
	$source = preg_replace('/<!--(.|\s)*?-->/', '', $source); 

  // replace textarea blocks
  bc_compress_replace("@@@SMARTY:TRIM:TEXTAREA@@@",$_textarea_blocks, $source);

  // replace pre blocks
  bc_compress_replace("@@@SMARTY:TRIM:PRE@@@",$_pre_blocks, $source);

  // replace style
  bc_compress_replace("@@@SMARTY:TRIM:STYLE@@@",$_style_blocks, $source);
  
  // replace script blocks
  bc_compress_replace("@@@SMARTY:TRIM:SCRIPT@@@",$_script_blocks, $source);

  return $source;
}

function bc_compress_replace($search_str, $replace, &$subject) 
{
    $_len = strlen($search_str);
    $_pos = 0;
    for ($_i=0, $_count=count($replace); $_i<$_count; $_i++)
        if (($_pos=strpos($subject, $search_str, $_pos))!==false)
            $subject = substr_replace($subject, $replace[$_i], $_pos, $_len);
        else
            break;

}


?>
