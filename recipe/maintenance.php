<?php

/**
 * Deployer recipe to enable or disable maintenance mode on
 * remote host using JontsaMaintenanceBundle.
 */

namespace Deployer;

task('maintenance:enable', function() {
    run("cd {{current_path}} && bin/console jontsa:maintenance enable");
})->desc('Enables maintenance mode on remote server');

task('maintenance:disable', function() {
    run("cd {{current_path}} && bin/console jontsa:maintenance disable");
})->desc('Disables maintenance mode on remote server');