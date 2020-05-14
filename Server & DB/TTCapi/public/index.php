<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Rather than directly use PHP for GET, POST req will make use of Slim framework 
//Get from following Url, defn inline func 
//Browser uses Url encoding to connect name/val pairs with =, sep pairs with &: name1=value1&name2=value2..


/*
    Used to define API endpoints 
*/

require __DIR__ . '/../vendor/autoload.php';

require '../includes/DbOperations.php';


//$app = new \Slim\App;
//Instantiate with error logger 
$config = ['settings' => ['displayErrorDetails' => true]]; 
$app = new Slim\App($config);


/*
    endpoint: createuser
    params: email, password, balance, first_name, last_name
    method: POST
*/
$app->post('/createuser', function(Request $request, Response $response){
    if(!areParamsMissing(array('email', 'password', 'balance', 'first_name', 'last_name'), $response)) {
        //slim converts JSON request to associative arr 
        $request_data = $request->getParsedBody(); //check POST params and if all are filled 
        $email = $request_data['email'];
        $password = $request_data['password'];
        $balance = $request_data['balance'];
        $first_name = $request_data['first_name'];
        $last_name = $request_data['last_name'];
        //Password encryption 
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $database = new DbOperations;
        $result = $database->createUser($email, $hash_password, $balance, $first_name, $last_name);
        if($result == USER_CREATED) {
            $msg = array();
            $msg['error'] = false;
            $msg['message'] = 'User creation successful';

            $response->write(json_encode($msg));
            return $response->withHeader('Content-type', 'application/json')->withStatus(201);
        }else if($result == USER_FAILURE) {
            $msg = array();
            $msg['error'] = true;
            $msg['message'] = 'Error';

            $response->write(json_encode($msg));
            return $response->withHeader('Content-type', 'application/json')->withStatus(422);
        }else if($result == USER_EXISTS) {
            $msg = array();
            $msg['error'] = true;
            $msg['message'] = 'User exists';

            $response->write(json_encode($msg));
            return $response->withHeader('Content-type', 'application/json')->withStatus(201);

        }
    }
});

$app->post('/userlogin', function(Request $request, Response $response){
    if(!areParamsMissing(array('email', 'password'), $response)) {
        $request_data = $request->getParsedBody(); //check POST params and if all are filled, after POST sent, hashing func applied
        $email = $request_data['email'];
        $password = $request_data['password'];

        $database = new DbOperations;
        $result = $database->userLogin($email, $password); //call db ops to login & check
        if($result == USER_AUTHENTICATED) {
            $user = $database->getUserByEmail($email); //after successful login, retrieve user data to display 
            $response_data = array();
            $response_data['error'] = false;
            $response_data['msg'] = 'Login successful';
            $response_data['user'] = $user;
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')->withStatus(200);

        } else if($result == USER_NOT_FOUND) {
            $response_data = array();
            $response_data['error'] = true;
            $response_data['msg'] = 'User not found';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')->withStatus(404);
            

        } else if($result == USER_NOT_AUTHENTICATED){
            $response_data = array();
            $response_data['error'] = true;
            $response_data['msg'] = 'Invalid username or password, try again';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')->withStatus(403);

        }
        
    }
    return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->post('/changebalance', function(Request $request, Response $response) {
    $request_data = $request->getParsedBody();
    $balance = $request_data['balance'];
    $email = $request_data['email'];
    $database = new DbOperations;

    $result = $database->currentBalanceChange($email, $balance);
    if($result == BALANCE_UPDATE){
        $user = $database->getUserByEmail($email);

        $response_data = array();
        $response_data['error'] = false;
        $response_data['msg'] = 'Successful balance update';
        $response_data['user'] = $user;
        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json');
    }
    else if($result == USER_FAILURE){
        $user = $database->getUserByEmail($email);

        $response_data = array();
        $response_data['error'] = true;
        $response_data['msg'] = 'User not found';
        $response_data['user'] = $user;
        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json');
    }
    else if($result == BALANCE_FAILURE) {
        $user = $database->getUserByEmail($email);
        $response_data = array();
        $response_data['error'] = true;
        $response_data['msg'] = 'Failed balance update; Enter a different amount';
        $response_data['user'] = $user;
        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json');
    }
});

//Confirm all params included for call to db 
function areParamsMissing($required_params, $response) {
    $error = false;
    $error_params = ''; //check missing params
    $request_params = $_REQUEST; //get request params; built-in var to collect data 
    //check required_params of call 
    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params <= 0)){ 
            $error = true;
            $error_params .= $param . ',';
        } //param not in set (missing or no length)
    }
    if($error){
        $error_detail = array();
        //Using associative arr that has key-val notation (index = key; assn val is bool); use for msg out, formatting to encode to JSON 
        $error_detail['error'] = true;
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -1) . ' are missing or empty'; //remove comma (last 2 chars)
        $response->write(json_encode($error_detail)); 
    }
    return $error;
}

$app->run();
?>