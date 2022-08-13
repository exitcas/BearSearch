<?php
include "vurl.php";
if ($vi == "") {
  die("<!DOCTYPE html><html lang=\"en\" dir=\"ltr\"><head><title>BearSearch</title></head><body style=\"font-family:'Verdana', sans-serif\"><h1>BearSearch</h1><p>Create a \"search engine\" for your <a href=\"https://bearblog.dev/\">Bearblog</a></p><ol><li>Create a blog/post with the permalink '<input style=\"font-style:italic;\" type=\"text\" value=\"this-blog-uses-bearsearch\" disabled />'. It could be listed or not, but it should be available via URL.</li><li>Enter to http://".htmlentities($_SERVER["HTTP_HOST"])."/[your username*] <i>(Eg.: <a href=\"/luqaska\">http://".htmlentities($_SERVER["HTTP_HOST"])."/luqaska</a>)</i></li></ol><p>*if you have a custom url, use your original one (or the one that appears as your name at the blog's RSS feed)</p><footer><a href=\"https://github.com/Luqaska/BearSearch\">Source code</a>. This website is not affilated with Bearblog or Herman Martinus.</footer></body></html>");
}

function error() {
  http_response_code(404);
  die("<!DOCTYPE html><html lang=\"en\" dir=\"ltr\"><body style=\"font-family:'Verdana', sans-serif\"><h1>BearSearch</h1><p>This blog doesn't exist (or it hasn't been added yet)</p><p><a href=\"/\">Go home?</a></p><footer><a href=\"https://github.com/Luqaska/BearSearch\">Source code</a>. This website is not affilated with Bearblog or Herman Martinus.</footer></body></html>");
}

$blog = "https://$vi.bearblog.dev/";
ob_start();
$web = file_get_contents($blog);
$meta = get_meta_tags($blog);
$handle = curl_init($blog."this-blog-uses-bearsearch/");
curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($handle);
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
ob_end_clean();
if($httpCode == 404) {
  error();
}
curl_close($handle);

?>
<!DOCTYPE html>
<html>
<head>
<title><?= $meta["title"]; ?></title>
<?php preg_match('/<link rel="shortcut icon" href="(.*?)">/s', $web, $match); echo $match[0]."\n"; ?>
<link rel="alternate icon" href="https://bearblog.dev/static/favicon.ico" type="image/x-icon" sizes="48x48"/>
<style>
input[type=submit] {
  background-color: inherit;
  color: inherit;
  border: 1px solid;
  padding: 5px;
  font-size: 16px;
}
</style>
<?php preg_match('/<style>(.*?)<\/style>/s', $web, $match); echo $match[0]; ?>
</head>
<body>
<header>
<a class="title" href="<?= $blog ?>"><h1><?= $meta["title"]; ?></h1></a>
<nav>
<?php preg_match('/<nav>(.*?)<\/nav>/s', $web, $match); echo $match[0];
/*preg_match('/<nav>(.*?)<\/nav>/s', $web, $match); $match=explode("\n", simplexml_load_string($match[0])["nav"]);
foreach ($match as $id => $m) {
  $line = simplexml_load_string($m);
  if (isset($line["href"])) {
    if (!filter_var($line["href"], FILTER_VALIDATE_URL)) {
      $line["href"] = $blog . $line["href"];
    }
  }
  echo $id;
  $a = "<";
  foreach ($line as $i => $l) {
    $a += $i;
    if ($l) {$a+='="'.$l.'"';}
  }
  $a += ">";
  $match[$id] = $a;
}
echo implode("\n", $match);*/ ?>
</nav>
</header>
<main>
<content>
  <form method="GET"><input type="text" name="q" placeholder="Lorem Ipsum" <?php if (isset($_GET["q"])) { echo "value=\"" . htmlentities($_GET["q"]) . "\""; } ?> required><input type="submit" value="Search"></form>
    <?php if (isset($_GET["q"])) {
     $fileContents= file_get_contents($blog."feed/");
     $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
     $fileContents = trim(str_replace('"', "'", $fileContents));
     $simpleXml = simplexml_load_string($fileContents);
     $simpleXml = $simpleXml->{"entry"};
     $q = strtolower($_GET["q"]);
  echo "<ul class=\"blog-posts\">\n";
     $f = False;
     foreach ($simpleXml as $i) {
       if( (strpos(strtolower($i->{"title"}), $q) !== false) or (strpos(strtolower(strip_tags($i->{"content"})), $q) !== false) ) {
         $date = strtotime($i->{"updated"});
         echo '<li><span><time datetime="'. date("Y-m-d", $date) .'">' . date("j, M Y", $date) . '</time></span><a href="' . $i->{"id"} . '">' . $i->{"title"} . "</a></li>\n";
         //unset($i[$id]);
         $f = True;
       }
     }
     if (!$f) {
       echo "<h2>Nothing found :(</h2>";
     }
  echo "</ul>";
    } ?>
</content>
</main>
<footer>
  Powered by <a href="/">BearSearch</a>
</footer>
</body>
</html>
