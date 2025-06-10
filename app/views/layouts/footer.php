<footer>
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Make URL_ROOT available to JavaScript -->
    <script>
        const URL_ROOT = '<?= URL_ROOT ?>';
    </script>
    <!-- Make auth status available globally -->
    <script>
        window.authStatus = {
            isLoggedIn: <?php echo isLoggedIn() ? 'true' : 'false'; ?>,
            loginUrl: '<?= URL_ROOT ?>/auth/login',
            registerUrl: '<?= URL_ROOT ?>/auth/register'
        };
    </script>
    <!-- AUTOSEARCH JQUERY -->
    <script src="<?php echo URL_ROOT; ?>/assets/js/autosearch.js"></script>
    <!-- LIBRARY HANDLEBAR -->
    <script src="<?php echo URL_ROOT; ?>/assets/js/library-handlebars.js"></script>
    <!-- PLAYER BAR JQUERY -->
    <script src="<?php echo URL_ROOT; ?>/assets/js/playerbar.js"></script>
</footer>
</body>

</html>