<?php

namespace App\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class PostController extends BaseController
{
    /*
    * handles the POST /statuses/create route
    * takes one parameter:
    * text <string>
    */
    public function create() {
        \App\Controllers\AuthController::checkLogin();

        $post = new Post;
        $request = Request::createFromGlobals();

        $text = $request->request->get('text');

        $post->text = $text;
        $post->author_id = $this->session->get('user_id');
        $response = $post->validateAndSave();

        header('Content-Type: application/json');
        return json_encode($response);
    }

    /*
    * handles the GET /statuses/user-timeline
    *
    * takes 3 parameters:
    * - user_id <num>
    * - limit <num>
    * - offset <num>
    *
    * returns a list of PostDataObjects
    */
    public function userTimeline() {
        \App\Controllers\AuthController::checkLogin();

        $request = Request::createFromGlobals();
        $user_id = $request->query->get('user_id');
        $limit = $request->query->get('limit');
        $offset = $request->query->get('offset');

        if ($user_id == NULL || $limit == NULL || $offset == NULL) {
            header('Content-Type: application/json');
            return json_encode(["status" => "error", "message" => "Some parameters are missing."]);
        }

        $response = Post::userTimeline($user_id, $limit, $offset);

        header('Content-Type: application/json');
        return json_encode($response);
    }

    /*
    * handles the GET /statuses/home
    *
    * takes parameters:
    * - limit <num>
    * - offset <num>
    * and returns a list of PostDataObjects
    */
    public function home() {
        \App\Controllers\AuthController::checkLogin();
        
        $request = Request::createFromGlobals();

        $user_id = $this->session->get('user_id');
        $limit = $request->query->get('limit');
        $offset = $request->query->get('offset');

        if ($limit == NULL || $offset == NULL) {
            header('Content-Type: application/json');
            return json_encode(["status" => "error", "message" => "Some parameters are missing."]);
        }

        $response = Post::home($user_id, $limit, $offset);

        header('Content-Type: application/json');
        return json_encode($response);
    }

    /*
    * handles the GET /statuses/:id
    *
    * takes no parameters
    *
    * returns a single PostDataObject
    */
    public function show($parameters) {
        \App\Controllers\AuthController::checkLogin();

        $id = $parameters['id'];
        try {
            $post = Post::findOrFail($id);
            $result = $post->getPostDataObject();
            $status = "success";
            $message = "Post.";
        } catch (ModelNotFoundException $e) {
            $status = "error";
            $message = $e->getMessage();
        }

        $response = ["status" => $status, "message" => $message, "result" => $result];

        header('Content-Type: application/json');
        return json_encode($response);
    }

}
