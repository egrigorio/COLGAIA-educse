<!DOCTYPE html>
<html data-theme="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'default'; ?>" class="bg-primary" lang="en" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@event-calendar/build/event-calendar.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@event-calendar/build/event-calendar.min.js"></script>
    <link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/calendario.css' ?>">
    <link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/styles.css' ?>">    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>