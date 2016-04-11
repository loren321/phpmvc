<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    /*
    * returns a User model which is an author of this post.
    */
    public function author()
    {
        return $this->belongsTo('App\Models\User', 'author_id', 'id');
    }

    /*
    * Saves a post if it is valid
    *
    * returns an array
    */
    public function validateAndSave() {
        if ($this->text == "") {
            $status = "error";
            $message = "Text must not be empty.";
        } else if (strlen($this->text) > 140) {
            $status = "error";
            $message = "Text must not be longer than 140 characters.";
        }
        try {
            $this->save();
            $status = "success";
            $message = "Post is saved";
        } catch (Exception $e) {
            $status = "error";
            $message = $e->getMessage();
        }

        return ["status" => $status, "message" => $message];
    }

    /*
    * builds a link to the current post and returns it as a string
    */
    public function getLink() {
        $link = "http://" . $_SERVER['SERVER_NAME'] . "/stauses/" . $this->id;
        return $link;
    }

    /*
    * builds a PostDataObject from a current Posr model and returns it as an array.
    */
    public function getPostDataObject() {
        $id = $this->id;
        $text = $this->text;
        $user = $this->author()->first()->getSmallUserObject();
        $link = $this->getLink();
        $created_at = $this->created_at;
        $updated_at = $this->updated_at;

        $data = [
            "id" => $id,
            "text" => $text,
            "user" => $user,
            "links" => [
                "show" => $link
            ],
            "created_at" => $created_at,
            "updated_at" => $updated_at
        ];

        return $data;
    }

    /*
    * takes 3 integer parameters
    *
    * builds a timeline object and returns it as an array
    */
    public static function userTimeline($user_id, $limit, $offset) {
        $posts = self::where('author_id', $user_id)->skip($offset)->take($limit)->get();
        $status = "success";
        $message = "User posts.";
        $results = [];

        if ($posts->count() == 0) {
            $status = "error";
            $message = "No posts are found";
        }

        foreach ($posts as $post) {
            $post_data = $post->getPostDataObject();
            array_push($results, $post_data);
        }

        $self_link = "http://" . $_SERVER['SERVER_NAME'] . "/stauses/user-timeline/"
                    ."?user_id=" . $user_id
                    ."&limit=" . $limit
                    ."&offset=" . $offset;

        $next_offset = $offset + $limit;
        $next_link = "http://" . $_SERVER['SERVER_NAME'] . "/stauses/user-timeline/"
                    ."?user_id=" . $user_id
                    ."&limit=" . $limit
                    ."&offset=" . $next_offset;

        $prev_offset = $offset - $limit;
        if ($prev_offset >= 0) {
            $prev_link = "http://" . $_SERVER['SERVER_NAME'] . "/stauses/user-timeline/"
                        ."?user_id=" . $user_id
                        ."&limit=" . $limit
                        ."&offset=" . $prev_offset;
        }

        $total = $posts->count();

        $data = [
            "status" => $status,
            "message" => $message,
            "results" => $results,
            "links" => [
                "self" => $self_link,
                "next" => $next_link,
                "prev" => $prev_link
            ],
            "total" => $total
        ];
        return $data;
    }

    /*
    * takes 3 integer parameters
    *
    * builds a home timeline object and returns it as an array
    */
    public static function home($user_id, $limit, $offset) {
        $following_ids = \App\Models\User::find($user_id)->getFollowingIds();
        $posts = self::whereIn('author_id', $following_ids)->skip($offset)->take($limit)->get();
        $status = "success";
        $message = "User posts.";
        $results = [];

        if ($posts->count() == 0) {
            $status = "error";
            $message = "No posts are found";
        }

        foreach ($posts as $post) {
            $post_data = $post->getPostDataObject();
            array_push($results, $post_data);
        }

        $self_link = "http://" . $_SERVER['SERVER_NAME'] . "/stauses/user-timeline/"
                    ."?user_id=" . $user_id
                    ."&limit=" . $limit
                    ."&offset=" . $offset;

        $next_offset = $offset + $limit;
        $next_link = "http://" . $_SERVER['SERVER_NAME'] . "/stauses/user-timeline/"
                    ."?user_id=" . $user_id
                    ."&limit=" . $limit
                    ."&offset=" . $next_offset;

        $prev_offset = $offset - $limit;
        if ($prev_offset >= 0) {
            $prev_link = "http://" . $_SERVER['SERVER_NAME'] . "/stauses/user-timeline/"
                        ."?user_id=" . $user_id
                        ."&limit=" . $limit
                        ."&offset=" . $prev_offset;
        }

        $total = $posts->count();

        $data = [
            "status" => $status,
            "message" => $message,
            "results" => $results,
            "links" => [
                "self" => $self_link,
                "next" => $next_link,
                "prev" => $prev_link
            ],
            "total" => $total
        ];
        return $data;
    }
}
