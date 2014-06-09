<?php
require_once("../include/auth.php");
require_once("../include/auth_admin.php");

$page_title = "Site Admin";
?>

<?php include("../include/header.php"); ?>

<h1>Security Experiments</h1>

<p>Based on: Essential PHP Security, by Chris Shiflett, 10/2005.</p>

<ol>
<li><a href='summary.php'>Summary</a></li>
<li><a href='http.php'>Spoofing HTTP header</a>. p.31.</li>
</ol>

<?php include("../include/footer.php"); ?>

