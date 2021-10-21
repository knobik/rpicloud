<?php

Route::prefix('jobs')->group(function () {
    Route::queueMonitor();
});
