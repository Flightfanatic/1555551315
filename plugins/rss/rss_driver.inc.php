<?php

/*
 +-----------------------------------------------------------------------+
 |                                                                       |
 | PURPOSE:                                                              |
 |   Send mailboxcontents as RSS feed                                    |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Roland Liebl <roland@liebl.ath.cx>                            |
 +-----------------------------------------------------------------------+

 $Id$

*/

///////////////
// RSS Encoding
///////////////

function api_rss_encode($string, $enc='xml'){
  //$string = rep_specialchars_output($string, $enc);
  if($string == ""){
    $string = cmail_label("rss.novalue");
  }
  $string = makeAllEntities($string) ;//htmlspecialchars($string, ENT_QUOTES);
	
  return $string;
}

function get_html_translation_table_extended(){

  $arr_entities = array_merge(get_html_translation_table(HTML_ENTITIES),
    array(
    '&apos;'=>'&#39;', '&minus;'=>'&#45;', '&circ;'=>'&#94;', '&tilde;'=>'&#126;', '&Scaron;'=>'&#138;',
    '&lsaquo;'=>'&#139;', '&OElig;'=>'&#140;', '&lsquo;'=>'&#145;', '&rsquo;'=>'&#146;', '&ldquo;'=>'&#147;',
    '&rdquo;'=>'&#148;', '&bull;'=>'&#149;', '&ndash;'=>'&#150;', '&mdash;'=>'&#151;', '&tilde;'=>'&#152;',
    '&trade;'=>'&#153;', '&scaron;'=>'&#154;', '&rsaquo;'=>'&#155;', '&oelig;'=>'&#156;', '&Yuml;'=>'&#159;',
    '&yuml;'=>'&#255;', '&OElig;'=>'&#338;', '&oelig;'=>'&#339;', '&Scaron;'=>'&#352;', '&scaron;'=>'&#353;',
    '&Yuml;'=>'&#376;', '&fnof;'=>'&#402;', '&circ;'=>'&#710;', '&tilde;'=>'&#732;', '&Alpha;'=>'&#913;',
    '&Beta;'=>'&#914;', '&Gamma;'=>'&#915;', '&Delta;'=>'&#916;', '&Epsilon;'=>'&#917;', '&Zeta;'=>'&#918;',
    '&Eta;'=>'&#919;', '&Theta;'=>'&#920;', '&Iota;'=>'&#921;', '&Kappa;'=>'&#922;', '&Lambda;'=>'&#923;',
    '&Mu;'=>'&#924;', '&Nu;'=>'&#925;', '&Xi;'=>'&#926;', '&Omicron;'=>'&#927;', '&Pi;'=>'&#928;',
    '&Rho;'=>'&#929;', '&Sigma;'=>'&#931;', '&Tau;'=>'&#932;', '&Upsilon;'=>'&#933;', '&Phi;'=>'&#934;',
    '&Chi;'=>'&#935;', '&Psi;'=>'&#936;', '&Omega;'=>'&#937;', '&alpha;'=>'&#945;', '&beta;'=>'&#946;',
    '&gamma;'=>'&#947;', '&delta;'=>'&#948;', '&epsilon;'=>'&#949;', '&zeta;'=>'&#950;', '&eta;'=>'&#951;',
    '&theta;'=>'&#952;', '&iota;'=>'&#953;', '&kappa;'=>'&#954;', '&lambda;'=>'&#955;', '&mu;'=>'&#956;',
    '&nu;'=>'&#957;', '&xi;'=>'&#958;', '&omicron;'=>'&#959;', '&pi;'=>'&#960;', '&rho;'=>'&#961;', '&sigmaf;'=>'&#962;',
    '&sigma;'=>'&#963;', '&tau;'=>'&#964;', '&upsilon;'=>'&#965;', '&phi;'=>'&#966;', '&chi;'=>'&#967;', '&psi;'=>'&#968;',
    '&omega;'=>'&#969;', '&thetasym;'=>'&#977;', '&upsih;'=>'&#978;', '&piv;'=>'&#982;', '&ensp;'=>'&#8194;',
    '&emsp;'=>'&#8195;', '&thinsp;'=>'&#8201;', '&zwnj;'=>'&#8204;', '&zwj;'=>'&#8205;', '&lrm;'=>'&#8206;',
    '&rlm;'=>'&#8207;', '&ndash;'=>'&#8211;', '&mdash;'=>'&#8212;', '&lsquo;'=>'&#8216;', '&rsquo;'=>'&#8217;',
    '&sbquo;'=>'&#8218;', '&ldquo;'=>'&#8220;', '&rdquo;'=>'&#8221;', '&bdquo;'=>'&#8222;', '&dagger;'=>'&#8224;',
    '&Dagger;'=>'&#8225;', '&bull;'=>'&#8226;', '&hellip;'=>'&#8230;', '&permil;'=>'&#8240;', '&prime;'=>'&#8242;',
    '&Prime;'=>'&#8243;', '&lsaquo;'=>'&#8249;', '&rsaquo;'=>'&#8250;', '&oline;'=>'&#8254;', '&frasl;'=>'&#8260;',
    '&euro;'=>'&#8364;', '&image;'=>'&#8465;', '&weierp;'=>'&#8472;', '&real;'=>'&#8476;', '&trade;'=>'&#8482;',
    '&alefsym;'=>'&#8501;', '&larr;'=>'&#8592;', '&uarr;'=>'&#8593;', '&rarr;'=>'&#8594;', '&darr;'=>'&#8595;',
    '&harr;'=>'&#8596;', '&crarr;'=>'&#8629;', '&lArr;'=>'&#8656;', '&uArr;'=>'&#8657;', '&rArr;'=>'&#8658;',
    '&dArr;'=>'&#8659;', '&hArr;'=>'&#8660;', '&forall;'=>'&#8704;', '&part;'=>'&#8706;', '&exist;'=>'&#8707;',
    '&empty;'=>'&#8709;', '&nabla;'=>'&#8711;', '&isin;'=>'&#8712;', '&notin;'=>'&#8713;', '&ni;'=>'&#8715;',
    '&prod;'=>'&#8719;', '&sum;'=>'&#8721;', '&minus;'=>'&#8722;', '&lowast;'=>'&#8727;', '&radic;'=>'&#8730;',
    '&prop;'=>'&#8733;', '&infin;'=>'&#8734;', '&ang;'=>'&#8736;', '&and;'=>'&#8743;', '&or;'=>'&#8744;',
    '&cap;'=>'&#8745;', '&cup;'=>'&#8746;', '&int;'=>'&#8747;', '&there4;'=>'&#8756;', '&sim;'=>'&#8764;',
    '&cong;'=>'&#8773;', '&asymp;'=>'&#8776;', '&ne;'=>'&#8800;', '&equiv;'=>'&#8801;', '&le;'=>'&#8804;',
    '&ge;'=>'&#8805;', '&sub;'=>'&#8834;', '&sup;'=>'&#8835;', '&nsub;'=>'&#8836;', '&sube;'=>'&#8838;',
    '&supe;'=>'&#8839;', '&oplus;'=>'&#8853;', '&otimes;'=>'&#8855;', '&perp;'=>'&#8869;', '&sdot;'=>'&#8901;',
    '&lceil;'=>'&#8968;', '&rceil;'=>'&#8969;', '&lfloor;'=>'&#8970;', '&rfloor;'=>'&#8971;', '&lang;'=>'&#9001;',
    '&rang;'=>'&#9002;', '&loz;'=>'&#9674;', '&spades;'=>'&#9824;', '&clubs;'=>'&#9827;', '&hearts;'=>'&#9829;',
    '&diams;'=>'&#9830;'
    )
  ); 

  return $arr_entities;
}

// Convert str to UTF-8 (if not already), then convert that to HTML named entities.
// and numbered references. Compare to native htmlentities() function.
// Unlike that function, this will skip any already existing entities in the string.
// mb_convert_encoding() doesn't encode ampersands, so use makeAmpersandEntities to convert those.
// mb_convert_encoding() won't usually convert to illegal numbered entities (128-159) unless
// there's a charset discrepancy, but just in case, correct them with correctIllegalEntities.
function makeSafeEntities($str, $convertTags = 0, $encoding = "") {
  if (is_array($arrOutput = $str)) {
    foreach (array_keys($arrOutput) as $key)
      $arrOutput[$key] = makeSafeEntities($arrOutput[$key],$encoding);
    return $arrOutput;
    }
  else if ($str !== "") {
    $str = makeUTF8($str,$encoding);
    $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
    $str = makeAmpersandEntities($str);
    if ($convertTags)
      $str = makeTagEntities($str);
    $str = correctIllegalEntities($str);
    return $str;
    }
  }

// Convert str to UTF-8 (if not already), then convert to HTML numbered decimal entities.
// If selected, it first converts any illegal chars to safe named (and numbered) entities
// as in makeSafeEntities(). Unlike mb_convert_encoding(), mb_encode_numericentity() will
// NOT skip any already existing entities in the string, so use a regex to skip them.
function makeAllEntities($str, $useNamedEntities = 0, $encoding = "") {
  $str = makeUTF8($str,$encoding);
  if ($useNamedEntities)
    $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
  $str = makeTagEntities($str,$useNamedEntities);
  // Fix backslashes so they don't screw up following mb_ereg_replace
  // Single quotes are fixed by makeTagEntities() above
  $str = mb_ereg_replace('\\\\',"&#92;", $str);
  // Fix &euro;
  $str = mb_ereg_replace('&euro;',"€", $str);
  $trans_tbl =get_html_translation_table_extended();
  $trans_tbl =array_flip ($trans_tbl );
  $str = strtr ($str, $trans_tbl );

  mb_regex_encoding("UTF-8");
  $str = mb_ereg_replace("(?>(&(?:[a-z]{0,4}\w{2,3};|#\d{2,5};)))|(\S+?)",
                        "'\\1'.mb_encode_numericentity('\\2',array(0x0,0x2FFFF,0,0xFFFF),'UTF-8')", $str, "ime");
  $str = correctIllegalEntities($str);
  return utf8_encode($str);
  }

// Convert common characters to named or numbered entities
function makeTagEntities($str, $useNamedEntities = 1) {
  // Note that we should use &apos; for the single quote, but IE doesn't like it
  $arrReplace = $useNamedEntities ? array('&apos;','&quot;','&lt;','&gt;') : array('&#39;','&#34;','&#60;','&#62;'); //#39
  return str_replace(array("'",'"','<','>'), $arrReplace, $str);
}


// Convert ampersands to named or numbered entities.
// Use regex to skip any that might be part of existing entities.
function makeAmpersandEntities($str, $useNamedEntities = 1) {
  return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/m", $useNamedEntities ? "&amp;" : "&#38;", $str);
  }

// Convert illegal HTML numbered entities in the range 128 - 159 to legal counterparts
function correctIllegalEntities($str) {
  $chars = array(
    128 => '&#8364;',
    130 => '&#8218;',
    131 => '&#402;',
    132 => '&#8222;',
    133 => '&#8230;',
    134 => '&#8224;',
    135 => '&#8225;',
    136 => '&#710;',
    137 => '&#8240;',
    138 => '&#352;',
    139 => '&#8249;',
    140 => '&#338;',
    142 => '&#381;',
    145 => '&#8216;',
    146 => '&#8217;',
    147 => '&#8220;',
    148 => '&#8221;',
    149 => '&#8226;',
    150 => '&#8211;',
    151 => '&#8212;',
    152 => '&#732;',
    153 => '&#8482;',
    154 => '&#353;',
    155 => '&#8250;',
    156 => '&#339;',
    158 => '&#382;',
    159 => '&#376;');
  foreach (array_keys($chars) as $num)
    $str = str_replace("&#".$num.";", $chars[$num], $str);
  return $str;
  }

// Compare to native utf8_encode function, which will re-encode text that is already UTF-8
function makeUTF8($str,$encoding = "") {
  if ($str !== "") {
    if (empty($encoding) && isUTF8($str))
      $encoding = "UTF-8";
    if (empty($encoding))
      $encoding = mb_detect_encoding($str,'UTF-8, ISO-8859-1');
    if (empty($encoding))
      $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
    return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str,"UTF-8",$encoding);
    }
  }

// Much simpler UTF-8-ness checker using a regular expression created by the W3C:
// Returns true if $string is valid UTF-8 and false otherwise.
// From http://w3.org/International/questions/qa-forms-utf-8.html
function isUTF8($str) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);
  }

///// END FUNCTIONS /////

$imap_charset = 'UTF-8';

$mbox = "INBOX";
if(!empty($_GET['_mbox']))
  $mbox = strtolower(urldecode(trim($_GET['_mbox'])));
  
if(in_array(ucwords($mbox), $CONFIG['default_imap_folders'])    ||
   in_array(strtolower($mbox), $CONFIG['default_imap_folders']) ||
   in_array(strtoupper($mbox), $CONFIG['default_imap_folders']) ||
   in_array($mbox, $CONFIG['default_imap_folders'])
){
  $disp_mbox = cmail_label(strtolower($mbox));
}
else{
  $disp_mbox = $mbox;
}

$webmail_url = 'http';
if (strstr('HTTPS', $_SERVER['SERVER_PROTOCOL'] )!== FALSE)
  $webmail_url .= 's';
$webmail_url .= '://'.$_SERVER['SERVER_NAME'];
if ($_SERVER['SERVER_PORT'] != '80')
	$webmail_url .= ':'.$_SERVER['SERVER_PORT'];

if (dirname($_SERVER['SCRIPT_NAME']) != '/')
	$webmail_url .= dirname($_SERVER['SCRIPT_NAME']) . '/';

$webmail_url = str_replace("\\","",$webmail_url);

$root_url = $webmail_url;

$webmail_url .= '?_task=mail';

$allowed_flags = array('all','unseen','flagged','unanswered','deleted');

$flag = "all";
$iflag = "seen";
$cflag = FALSE;

if(!empty($_GET['_flag']))
  $gflag = strtolower(urldecode(trim($_GET['_flag'])));
  
$fa = array_flip($allowed_flags);
if(!empty($fa[$gflag]))
  $flag = $gflag;

if($flag == "all"){ //ok
  $iflag = "unset";
  $cflag = FALSE;
}

if($flag == "unseen"){ //ok
  $iflag = "seen";
  $cflag = FALSE;
}
if($flag == "flagged"){ //ok
  $iflag = "flagged";
  $cflag = TRUE;
}
if($flag == "unanswered"){ //label missing
  $iflag = "answered";
  $cflag = TRUE;
}
if($flag == "deleted"){ //label missing
  $iflag = "deleted";
  $cflag = TRUE;
}

$rss_truncate_items = 1000;
if(!empty($CONFIG['rss_truncate_items']) && is_numeric($CONFIG['rss_truncate_items']))
  $rss_truncate_items = abs($CONFIG['rss_truncate_items']);

$rss_truncate_body = 50;
if(!empty($CONFIG['rss_truncate_body']) && is_numeric($CONFIG['rss_truncate_body']))
  $rss_truncate_body = abs($CONFIG['rss_truncate_body']);
  
$rss_truncate_body_parsing = 10;
if(!empty($CONFIG['rss_truncate_body_parsing']) && is_numeric($CONFIG['rss_truncate_body_parsing']))
  $rss_truncate_body_parsing = abs($CONFIG['rss_truncate_body_parsing']);  

$IMAP->set_caching(false);
$messagecount_unread  = $IMAP->messagecount($mbox, 'UNSEEN', TRUE);
$messagecount         = $IMAP->messagecount($mbox, 'ALL', TRUE);

if($flag == "unseen" && $messagecount_unread == 0)
  $rss_truncate_items = 0;

$sort_col = 'date';
$sort_order = 'DESC';

// Send global XML output
header('Content-type: text/xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          // HTTP/1.0

echo '<?xml version="1.0" encoding="UTF-8"?>
	<rss version="2.0"
	 xmlns:dc="http://purl.org/dc/elements/1.1/"
	 xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	 xmlns:admin="http://webns.net/mvcb/"
	 xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	 xmlns:content="http://purl.org/rss/1.0/modules/content/">';
	 
// Send channel-specific output
echo '
	<channel>
		<pubDate>'.date('r').'</pubDate>
		<lastBuildDate>'.date('r').'</lastBuildDate>
		<link>'.api_rss_encode($webmail_url . "&_action=plugin.rssauth") .'</link>
		<title>' . api_rss_encode($cmail->config->get('product_name') . ' ::: ' . $_SESSION['username'] . " ::: " . ucwords($disp_mbox) . " (" . cmail_label('rss.rss_' . $flag) . ")") . '</title>
		<description>' . api_rss_encode(ucwords($disp_mbox)) . '</description>
		<generator>'.api_rss_encode($CONFIG['useragent'], 'xml').'</generator>
		<image>
			<link>' . api_rss_encode($webmail_url) . '</link>
			<title>'.api_rss_encode($cmail->config->get('product_name')).' logo</title>
			<url>'.api_rss_encode($root_url . $cmail->config->get('skin_path') .'/images/Crystal_logo.png').'</url>
		</image>';

// Check if the user wants to override the default sortingmethode
if (isset($_GET['_sort']))
  list($sort_col, $sort_order) = explode('_', get_input_value('_sort', cmail_INPUT_GET));

// Add message to output
if ($messagecount > 0){
  $IMAP->page_size = min($messagecount,$rss_truncate_items);
  $items = $IMAP->list_headers($mbox, null, $sort_col, $sort_order);
 
  $i = -1;
  foreach ($items as $item){
    if($item->$iflag == $cflag){
      $i++;
      // Convert '"name" <email>' to 'email (name)'
      if (strstr($item->from, '<')){
        $item->from = preg_replace('~"?([^"]*)"? <([^>]*)>~', '\2 (\1)', $item->from);
      }
      
      $from_o = imap_mime_header_decode($item->from);
      $item->from = $from_o[0]->text . $from_o[1]->text . $from_o[2]->text;

      $item->link = $webmail_url.'&_action=show&_uid='.$item->uid.'&_mbox=' . urlencode($mbox);
      
      if($i < $rss_truncate_body_parsing){

        $item->body = $IMAP->get_body($item->uid);
         
        $MESSAGE = new cmail_message($item->uid);

        $item->body = $MESSAGE->first_text_part();
            
        if(strlen($item->body) > $rss_truncate_body){
          $trailing = " ... [" . cmail_label('rss.truncated') . "]";
        }
        else{
          $trailing = "";
        }
        
        $description = '<description><![CDATA['."\n".nl2br(api_rss_encode(substr($item->body,0,$rss_truncate_body) . $trailing))."\n".']]></description>' . "\n";
        //Find me: dirty fix!
        $description = str_replace("&apos;","&#39;",$description);
      }
      else{
        $description = "";
      }
      
      $subject = $item->subject;
      $res = imap_mime_header_decode($subject);
      $subject = $res[0]->text;
      if($subject == ""){
        $subject = cmail_label("nosubject");
      }

      // Print the actual messages
      echo '
			<item>
				<title>'.api_rss_encode("#" . ($i+1) . " | " . $subject . " | " . $item->from).'</title>
				' . $description . '
				<link>'.api_rss_encode($item->link).'</link>
				<author>'.api_rss_encode($item->from).'</author>
				<guid>'.api_rss_encode($item->link).'</guid>
				<pubDate>'.date('r', $item->timestamp).'</pubDate>
			</item>';
    }
  }
}
else{
      echo '
			<item>
				<title>'.api_rss_encode($mbox . ': ' . cmail_label('rss.mboxempty')).'</title>
				<description></description>
				<link></link>
				<author></author>
				<guid></guid>
				<pubDate></pubDate>
			</item>';
}

echo '</channel>
</rss>';

exit;
?>
