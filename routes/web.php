<?php

// shared.php must load before public.php: it registers static /stickers/* paths
// (e.g. /stickers/my-stickers, /stickers/issue) that would otherwise be swallowed
// by public.php's /stickers/{sticker} wildcard route if that matched first.
require __DIR__ . '/shared.php';
require __DIR__ . '/public.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
