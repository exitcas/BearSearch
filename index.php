<?php
include "vurl.php";
if ($vi == "") {
  die("https://bearsearch.lu700.repl.co/[username]");
}

set_error_handler(function() { die("This user doesn't exist"); });
$blog = "https://$vi.bearblog.dev/";
$web = file_get_contents($blog);
$meta = get_meta_tags($blog);
$handle = curl_init($blog."this-blog-uses-bearsearch/");
curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($handle);
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
if($httpCode == 404) {
  die("This user doesn't exist");
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
<?php preg_match('/<nav>(.*?)<\/nav>/s', $web, $match); echo $match[0]; ?>
</nav>
</header>
<main>
<content>
  <form method="GET"><input type="text" name="q" placeholder="*query goes here*" <?php if (isset($_GET["q"])) { echo "value=\"" . htmlentities($_GET["q"]) . "\""; } ?> required><input type="submit" value="Search"></form>
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
  Powered by <a href="https://bearblog.dev">Bear ʕ•ᴥ•ʔ</a>(<a href="/">search</a>)<br />
  <a href="/block.php">Block your website</a>
</footer>
</body>
</html>