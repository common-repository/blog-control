<?
if ($_SERVER["REQUEST_METHOD"] == 'POST') return;
foreach ($_COOKIE as $n=>$v) { if (substr($n, 0, 13) == 'wordpressuser') return;}
$bc_uri = $_SERVER['REQUEST_URI'];
if (strpos($bc_uri, '/wp-') === false && strpos($bc_uri, '/download') !== 0)
{
	$x = strpos($bc_uri, '#');
	if ($x !== false) $bc_uri = substr($bc_uri, 0, $x); 
	$bc_cache_name = md5($bc_uri);
	if (file_exists(ABSPATH . '/wp-content/bc-cache/' . $bc_cache_name . '.dat'))
	{
		$data = unserialize(file_get_contents(ABSPATH . '/wp-content/bc-cache/' . $bc_cache_name . '.dat'));

		if ($data != null && $data['time'] > time()-3600) 
		{
			header('Content-Type: text/html;charset=UTF-8');

			if ($data['html'] != '')
			{
				echo $data['html'];
				//echo '<!-- ' . var_dump($_COOKIE) . ' -->';
				echo '<!--  -->';
				exit();
			}
		}
	}
	ob_start('bc_cache_callback');
}

function bc_cache_callback($buffer)
{
  $name = md5($_SERVER['REQUEST_URI']);
  
  $data['uri'] = $_SERVER['REQUEST_URI'];
  $data['referer'] = $_SERVER['HTTP_REFERER'];
  $data['time'] = time();
  $data['html'] = $buffer;
  
  $file = fopen(ABSPATH . '/wp-content/bc-cache/' . $name . '.dat', 'w');
  fwrite($file, serialize($data));
  fclose($file);
  
  return $buffer;
}

?>
