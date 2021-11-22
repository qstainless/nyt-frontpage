<?php
declare(strict_types=1);

echo '<div style="font-family: monospace">';

echo '<p>Test for version and location of ImageMagick </p>';

exec('which convert', $output, $rcode);

if ($rcode === 0) {
    echo '<p>$ type convert<br>';
    echo '<span style="color:blue">';
    system('type convert');
    echo '</span></p>';

    echo '<p>$ convert -version<br>';

    exec('convert -version', $output, $rcode);

    $version = '';

    foreach ($output as $item) {
        $version .= $item . '<br>';
    }

    echo '<span style="color:blue">' . $version . '</span>';
} else {
    echo '<p>$ which convert<br>';
    echo '<span style="color:blue">convert not found</span>';
}

echo '</p>';

echo '</div>';
