<?php

Route::get('/liveblade/refresh/{component}', function ($component) {
    return view("vendor.liveblade.{$component}");
