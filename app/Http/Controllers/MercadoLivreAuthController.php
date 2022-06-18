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
    public function verify_already_exist_state_code($random_id){
        $test = DB::table("mercado_livre_user")->where('state', $random_id)->first();
        if(empty($test)){
            return ["status"=>false];//noexist
        }else{
            return ["status"=>true];//exist
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
        
        $verify_already_exist_status_code = MercadoLivreAuthController::verify_already_exist_state_code($random_id);
        $link_auth = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=".env("APP_ID")."&redirect_uri=".env("YOUR_URL")."&state=$random_id";
        if($verify_already_exist_status_code['status'] == true){
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
    public function get_first_code(){
            if(!empty($_GET["state"])){
                $random_id = $_GET["state"];
                $Veryfy_random_id = MercadoLivreAuthController::Veryfy_random_id($random_id);
                if(!empty($Veryfy_random_id)){
                    return $Veryfy_random_id;
                }
            }else{
                $Veryfy_random_id = MercadoLivreAuthController::Veryfy_random_id(NULL);
                if(!empty($Veryfy_random_id)){
                    return $Veryfy_random_id;
                }
            }
            $verify_already_exist_status_code = MercadoLivreAuthController::verify_already_exist_state_code($random_id);
            if($verify_already_exist_status_code['status'] == false){
                return [
                    "status"=>"error", 
                    "message"=>"hey brother, the state was not generated from my system, what are you doing?" 
                ];
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

            $result = json_decode(curl_exec($ch));
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            if(!empty($result->message)){
                echo $result->message . "<br>";
                if($result->message == "Error validating grant. Your authorization code or refresh token is invalid"){
                    return [
                        "status"=>"error", 
                        "message"=>"hey brother, The MercadoLivre doesn't like what you're doing. Are you trying to bypass the system? Be honest and don't repeat!" 
                    ];
                }
                //MercadoLivreAuthController::first_Auth();
            }elseif(!empty($result->access_token)){
                $user = DB::table('mercado_livre_user')->where('state',$random_id)->update([
                    'access_token'=>$result->access_token,
                    'token_type'=>$result->token_type,
                    'expires_in'=>time()+$result->expires_in,
                    'scope'=>$result->scope,
                    'user_id'=>$result->user_id,
                    'refresh_token'=>$result->refresh_token
                ]);
                return [
                    "status"=>"success", 
                    "message"=>"Your code has been generated successfully. Can you close this windows",
                    'refresh_token'=>$result->refresh_token
                ];
            }
    }
    
}
