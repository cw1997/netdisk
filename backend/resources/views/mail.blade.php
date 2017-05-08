<!DOCTYPE html>
<html>
<head>
	<title>百毒网盘 - 安全验证邮件</title>
	<style type="text/css" media="screen">
		strong {
			font-weight: bolder;
			font-size: 36px;
			font-family: 黑体;
			color: red;
		}
	</style>
</head>
<body>
	<h1>百毒网盘 - 安全验证邮件</h1>
	<hr>
	<p>{{ $email }}：您好，为了您的百毒网盘帐号安全，请在操作时输入验证码 <strong>{{ $vcode }}</strong> ，如果您最近并没有访问该网站，请忽略该安全验证邮件。</p>
	<p align="right">百毒网盘 昌维 2017.3</p>
</body>
</html>
