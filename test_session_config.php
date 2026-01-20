<?php

/**
 * Test script to verify session timeout settings
 */
require_once "assets/libs/session_init.php";

echo "Session Configuration Test\n";
echo "===========================\n";
echo "session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . " seconds\n";
echo "session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . " seconds\n";
echo "session.cache_expire: " . session_cache_expire() . " minutes\n";
echo "\n";
echo "Expected: 28800 seconds (8 hours)\n";
echo "In minutes: " . (ini_get('session.gc_maxlifetime') / 60) . " minutes\n";
echo "In hours: " . (ini_get('session.gc_maxlifetime') / 3600) . " hours\n";
