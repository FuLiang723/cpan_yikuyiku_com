use HTTP::Tiny;
use List::MoreUtils qw{ uniq };
use Date::Parse;
use String::Escape qw /backslash/;

my @modules;
my @m;
my $resp;

#fetch core module name
for (A..Z)
{
	$resp = HTTP::Tiny->new("timeout" => 30)->get("http://perldoc.perl.org/index-modules-$_.html")->{content};
	@m = $resp =~ /<li><a href="[\w\/]+\.html">([\w\:]+)<\/a>\s*\-/smg;
	push @modules, @m;
}

#fetch cpan module name
$resp = HTTP::Tiny->new("timeout" => 30)->get("http://www.cpan.org/modules/01modules.index.html")->{content};
@m = $resp =~ /([\-\.\w\d]+)\-[\.\d]+\.tar\.gz<\/a>/g;
push @modules, @m;

@modules = uniq @modules;

#fetch module info
foreach (@modules)
{
	$_ =~ s/\-/\:\:/g;

	$resp = HTTP::Tiny->new("timeout" => 30)->get("https://metacpan.org/module/$_")->{content};
	my ($d) = $resp =~ /<li><strong class="relatize">([\w\:\s]+)<\/strong><\/li>/;
	my ($version) = $resp =~ /<li>Module version: ([\d\.]+)<\/li>/;
	my ($description) = $resp =~ /<title>[\w\:]+ \- (.+) \- metacpan\.org<\/title>/;
	$description =~ s/\'//g;
	$description =~ s/\"//g;
	$description =~ s/\#//g;
	$description =~ s/\*//g;
	$description =~ s/\=//g;
	$description =~ s/\;//g;
	$description =~ s/\///g;
	$description =~ s/\\//g;
	$description =~ s/\,//g;
	my ($score) = $resp =~ /(\d*?)<\/span> \+\+/;
	my $ts = str2time($d);

	my $sql = "replace into zabc_modules (name,score,version,description,updated) values ('$_', '$score', '$version', '$description', '$ts');";
	print $sql; # DBI->do($sql);
	print "\n";
}
