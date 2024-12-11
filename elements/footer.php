</main>
<script>
    if (document.querySelector('main > .sidebar')) {
        document.querySelector('main').classList.add('has-sidebar');
    }

    document.querySelectorAll('.bi').forEach(icon => {
        const normalClass = icon.classList[1];
        const fillClass = `bi-${normalClass.replace('bi-', '')}-fill`;
        icon.addEventListener('mouseover', () => icon.classList.replace(normalClass, fillClass));
        icon.addEventListener('mouseout', () => icon.classList.replace(fillClass, normalClass));
    });
</script>
</html>