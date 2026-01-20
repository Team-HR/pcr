<?php

/**
 * Session Configuration
 * This file centralizes session settings for the application.
 * Session timeout is set to 8 hours (28800 seconds).
 */

// Set session lifetime to 8 hours (28800 seconds)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// Optional: Set session cache expiration
session_cache_expire(480); // 480 minutes = 8 hours
