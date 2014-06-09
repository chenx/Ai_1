<?php
require_once("auth.php");
require_once("auth_admin.php");

$page_title = "Site Admin";
?>

<?php include("header.php"); ?>

<h3>Admin Functions</h3>

<ul>
<li><a href='admin_users.php'>Manage Users</a></li>
<!--<li><a href='admin_images.php'>Manage Images</a></li>-->
<li><a href='#' onclick="javascript: open_file('admin_images.php');">Manage Images</a></li>
<li><a href='admin_create_schema.php'>Create Schema For Tables</a></li>
<li><a href='admin_dump_table.php'>Dump Contents Of Tables</a></li>
<li><a href='admin_backup_db.php'>Backup Database</a></li>
</ul>

<h3>New functions</h3>
<ul>
<li><a href="captcha/README">Chinese captcha function</a> (7/16/2013) </li>
<li><a href='security/'>Security experiments</a> (7/16/2013)</li>
<li><a href="theme1.html">Page theme - centered</a> (6/26/2013)</li>
<li><a href="mem_paypal.php">Make payment using paypal</a> (6/21/2013)</li>
<li><a href="mem_writeimg.php">Write image from url, no cache on server</a> (6/21/2013)</li>
<li><a href="mem_pdf.php">Display text file, create PDF file.</a> (4/2013)</li>
</ul>

<h3>Dev Tasks</h3>
<ol>
<li>Blog</li>
<li>OJ</li>
<li>e-commerce, sell books. paypal, cc etc.</li>
<li>3 kinds of IMS</li>
<li>audio, video mgmt</li>
<li>Export to excel, access, pdf, word</li>
<li>News Letter subscription</li>
<li>Conversion tools</li>
<li>Web crawling - recommendation system</li>
<li>web service interface. SOAP, REST</li>
<li>graphing</li>
<li>cookie</li>
<li>login using fb etc.
<br/><a href='https://developers.facebook.com/docs/guides/web/'>FB for websites</a>
<br/><a href='https://developers.facebook.com/docs/facebook-login/login-flow-for-web/'>The login flow for web</a>
<br/><a href='https://developers.facebook.com/docs/facebook-login/login-flow-for-web-no-jssdk/'>The login flow for web, w/o javascript SDK</a> (use this one)
<br/><a href='https://developers.facebook.com/docs/facebook-login/getting-started-web/'>Getting started with FB login for web</a> (with SDK)
</li>
<li>BBS: phpBB</li>
<li>Wiki</li>
<li>Console</li>
<li>2-D scan matrix generator</li>
<li>Calendar</li>
<li>Search, suggestion dropdown</li>
<li>Game</li>
<li>List of countries, major cities etc.</li>
<li>Book reader</li>
</ol>

<?php include("footer.php"); ?>

