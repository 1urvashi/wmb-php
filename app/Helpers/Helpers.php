<?php

function cdn($asset) {
    //Check if we added cdn's to the config file
    if (!config('app.cdn')) {
        return asset($asset);
    }

    //Get file name & cdn's
    $cdns = config('app.cdn');
    $assetName = basename($asset);

    //remove any query string for matching
    $assetName = explode("?", $assetName);
    $assetName = $assetName[0];

    //Find the correct cdn to use
    foreach ($cdns as $cdn => $types) {
        if (preg_match('/^.*\.(' . $types . ')$/i', $assetName)) {
            return cdnPath($cdn, $asset);
        }
    }

    //If we couldnt match a cdn, use the last in the list.
    end($cdns);

    return cdnPath(key($cdns), $asset);
}

function cdnPath($cdn, $asset) {
    return rtrim($cdn, "/") . "/" . ltrim($asset, "/");
}

function unique_random($table, $col, $chars = 7, $prefix = '') {
    $unique = false;
    // Store tested results in array to not test them again
    $tested = [];
    do {
        if (!empty($prefix)) {
            $chars = $chars - strlen($prefix);
            $random = $prefix . str_random($chars);
        } else {
            // Generate random string of characters
            $random = str_random($chars);
        }
        // Check if it's already testing
        // If so, don't query the database again
        if (in_array($random, $tested)) {
            continue;
        }
        // Check if it is unique in the database
        $count = DB::table($table)->where($col, '=', $random)->count();
        // Store the random character in the tested array
        // To keep track which ones are already tested
        $tested[] = $random;
        // String appears to be unique
        if ($count == 0) {
            // Set unique to true to break the loop
            $unique = true;
        }
        // If unique is still false at this point
        // it will just repeat all the steps until
        // it has generated a random string of characters
    } while (!$unique);
    return $random;
}