<h1>Login</h1>
<?php

use App\Utility\Functions;
use App\Utility\Session;

// Display errors
Functions::displayError(message: Session::get('login.error'));
Session::delete('login.error');
?>
<form action="?page=formHandler" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="login">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <input type="submit" name="login" value="Login">
</form>