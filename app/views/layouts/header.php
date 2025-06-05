<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <!-- BOOTSTRAP ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- FONT-AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- HOME PAGE MAIN STYLE -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/index.css">
    <!-- PLAYERBAR style -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/playerbar.css">
    <!-- SONG CARDS style -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/songCards.css">

    <!-- NAVBAR STYLE 2.0 -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/sidenavbar.css">
    <!-- SEARCH BAR -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/searchbar.css">

    <!-- LIBRARY -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/library.css">
    <!-- PLAYLIST -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/playlist.css">

    <!-- HANDLEBARS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.8/handlebars.min.js"></script>
</head>

<body class="<?php echo isset($data['is_music_page']) ? 'music-layout' : 'default-layout'; ?>">
    <?php
    // Only show navbar if not explicitly hidden
    if (!isset($data['hide_navbar']) || !$data['hide_navbar']) {
        require_once APP_ROOT . '/app/views/partials/sidenavbar.php';
        require_once APP_ROOT . '/app/views/partials/floatingSearchBar.php';
    }
    ?>