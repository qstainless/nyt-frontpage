<?php
declare(strict_types=1);

/**
 * Gets today's frontpage for The New York Times
 *
 * @return string
 */
function get_frontpage(): string
{
    // Exit if ImageMagick is not installed
    if (!check_imagemagick()) {
        die('ImageMagick is not installed. Unable to continue.');
    }

    // Define variables
    $file_name   = 'frontpage';
    $source_ext  = '.pdf';
    $display_ext = '.jpg';

    // set path to the most recently available NYT frontpage
    $frontpage = get_frontpage_info();

    $file_name = $file_name . '-' . $frontpage['show_date'];

    // Only fetch and convert the frontpage if it hasn't been previously fetched and converted
    if (!file_exists($file_name . $source_ext) || !file_exists($file_name . $display_ext)) {
        //  Delete old downloaded and converted frontpage images
        delete_old_frontpage();
        // fetches the frontpage and saves it
        fetch_frontpage($frontpage['path'], $file_name . $source_ext);

        /**
         * Converts the PDF to JPG with set density (dpi) and quality
         * Choose a lower dpi setting if bandwidth is a concern
         *
         * See https://imagemagick.org/script/convert.php for all options
         */
        exec('convert -density 150 ' . $file_name . $source_ext . ' -quality 80 ' . $file_name . $display_ext);
    }

    return $file_name . $display_ext;
}

/**
 * Checks to see if the frontpage for today exists or returns yesterday's frontpage
 * From:
 * https://alexanderklopping.medium.com/an-updated-daily-front-page-of-the-new-york-times-as-artwork-on-your-wall-3b28c3261478
 *
 * @return array
 */
function get_frontpage_info(): array
{
    $show_date = date('Y/m/d');
    $path      = 'https://static01.nyt.com/images/' . $show_date . '/nytfrontpage/scan.pdf';

    // check if there is any today
    $file_headers = @get_headers($path);

    // if there's none today, get yesterday's
    if (!$file_headers || $file_headers[0] === 'HTTP/1.1 404 Not Found') {
        $show_date = date('Y/m/d', strtotime('-1 days'));
        $path      = 'https://static01.nyt.com/images/' . $show_date . '/nytfrontpage/scan.pdf';
    }

    return [
        'path'      => $path,
        'show_date' => date('Y-m-d', strtotime($show_date)),
    ];
}

/**
 * Fetches the frontpage from the specified path and saves it to the specified file
 * See https://www.php.net/manual/en/book.curl.php
 *
 * @param $source_url
 * @param $source_file
 */
function fetch_frontpage($source_url, $source_file): void
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $source_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    $result = curl_exec($curl);

    save_file($source_file, $result);
}

/**
 * Saves the frontpage
 * See https://www.php.net/manual/en/function.file-put-contents.php
 *
 * @param $filename
 * @param $data
 */
function save_file($filename, $data): void
{
    file_put_contents($filename, $data);
}

/**
 * Checks if ImageMagick is installed on the server
 *
 * @return bool
 */
function check_imagemagick(): bool
{
    exec('which convert', $output, $rcode);

    // returns true if ImageMagick is installed
    return $rcode === 0;
}

/**
 * Deletes old downloaded and converted frontpage images
 */
function delete_old_frontpage(): void
{
    foreach (glob('frontpage*') as $filename) {
        if (preg_match('/(frontpage-)\d{4}-\d{2}-\d{2}.(jpg|pdf)/', $filename)) {
            unlink($filename);
        }
    }
}
