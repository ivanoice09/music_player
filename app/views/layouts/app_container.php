<div id="app-content">
    <?php
    // Content will be loaded here dynamically
    if (!empty($content)) {
        echo $content;
    } else {
        // Default content when accessed directly
        require_once APP_ROOT . '/app/views/home/index.php';
    }
    ?>
</div>