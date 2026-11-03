<?php

/*
 * Vercel's entry point.
 *
 * Vercel invokes a PHP function rather than serving a web root, so every
 * request is routed here by vercel.json and handed straight to Laravel's own
 * front controller. Nothing else belongs in this file.
 *
 * Unused unless the app is deployed to Vercel — see docs/deploy-vercel.md.
 */

require __DIR__.'/../public/index.php';
