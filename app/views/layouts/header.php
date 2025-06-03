<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <!-- HANDLEBARS -->
    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <!-- FONT-AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- HOME PAGE MAIN STYLE -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/index.css">
    <!-- playerbar style -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/playerbar.css">
    <!-- song cards style -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/songCards.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.8/handlebars.min.js"></script>

</head>

<body>
    <?php
    // Only show navbar if not explicitly hidden
    if (!isset($data['hide_navbar']) || !$data['hide_navbar']) {
        require_once APP_ROOT . '/app/views/partials/navbar.php';
    }
    ?>