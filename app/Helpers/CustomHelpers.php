<?php

if (!function_exists('live_asset')) {
    /**
     * Generate a URL for a local asset.
     *
     * @param string $path
     * @return string
     */
    function live_asset($path)
    {
        return env('LIVE_ASSET_URL') . '/' . ltrim($path, '/'); // Ensure there's no double slash
    }
}
