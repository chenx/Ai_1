<tr>
<td>Enter code in image:<font color='red'>*</font></td>
<td><input type='text' id='txtCaptcha' name='txtCaptcha' value=''/></td>
<td>
<img id='imgCaptcha' src='func/captcha.php' border='1' style='vertical-align: middle;' title='Captcha image' width='150' height='20'>
<img id='btnChange' src='images/refresh.png' style='vertical-align: middle;' title='Change captcha image' onclick="javascript: changeCaptcha();">

<script type='text/javascript'>
//
// Use this to avoid refresh the entire page.
//
function changeCaptcha(o) {
    document.getElementById('imgCaptcha').src = "func/captcha.php?" + Math.random();
}

</script>

</td>
</tr>


