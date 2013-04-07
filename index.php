<!DOCTYPE html>
<html lang="en">
<head>
<title>Find you the Best CPAN Module</title>
<meta name="description" content="Help you search Perl Modules which you wanted, and order them by likes number">
<meta name="keywords" content="Perl,CPAN,Search,Perl Module">
<meta name="author" content="Chen Gang,Fu Liang">
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet">
<style type="text/css">html,body{background-color:#e6e6e6;background-image:url('http://ww2.sinaimg.cn/large/60510e1ajw1e326vd3f9gj.jpg');background-repeat:no-repeat;background-position:top center;background-attachment:fixed;}.container{margin:25px auto;}.span10{margin:30px auto;}</style>
</head>


<body>
<div class="container">
<div class="row-fluid">
<div class="span12" style="background:#fff;-moz-box-shadow: 0 0 30px 3px #999;-webkit-box-shadow: 0 0 30px 3px #999;">
<div class="span10 offset1">
<form method="get" action="" class="form-search">
  <fieldset>
    <legend>Find you the Best CPAN Module</legend>
    <div class="input-append">
<?php
$q = $_GET['q'];
$kw = $q ? $q : 'Keyword..' ;
?>
    <input class="search-query" id="appendedInputButton" type="text" name="q" placeholder="<?php echo $kw; ?>">
    <button class="btn btn-info" type="submit">Search</button>
    </div>
  </fieldset>
</form>


<tbody>
<?php
$dsn = 'mysql:dbname=cpan;host=127.0.0.1';
$user = 'cpan';
$password = '4e537708a08d3926753e934317ed6394';

$dbh = new PDO($dsn, $user, $password);
$sth = $dbh->prepare("select name,score,version,description,updated from zabc_modules where match (name,description) against ('$q' in boolean mode) order by score desc , updated desc");
$sth->execute();
$result = $sth->fetchAll();
$tip = "";
if (empty($result))
{
	$sth = $dbh->prepare("select name,score,version,description,updated from zabc_modules order by score desc , updated desc limit 100");
	$sth->execute();
	$result = $sth->fetchAll();
	$tip = "No module found for '$q'. Below are Top100 liked modules on cpan.";
}

echo <<<HTML
<p class="muted">
  <em>$tip</em>
</p>
<table class="table table-hover">
<thead>
<tr class="success">
<th>Rank</th>
<th>Likes</th>
<th>Module Name</th>
<th>Version</th>
<th>Updated At</th>
</tr>
</thead>
HTML;

$i = 0;
foreach ($result as $row )
{
	$i++;
	$score = $row['score'];
	$name = mb_strimwidth($row['name'], 0, 50, ' ...');
	$description = $row['description'];
	if(strlen($description) + strlen($name) > 100)
	{
		$p = "<p>";
		$p2 = "</p>";
	}
	else
	{
		$p = '';
		$p2 = '';
	}
	$description_short = mb_strimwidth($row['description'], 0 , 115, ' ...');
	if ($description != $description_short)
	{
		$description_title = "title=\"$description\"";
	}
	$version = $row['version'] ? $row['version'] : 'n/a';
	$updated = date('M d, Y',$row['updated']);
echo <<<HTML
<tr>
<td>
$i
</td><td>
$score
</td><td>
<a href="https://metacpan.org/module/$name" target="_blank">
<big>$name</big>
</a>
$p<small class="muted" $description_title>$description_short</small>$p2
</td><td>
$version
</td><td>
$updated
</td>
</tr>
HTML;

}
?>

</tbody>
</table>

<p class="text-right lead">
<small>
Designed by <a href="http://blog.yikuyiku.com">blog.yikuyiku.com</a>, 
thx to <a href="http://weibo.com/u/1670442183">@Lars</a>
</small>
</p>

</div>
</div>
</div>
</div>
</body>
</html>
