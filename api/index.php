<?php
require_once __DIR__ . '/../vendor/autoload.php';

use API\Entity\Videos;
use API\Entity\Playlists;
use API\Config\Database;

function deliver_response($response)
{

    // Define HTTP responses
    $httpResponseCode = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    // Set HTTP Response
    header('HTTP/1.1 ' . $response['status'] . ' ' . $httpResponseCode[$response['status']]);
    // Set HTTP Response Content Type
    header('Content-Type: application/json; charset=utf-8');
    // Format data into a JSON response
    $jsonResponse = json_encode($response['data']);
    // Deliver formatted data
    echo $jsonResponse;
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Set default HTTP response of 'Not Found'
$response['status'] = 404;
$response['data'] = null;
$urlArray = explode('/', $_SERVER['REQUEST_URI']);

array_shift($urlArray); // remove first value as it's empty
// remove 2nd and 3rd array, because it's directory
array_shift($urlArray); // 2nd = 'Raiponce'
array_shift($urlArray); // 3rd = 'api'
// get the action (resource, collection)
$action = $urlArray[0];
// get the method
$method = $_SERVER['REQUEST_METHOD'];

if (empty($action)) {
    deliver_response(array('status' => 400, 'data' => 'chose an action'));
    exit;
}

$oPath = "\API\Entity\\" . ucfirst($action);
if (class_exists($oPath)) {
    $object = new $oPath($db);
} else {
    deliver_response(array('status' => 400, 'data' => 'chose a valid action'));
}

switch ($method) {
    case 'GET':
        if (!isset($urlArray[1])) {
            // METHOD : GET api/$object
            $data = $object->getAll();
            $response['status'] = 200;
            $response['data'] = $data;
        } elseif (!isset($urlArray[2])) {
            // METHOD : GET api/$object/:id
            $id = $urlArray[1];
            $data = $object->get($id);
            if (empty($data)) {
                $response['status'] = 404;
                $response['data'] = array('error' => 'An error has occurred');
            } else {
                $response['status'] = 200;
                $response['data'] = $data;
            }
        } else {
            // METHOD : GET api/$object/:id/$object2
            $object2 = $urlArray[2];
            $id = $urlArray[1];
            $newAction = 'get' . $object2 . 'from' . $action;
            $data = $object->$newAction($id);
            if (empty($data)) {
                $response['status'] = 404;
                $response['data'] = array('error' => 'An error has occurred');
            } else {
                $response['status'] = 200;
                $response['data'] = $data;
            }
        }
        break;
    case 'POST':
        // METHOD : POST api/$object
        // get post from client
        $json = file_get_contents('php://input');
        $post = json_decode($json, true); // decode to array
        // check input completeness
        if (!isset($urlArray[1])) {
            if (empty($post)) {
                $response['status'] = 400;
                $response['data'] = array('error' => 'no data send');
            } else {
                $status = $object->insert($post);
                if ($status['status'] == 1) {
                    $response['status'] = 201;
                    $response['data'] = $status['data'];
                } else {
                    $response['status'] = 400;
                    $response['data'] = array('error' => 'An error has occurred');
                }
            }
        } else {
            $object2 = $urlArray[2];
            $id = $urlArray[1];
            $newAction = 'add' . $object2 . 'to' . $action;
            $status = $object->$newAction($post);
            if ($status['status'] == 1) {
                $response['status'] = 201;
                $response['data'] = $status['data'];
            } else {
                $response['status'] = 400;
                $response['data'] = array('error' => 'An error has occurred');
            }
        }
        break;
    case 'PUT':
        // METHOD : PUT api/$object/:id
        if (isset($urlArray[1])) {
            $id = $urlArray[1];
            // check if id exist in database
            $data = $object->get($id);
            if (empty($data)) {
                $response['status'] = 404;
                $response['data'] = array('error' => 'Object not found');
            } else {
                // get post from client
                $json = file_get_contents('php://input');
                $post = json_decode($json, true); // decode to array

                // check input completeness
                if (empty($post)) {
                    $response['status'] = 400;
                    $response['data'] = array('error' => 'no data send');
                } else {
                    $post['id'] = $id;
                    $status = $object->update($post);
                    if ($status['status'] == 1) {
                        $response['status'] = 200;
                        $response['data'] = $status['data'];
                    } else {
                        $response['status'] = 400;
                        $response['data'] = array('error' => 'An error has occurred');
                    }
                }
            }
        }
        break;
    case 'DELETE':
        // METHOD : DELETE api/$object/:id
        if (isset($urlArray[1])) {
            $id = $urlArray[1];
            // check if id exist in database
            $data = $object->get($id);
            if (empty($data)) {
                $response['status'] = 404;
                $response['data'] = array('error' => 'Object not found');
            } elseif (!isset($urlArray[2])) {
                $status = $object->delete($id);
                if ($status == 1) {
                    $response['status'] = 200;
                    $response['data'] = array('success' => 'delete successfully');
                } else {
                    $response['status'] = 400;
                    $response['data'] = array('error' => 'An error has occurred');
                }
            } else {
                $object2 = $urlArray[2];
                $id2 = $urlArray[3];
                $newAction = 'delete' . $object2 . 'from' . $action;
                $status = $object->$newAction($id, $id2);
                if ($status['status'] == 1) {
                    $response['status'] = 201;
                    $response['data'] = $status['data'];
                } else {
                    $response['status'] = 400;
                    $response['data'] = array('error' => 'An error has occurred');
                }

            }
        }
        break;
}

// Return Response to browser
deliver_response($response);
