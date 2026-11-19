<?php

return [

    // Hosts that show the "coming soon" page instead of the site (comma-separated, e.g. klevers.eu,www.klevers.eu).
    // Empty = the site is open on every host. Admin, robots.txt and /up stay available.
    'coming_soon_hosts' => array_values(array_filter(array_map(
        fn ($host) => strtolower(trim($host)),
        explode(',', (string) env('COMING_SOON_HOSTS', '')),
    ))),

];
