<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Image extends Eloquent
{
    /*
    * file extensions allowed when saving an image file
    */
    static $allowed_extensions = [
        "png",
        "jpg",
        "jpeg",
        "gif"
    ];

    /*
    * builds a link for the image and returns it as a string.
    */
    public function getLink() {
        $filename = $this->filename;
        $directory = "img/";

        $link = "http://" . $_SERVER['SERVER_NAME'] . '/' . $directory . $filename;
        return $link;
    }

}
