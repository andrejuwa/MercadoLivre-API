<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MercadoLivreAuthController extends Controller
{
    public function index(){
        MercadoLivreAuthController::first_Auth();
    }
    public function Veryfy_random_id($random_id){
        if($random_id){
            $random_id = $random_id;
        }else{
            return ["status"=>"error","message"=>"My friend, this State is null. Your code needs a doctor, take it immediatly"];
        }
        if(strlen( $random_id)<=16){
            return ["status"=>"error","message"=>"My friend, this State is very bad. Please veryfy your state, do not be dumb"];
        }elseif(strlen($random_id)>=255){
            return ["status"=>"error","message"=>"hey brother, your security state is very big, do you want to corrupt my system? I'm watching huh?"];
        }elseif(!preg_match("/^([a-zA-Z0-9]+)$/", $random_id)){
            return ["status"=>"error","message"=>"wow what are you trying to do? Don't use special characters on my system, you're not crazy, are you?"]; 
        }elseif( (!env("APP_ID")) || (!env("YOUR_URL"))  || (!env("YOUR_URL"))){
            return ["status"=>"error","message"=>"My system is crashed, please retry later"];  
        }
    }
    public function get_link(){
        if($_POST["random_id"]){
            $random_id = $_POST["random_id"];
            $Veryfy_random_id = MercadoLivreAuthController::Veryfy_random_id($random_id);
            if(!empty($Veryfy_random_id)){
                return $Veryfy_random_id;
            }
        }
        
        $test = DB::table("mercado_livre_user")->where('state', $random_id)->first();
        $link_auth = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=".env("APP_ID")."&redirect_uri=".env("YOUR_URL")."&state=$random_id";
        if(!empty($test)){
            return [
                "status"=>"error",
                "message"=>"wow, are you trying to cheat on me? This code has already been used... But, is here your link",
                "link"=>$link_auth
            ];  
        }
        DB::table("mercado_livre_user")->insert(['state'=>$random_id]);
        return [
            "status"=>"Success", 
            "message"=>"You are a badass, everything is fine with you state", 
            "link"=>$link_auth,
            
        ];
    }
    public function get_code(){
            if((!$_GET["state"])||(strlen($_GET["state"])<16)){
                return "My friend, this State is very bad or null";
            }
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
                $user = DB::table('mercado_livre_user')->insert([
                    'state'=>$_GET["state"],
                    'access_token'=>$result->access_token,
                    'token_type'=>$result->token_type,
                    'expires_in'=>time()+$result->expires_in,
                    'scope'=>$result->scope,
                    'user_id'=>$result->user_id,
                    'refresh_token'=>$result->refresh_token
                ]);
            }elseif(!empty($result->message)){
                echo $result->message . "<br>";
                MercadoLivreAuthController::first_Auth();
            }
    }
    
}
