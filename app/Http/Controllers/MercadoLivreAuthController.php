<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MercadoLivreAuthController extends Controller
{
    public function index(){
        MercadoLivreAuthController::first_Auth();
    }
    public function first_Auth(){
        $RANDOM_ID = "asdsa";
        $link_auth = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=".env("APP_ID")."&redirect_uri=".env("YOUR_URL")."&state=$RANDOM_ID";
        echo "<a href='$link_auth'>redirect link </a>";
    }
    public function get_code(){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.mercadolibre.com/oauth/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&client_id=".env("APP_ID")."&client_secret=".env("SECRET_KEY")."&code=". $_GET["code"] . "&redirect_uri=".env("YOUR_URL"));

            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $result = json_decode($result);
            if(!empty($result->access_token)){
                echo $result->access_token . "<br>";
                echo $result->token_type . "<br>";
                echo $result->expires_in . "<br>";
                echo $result->scope . "<br>";
                echo $result->user_id . "<br>";
                echo $result->refresh_token . "<br>";
            }elseif(!empty($result->message)){
                echo $result->message;
            }
    }
    
}
