<?php use App\Utility\Functions; ?>
<header>
    <p class="header-title">Hollow Mountains</p>
    <p class="header-page-title"><?= Functions::convertToTitle($_GET['page']) ?></p>
    <ul class="nav">
        <li><a href="?page=home">home</a></li>
        <li><a href="?page=medewerkers.overzicht">medewerkers</a></li>
        <li><a href="?page=attracties.overzicht">attracties</a></li>
    <?php if ($_SESSION['loggedIn'] === true): ?>
        <li><a href="?page=logout">logout</a></li>
    <?php else: ?>
        <li><a href="?page=login">login</a></li>
    <?php endif; ?>
    </ul>
</header>