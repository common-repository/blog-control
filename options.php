<?
function bc_request($name, $default=null) 
{
	if (!isset($_REQUEST[$name])) return $default;
	if (get_magic_quotes_gpc()) return bc_stripslashes($_REQUEST[$name]);
	else return $_REQUEST[$name];
}

function bc_stripslashes($value)
{
	$value = is_array($value) ? array_map('bc_stripslashes', $value) : stripslashes($value);
	return $value;
}
	
function bc_field_text($name, $label='', $tips='', $attrs='')
{
  global $options;
  if (strpos($attrs, 'size') === false) $attrs .= 'size="30"';
  echo '<tr><td class="label">';
  echo '<label for="options[' . $name . ']">' . $label . '</label></td>';
  echo '<td><input type="text" ' . $attrs . ' name="options[' . $name . ']" value="' . 
    htmlspecialchars($options[$name]) . '"/>';
  echo ' ' . $tips;
  echo '</td></tr>';
}

function bc_field_checkbox($name, $label='', $tips='', $attrs='')
{
  global $options;
  echo '<tr><td class="label">';
  echo '<label for="options[' . $name . ']">' . $label . '</label></td>';
  echo '<td><input type="checkbox" ' . $attrs . ' name="options[' . $name . ']" value="1" ' . 
    ($options[$name]!= null?'checked':'') . '/>';
  echo ' ' . $tips;
  echo '</td></tr>';
}

function bc_field_textarea($name, $label='', $tips='', $attrs='')
{
  global $options;
  
  if (strpos($attrs, 'cols') === false) $attrs .= 'cols="70"';
  if (strpos($attrs, 'rows') === false) $attrs .= 'rows="5"';
  
  echo '<tr><td class="label">';
  echo '<label for="options[' . $name . ']">' . $label . '</label></td>';
  echo '<td><textarea wrap="off" ' . $attrs . ' name="options[' . $name . ']">' . 
    htmlspecialchars($options[$name]) . '</textarea>';
  echo '<br /> ' . $tips;
  echo '</td></tr>';
}	

$options = get_option('bc');
if (isset($_POST['update']))
{
  $options = bc_request('options');
  update_option('bc', $options);
}
?>	
<style type="text/css">
#blog-control .label
{
  width: 150px;
  vertical-align: top;
  font-weight: bold;
  text-align: right;
}
#blog-control textarea {
    font-family: monospace;
}
</style>
    <div class="wrap">
    <div id="blog-control">
      <form method="post">
      
        <h2>Blog Control</h2>
        
        <h3>General</h3>
        <table>
        <? bc_field_checkbox('compress', 'Compress HTML', '(reduce the size of the html, on my blog from 10 to 30%)'); ?>
        <? bc_field_checkbox('cache', 'Enable cache', '(read the instruction in the read me - not for newbye'); ?>
        </table>
        
        <h3>Outboud links</h3>
        <table>
        <? bc_field_checkbox('link_blank', 'New window', '(open links in a new window)'); ?>
        <? bc_field_checkbox('link_track_urchin', 'Track outbound links', '(needs google analytics code installed - old version (urchinTracker))'); ?>
        <? bc_field_checkbox('link_nofollow', 'Add "nofollow" to post links'); ?>
        </table>
        
        <h3>Head</h3>
        <table>
        <? bc_field_textarea('head', 'Head html code', '(eg. the verification metatag of Google or MSN Webmaster Tools)'); ?>
        </table>
		
		<h3>Robots</h3>
        <table>
        <? bc_field_checkbox('bot_search', 'Block bot on search pages'); ?>
        <? bc_field_checkbox('bot_category', 'Block bot on category pages'); ?>
        </table>
		
          
        <h3>Footer</h3>
        <p>This text code will be added to the footer of the blog.</p>
        <table>
        <? bc_field_textarea('footer', 'Footer code', '(eg. the Google Analytics code or the MyBlogLog tracking code)'); ?>
        </table>
        
        <h3>Post</h3>
        <table>
        <? bc_field_textarea('post_before', 'Code before the post', '[title] or [title_encoded] for the post title, [link] or [link_encoded] for the post permalink'); ?>
        <? bc_field_textarea('post_after', 'Code after the post', '[title] or [title_encoded] for the post title, [link] or [link_encoded] for the post permalink'); ?>
        <? bc_field_textarea('post_more', 'Code after the more break', '[title] or [title_encoded] for the post title, [link] or [link_encoded] for the post permalink'); ?>
        </table>

        <h3>Page</h3>
        <table>
        <? bc_field_checkbox('page_post', 'Use the post codes'); ?>
        <? bc_field_textarea('page_before', 'Code before the page', '[title] or [title_encoded] for the post title, [link] or [link_encoded] for the post permalink'); ?>
        <? bc_field_textarea('page_after', 'Code after the page', '[title] or [title_encoded] for the post title, [link] or [link_encoded] for the post permalink'); ?>
        </table>
        
        <h3>Related</h3>
        <table>
        <? bc_field_text('related_max', 'Number of posts to display'); ?>
        <? bc_field_text('related_label', 'Label if related present', '(eg. Other aticles you may be interested in:)'); ?>
        <? bc_field_text('related_open', 'HTML to open the list', '(usually &lt;ul&gt;)'); ?>
        <? bc_field_text('related_close', 'HTML to close the list', '(usually &lt;/ul&gt;)'); ?>
        <? bc_field_text('related_before', 'HTML before an item', '(usually &lt;li&gt;)'); ?>
        <? bc_field_text('related_after', 'HTML after an item', '(usually &lt;/li&gt;'); ?>
        <? bc_field_text('related_separator', 'Separator', '(separator between related link, leave blank if you use the &lt;ul&gt;&lt;li&gt; structured list)'); ?>
        </table>  
        <p>The result will be: [before] [link] [after] [separator].</p>      
        
        <h3>Blocks</h3>
        
        <table>
        <? bc_field_textarea('block1', 'Code for block1', ''); ?>
        <? bc_field_textarea('block2', 'Code for block2', ''); ?>
        <? bc_field_textarea('block3', 'Code for block3', ''); ?>
        </table>

        <div class="submit">
          <input type="submit" name="update" value="Update">
        </div>
      </form>
      </div>
    </div>
  </body>
</html>
