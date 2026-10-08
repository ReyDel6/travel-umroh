<?php
// Pintu pengaman docroot: memastikan "/" selalu memuat front controller
// meskipun mod_rewrite nonaktif (DirectoryIndex index.php).
require __DIR__ . '/public/index.php';