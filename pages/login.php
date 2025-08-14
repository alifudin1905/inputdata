<?php
session_start();

if (isset($_SESSION['user'])) {
	header("Location: index.php");
	exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'] ?? '';
	$password = $_POST['password'] ?? '';
	if ($username === 'admin' && $password === 'admin') {
		$_SESSION['user'] = 'admin';
		header("Location: index.php");
		exit;
	} else {
		$error = 'Username atau password salah!';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<style>
		body { font-family: Arial; background: #f5f5f5; }
		.login-box { width: 350px; margin: 100px auto; background: #fff; padding: 32px 24px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
		.login-box h2 { margin-bottom: 24px; }
		.login-box input[type=text], .login-box input[type=password] { width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 4px; }
		.login-box button { width: 100%; padding: 10px; background: #1976d2; color: #fff; border: none; border-radius: 4px; font-size: 1rem; }
		.error { color: #d32f2f; margin-bottom: 16px; }
	</style>
</head>
<body>
	<div class="login-box">
		<h2>Login Admin</h2>
		<?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
		<form method="post">
			<input type="text" name="username" placeholder="Username" required autofocus>
			<input type="password" name="password" placeholder="Password" required>
			<button type="submit">Login</button>
		</form>
	</div>
</body>
</html>
