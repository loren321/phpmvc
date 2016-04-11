<?php

namespace App\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    /*
    * this method handles the route for GET /user/:id
    *
    * parameter is big_data <bool>
    */
    public function show($parameters) {
        \App\Controllers\AuthController::checkLogin();

        $id = $parameters['id'];
        $request = Request::createFromGlobals();
        $big_data = $request->query->get('big_data');
        try {
            if($big_data === "false" || $big_data === "0") {
                $result = User::findOrFail($id)->getSmallUserObject();
            } else {
                $result = User::findOrFail($id)->getBigUserObject();
            }
            $status = "success";
            $message = "User.";
        } catch (ModelNotFoundException $e) {
            $status = "error";
            $message = "User with that id is not found.";
        }

        $response = ["status" => $status, "message" => $message, "result" => $result];

        header('Content-Type: application/json');
        return json_encode($response);
    }

    /*
    * this method handles GET /user/search
    *
    * parameters are:
    * - query <string>
    * - limit <num>
    * - offset <num>
    */
    public function search() {
        \App\Controllers\AuthController::checkLogin();

        $request = Request::createFromGlobals();

        $limit = $request->query->get('limit');
        $offset = $request->query->get('offset');

        if ($request->query->has('query')) {
            $query = $request->query->get('query');
            $users = User::where('display_name', 'LIKE', '%$query%')->skip($offset)->take($limit)->get();;
        } else {
            $users = User::skip($offset)->take($limit)->get();;
        }


        $results = [];

        foreach ($users as $user) {
            array_push($results, $user->getSmallUserObject());
        }

        header('Content-Type: application/json');
        return json_encode(["status" => "success", "results" => $results]);
    }

    /*
    * this method handles the POST /user/:id/edit route.
    *
    * optional parameters are:
    * - display_name <string>
    * - description <text>
    * - cover_image <file>
    * - profile_image <file>
    *
    */
    public function update($parameters) {
        \App\Controllers\AuthController::checkLogin();

        $id = $parameters["id"];
        $user = User::find($id);

        $request = Request::createFromGlobals();

        $display_name = $request->request->get('display_name');
        $description = $request->request->get('description');
        $profile_image = $request->files->get('profile_image');
        $cover_image = $request->files->get('cover_image');

        if ($display_name != NULL) {
            $user->display_name = $display_name;
        }

        if ($description != NULL) {
            $user->description = $description;
        }

        try {
            if ($profile_image != NULL) {
                $profile_image_id = $user->saveProfileImage($profile_image);
                $user->profile_image = $profile_image_id;
            }

            if ($cover_image != NULL) {
                $cover_image_id = $user->saveCoverImage($cover_image);
                $user->cover_image = $cover_image_id;
            }

            $user->save();

            $status = "success";
            $message = "User is edited succesfully.";
        } catch (Exception $e) {
            $status = "error";
            $message = $e->getMessage();
        }

        $response = ["status" => $status, "message" => $message];

        header('Content-Type: application/json');
        return json_encode($response);
    }

    /*
    * this method handles the route POST /user/:id/follow
    */
    public function follow($parameters) {
        \App\Controllers\AuthController::checkLogin();

        $id = $parameters['id'];

        $current_user_id = $this->session->get('user_id');

        try {
            $current_user = User::find($current_user_id);
            $current_user->followers()->attach($id);

            $status = "success";
            $message = "User is followed succesfully.";
        } catch (Exception $e) {
            $status = "error";
            $message = $e->getMessage();
        }

        $response = ["status" => $status, "message" => $message];

        header('Content-Type: application/json');
        return json_encode($response);
    }

    /*
    * this method handles the route POST /user/:id/unfollow
    */
    public function unfollow($parameters) {
        \App\Controllers\AuthController::checkLogin();

        $id = $parameters['id'];

        $current_user_id = $this->session->get('user_id');

        try {
            $current_user = User::find($current_user_id);
            $current_user->followers()->detach($id);

            $status = "success";
            $message = "User is unfollowed succesfully.";
        } catch (Exception $e) {
            $status = "error";
            $message = $e->getMessage();
        }

        $response = ["status" => $status, "message" => $message];

        header('Content-Type: application/json');
        return json_encode($response);
    }

}
