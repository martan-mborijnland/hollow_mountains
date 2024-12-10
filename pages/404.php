<style>
    * {
        font-family: monospace;
    }

    body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-size: 2rem;
    }

    div {
        line-height: 1rem;
        text-align: center;
    }

    a {
        position: fixed;
        bottom: 0.5rem;
        font-size: 1rem;
    }
</style>
<h1>404</h1>
<div>
    <p>Oops... page</p>
    <p>"<b><?= $_GET['page'] ?></b>"</p>
    <p>was not found!</p>
</div>
<a href="?page=home">Go back...</a>