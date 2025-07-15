#!/bin/bash
cd /home/automusic.win/public_html
php artisan schedule:run >> /home/automusic.win/log/laravel-scheduler.log 2>&1