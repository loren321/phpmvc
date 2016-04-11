<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    // list of parameters that are required
    static $required = [
        'nickname',
        'email',
        'password'
    ];

    // list of parameters maximum lengths
    static $max = [
        'display_name' => 100,
        'nickname' => 25,
        'email' => 150,
        'password' => 15
    ];

    // list of parameters minimum lengths
    static $min = [
        'password' => 6
    ];

    public function followers() {
        return $this->belongsToMany('App\Models\User', 'follows', 'follower_id', 'followed_id');
    }

    public function follows() {
        return $this->belongsToMany('App\Models\User', 'follows', 'followed_id', 'follower_id');
    }

    /*
    * returns an array of id's of users that the current user follows
    */
    public function getFollowingIds() {
        $user_ids = [];
        foreach ($this->follows() as $follow) {
            array_push($user_ids, $follow->followed_id);
        }
        return $user_ids;
    }

    /*
    * validates a user and returns message
    */
    public function validate($parameters) {

        $status = "success";
        $message = "You are registered.";

        // check if any of required parameters is missing
        foreach (self::$required as $parameter) {
            if (!array_key_exists($parameter, $parameters)){
                $message = $parameter . " is required.";
                $status = "error";
            }
        }

        // check if any of parameters is longer than it should be
        foreach (self::$max as $parameter => $value) {
            if (isset($parameters[$parameter]) && strlen($parameters[$parameter]) > $value) {
                $message = $parameter . " must be shorter than " . $value;
                $status = "error";
            }
        }

        // check if any of the parameters is shorter than it should be
        foreach (self::$min as $parameter => $value) {
            if (isset($parameters[$parameter]) && strlen($parameters[$parameter]) < $value) {
                $message = $parameter . " must be longer than " . $value;
                $status = "error";
            }
        }

        // check the password confirmation
        if ($parameters['password'] != $parameters['password_confirm']) {
            $message = "passwords do not match";
            $status = "error";
        }

        // check if email is valid
        if (!filter_var($parameters['email'], FILTER_VALIDATE_EMAIL)) {
            $message = "email is invalid";
            $status = "error";
        }

        // check if terms are accepted
        if (!$parameters['terms']) {
            $message = "the terms must be accepted";
            $status = "error";
        }

        return ["status" => $status, "message" => $message];
    }

    /*
    * saves a user to a database if valid.
    */
    public function validateAndSave($parameters) {
        $validation = $this->validate($parameters);

        if ($validation['status'] = "success") {
            $this->display_name = $parameters['display_name'];
            $this->nickname = $parameters['nickname'];
            $this->email = $parameters['email'];
            $this->password = password_hash($parameters['password'], PASSWORD_BCRYPT);
            $this->save();
        }

        return $validation;
    }

    /*
    * builds a link for the user profile and returns it a string
    */
    public function getLink() {
        $link = "http://" . $_SERVER['SERVER_NAME'] . "/user/" . $this->id;
        return $link;
    }

    /*
    * builds a link for the user timeline and returns it as a string
    */
    public function getTimelineLink($limit, $offset) {
        $link = "http://" . $_SERVER['SERVER_NAME'] . "/stauses/user-timeline/"
                    ."?user_id=" . $this->id
                    ."&limit=" . $limit
                    ."&offset=" . $next_offset;
        return $link;
    }

    /*
    * returns an Image model for the users cover image
    */
    public function coverImage() {
        return $this->hasOne('App\Models\Image', 'id', 'cover_image');
    }

    /*
    * returns an Image model for the users profile image
    */
    public function profileImage() {
        return $this->hasOne('App\Models\Image', 'id', 'profile_image');
    }

    /*
    * takes an uploaded file as a parameter and saves it to a directory
    * returns the id
    */
    public function saveProfileImage($file) {
        $directory = __DIR__ . "/../../public/img/";

        $extension = $file->guessExtension();
        if (!in_array($extension, \App\Models\Image::$allowed_extensions)) {
            throw new Exception("This extension is not allowed.");
        }

        $filename = "profile" . $this->id . "." . $extension;
        $file->move($directory, $filename);

        $image = new \App\Models\Image;
        $image->filename = $filename;
        $image->save();

        return $image->id;
    }

    /*
    * takes an uploaded file as a parameter and saves it to a directory
    * returns the id
    */
    public function saveCoverImage($file) {
        $directory = __DIR__ . "/../../public/img/";

        $extension = $file->guessExtension();
        if (!in_array($extension, \App\Models\Image::$allowed_extensions)) {
            throw new Exception("This extension is not allowed.");
        }

        $filename = "cover" . $this->id . "." . $extension;
        $file->move($directory, $filename);

        $image = new \App\Models\Image;
        $image->filename = $filename;
        $image->save();

        return $image->id;
    }

    /*
    * builds a SmallUserObject and returns it as an aray
    */
    public function getSmallUserObject() {
        $id = $this->id;
        $display_name = $this->display_name;
        $nickname = $this->nickname;
        $email = $this->nickname;
        if ($this->profile_image == 0) {
            $profile_image = NULL;
        } else {
            $profile_image = $this->profileImage->getLink();
        }
        if ($this->cover_image == 0) {
            $cover_image = NULL;
        } else {
            $cover_image = $this->coverImage->getLink();
        }
        $profile_link = $this->getLink();

        $data = [
            "id" => $id,
            "display_name" => $display_name,
            "nickname" => $nickname,
            "email" => $email,
            "images" => [
                "profile" => $profile_image,
                "cover" => $cover_image
            ],
            "links" => [
                "profile" => $profile_link
            ]
        ];

        return $data;
    }

    /*
    * builds a BigUserObject and returns it as an aray
    */
    public function getBigUserObject() {
        $id = $this->id;
        $display_name = $this->display_name;
        $nickname = $this->nickname;
        $email = $this->nickname;
        $description = $this->description;
        $followers_count = $this->followers()->count();
        $follows_count = $this->follows()->count();
        if ($this->profile_image == 0) {
            $profile_image = NULL;
        } else {
            $profile_image = $this->profileImage->getLink();
        }
        if ($this->cover_image == 0) {
            $cover_image = NULL;
        } else {
            $cover_image = $this->coverImage->getLink();
        }
        $profile_link = $this->getLink();
        $timeline_link = $this->getTimelineLink(20, 0);

        $data = [
            "id" => $id,
            "display_name" => $display_name,
            "nickname" => $nickname,
            "email" => $email,
            "description" => $description,
            "followers_count" => $followers_count,
            "follows_count" => $follows_count,
            "images" => [
                "profile" => $profile_image,
                "cover" => $cover_image
            ],
            "links" => [
                "profile" => $profile_link,
                "usertimeline" => $timeline_link,
            ]
        ];

        return $data;
    }
}
