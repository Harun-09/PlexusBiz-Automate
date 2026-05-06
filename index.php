<?php

/**
 * Shared-hosting entry point.
 *
 * Keep the real Laravel front controller inside public/ so the project can
 * be deployed from the repository root on the same domain when needed.
 */

header('Location: public/');
exit;
