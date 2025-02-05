<?php
ini_set('memory_limit', '-1');

header("User-Agent: gerais.script-monitoramento");
header("Script-Version: 1.0.0");

$request_method = $_SERVER['REQUEST_METHOD'];

$conn = new PDO('sqlite:./monitoramento.db');

if($request_method == 'GET') {
    $identifier_path = './identifier.txt';

    try {
        $identifier = file_get_contents($identifier_path);
        header("Server-Identifier: $identifier");
    
    } catch(\Throwable $e) {
        header("Server-Identifier: Pendent");
    }

    verify_database($conn);
    handle_previous_request_header($conn);
    verify_database($conn);

    $metrics = get_metrics_info($conn);

    set_metrics_as_sended($conn);

    $server_info_stringify = json_encode($metrics);

    echo $server_info_stringify;

    http_response_code(200);
    exit();
}


// TODO
if($request_method == 'POST') {
    echo 'atualizou <br>';

    $res = system('git pull', $code );

    if(empty($res)) {
        var_dump($res);
    }

    //TODO - Verificar se já n esta atualizado

    die;

    $metrics = get_metrics_info($conn);
    $system = json_decode($metrics[0]->system);

    $scrip_directory = __DIR__.'/shell_scripts';

    if($system == 'Linux' && file_exists($scrip_directory.'/linux.exe'))
        $output = exec($scrip_directory.'/linux.sh');

    if($system == 'Windows' && file_exists($scrip_directory.'/windows.ps1'))
        $output = exec($scrip_directory.'/windows.ps1');

    print_r($system, $output);
}


/**
 * 
 */
function verify_database(PDO $conn) {
    $script = 'python3 python/script.py';

    try {
        $number_of_rows = $conn->query("SELECT COUNT(*) AS count FROM SystemInfo")->fetchColumn();
        
        if($number_of_rows < 1)
            exec($script);

    } catch (\Throwable $th) {
        exec($script);
    }
}

/**
 * 
 */
function handle_previous_request_header(PDO $conn) {
    if(isset($_GET['previous_request']) && $_GET['previous_request'] == 'successful') {
        try {
            $conn->exec('DELETE FROM SystemInfo WHERE sended = 1');
    
        } catch (\Throwable $th) {
            verify_database($conn);
        }
    }
}

/**
 * 
 */
function get_metrics_info(PDO $conn) {
    try {
        $res = $conn->query("SELECT * FROM SystemInfo ORDER BY ID DESC");

        $format_res = [];

        foreach ($res as $row) {
            $object = new stdClass();

            $object->sended = $row['sended'];
            $object->info = $row['info'];

            array_push($format_res, $object);
        }

        return $format_res;
    } catch (\Throwable $th) {
        verify_database($conn);
    }
}

/**
 * 
 */
function set_metrics_as_sended(PDO $conn) {
    try {
        $conn->exec('UPDATE SystemInfo SET sended = 1');
    
    } catch (\Throwable $th) {
        verify_database($conn);
    }
}