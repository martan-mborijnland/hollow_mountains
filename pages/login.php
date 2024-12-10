<h1>Login</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<span class="error">'. $_SESSION['error'] .'</span>';
    unset($_SESSION['error']);
}
?>
<form action="?page=formHandler" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="login">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <input type="submit" name="login" value="Login">
</form>