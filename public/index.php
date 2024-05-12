<?php
require_once __DIR__ . "/../vendor/autoload.php";

use vitoni\Fortunes;
use vitoni\Quote;
use vitoni\Quote\Formatter;
use vitoni\Quote\Parser;

// Grab from a single fortune file:
$fortunes = Fortunes::from(__DIR__ . '/datfiles/murphy');

$idx = -1;
if (isset($_GET["idx"])) {
    $idxParam = intval($_GET["idx"]);
    // check if in range
    if (isset($fortunes[$idxParam])) {
        $idx = $idxParam;
    }
}

// no index set yet, get random offset in range of fortunes
if ($idx === -1) {
    $idx = $fortunes->getRandomOffset();
}

$quote;
try {
    $quote = Parser::parse($fortunes[$idx]);
} catch(\Exception $e) {
    $quote = new Quote('Nothing to see here. Go along!', 'Unnamed authority');
}

$nextIdx = $idx;

// no while loop to cover fortunes files which have one or less fortunes
for ($i = 0; $i < 10; $i++) {
    $nextIdx = $fortunes->getRandomOffset();
    if ($nextIdx != $idx) {
        break;
    }
}
$href = "?idx=".$nextIdx;

echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8">
        <title>ViToni - Fortune teller</title>
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="StyleSheet" type="text/css" media="screen" href="/css/default.css"/>
        <link rel="StyleSheet" type="text/css" media="screen" href="/css/quotes.css"/>
        <link rel="StyleSheet" type="text/css" media="screen" href="/css/single_quote.css"/>
    </head>
    <body>
        <div id="container">
            <div id="header"></div>
            <div id="main">
<?php
$indent = '              ';
$htmlQuote = Formatter::format($quote, $indent);

echo $htmlQuote;
?>
            </div>
            <div id="footer">
                <a class="next"<?php echo " href='$href'";?> alt="next random fortune">&gt;&gt;&gt;</a>
            </div>
        </div>
    </body>
</html>
