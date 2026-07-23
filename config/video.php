<?php

return [
    'duration' => env('VIDEO_DURATION', 20),
    'max_size_kb' => env('VIDEO_MAX_SIZE_KB', 51200),
    'min_size_kb' => env('VIDEO_MIN_SIZE_KB', 30),
    'disk' => env('VIDEO_DISK', 'local'),
];
