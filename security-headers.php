<?php
  header("Content-Security-Policy: default-src 'self'; connect-src 'self' https://hive.splitbee.io/; script-src 'self' 'unsafe-inline' https://cdn.splitbee.io/; style-src 'self' 'unsafe-inline' https://unpkg.com; font-src 'self'; img-src 'self' data:; media-src 'self'; frame-src 'self'; base-uri 'self'; form-action 'self' http://www.uscm.se; frame-ancestors 'none'; object-src 'none';");
  header("Permissions-Policy: accelerometer=(), autoplay=(), camera=(), display-capture=(), fullscreen=(), geolocation=(), gyroscope=(), microphone=(), payment=(), storage-access=(), web-share=(), xr-spatial-tracking=()");
  header("Referrer-Policy: strict-origin-when-cross-origin");
  header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload");
  header("X-Content-Type-Options: nosniff");
  header("X-DNS-Prefetch-Control: on");
  header("X-Frame-Options: DENY");
  header("X-XSS-Protection: 1; mode=block");
?>
