<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Models\User;

class AuthController extends BaseController
{
    /*
    * this method handles the route POST /register
    * it validates a user and saves it to a database
    *
    * it takes the following parameters:
    * - disply_name <string>
    * - email <string>
    * - password <string>
    * - password_confirm <string>
    * - terms <bool>
    */
    static function register() {
        $request = Request::createFromGlobals();
        $post_parameters = $request->request->all();
        $user = new User;

        $response = $user->validateAndSave($post_parameters);

        header('Content-Type: application/json');
        return json_encode($response);
    }

    /*
    * this method handles the route POST /login
    * it sets the login session
    *
    * it takes the following parameters:
    * nickname <string>
    * password <string>
    */
    static function login() {
        $request = Request::createFromGlobals();

        $nickname = $request->request->get('nickname');
        $password = $request->request->get('password');

        $user = User::where('nickname', $nickname)->first();

        if($user == NULL) {
            $status = "error";
            $message = "User with that nickname doesn't exist.";
        } else {
            $password_hash = $user->password;
            if (!password_verify($password, $password_hash)) {
                $status = "error";
                $message = "Wrong password.";
            } else {
                $session = new Session();
                $session->set('user_id', $user->id);

                $status = "success";
                $message = "Logged in successfully";
                $user_object = $user->getBigUserObject();
            }
        }

        $response = ["status" => $status, "message" => $message, "user" => $user_object];

        header('Content-Type: application/json');
        return json_encode($response);
    }

    /*
    * this method is called from other contoller classes to check if a user is logged in
    * if the user is not logged in it shows a json error message and exits.
    */
    public static function checkLogin() {
        $session = new Session();
        $user_id = $session->get('user_id');
        if ($user_id === NULL) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "You are not logged in."]);
            die();
        }
    }

}
