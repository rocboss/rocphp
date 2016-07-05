<?php
// System Bootstrap File.
require '../../system/Roc.php';

Roc::set(array_merge(
    // System infrastructure configuration
    require '../../app/config/base.php',

    // Database configuration
    require '../../app/config/database.php',

    // Miscellaneous configuration
    require '../../app/config/others.php'
));

// Route mapping
Roc::set('system.router', require '_router.php');

// Automatically load path
Roc::path(Roc::get('system.controller.path'));
Roc::path(Roc::get('system.service.path'));
Roc::path(Roc::get('system.model.path'));
Roc::path(Roc::get('system.vendor.path'));

// Initialization
Roc::before('start', ['Bootstrap', 'init']);
Roc::start();
