<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Remote system logging.
  // TODO: replace with classmates logging api using Guzzle for HTTP requests.
  function send_remote_syslog($message, $component = 'HomeAutomationSystem', $program = 'ERR') {
    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    foreach(explode('\n', $message) as $line) {
      $syslog_message = '<22>' . date('M d H:i:s ') . $program . ' ' . $component . ': ' . $line;

      // Actually log to papertrail on assigned port 57238.
      socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, 'logs.papertrailapp.com', 57238);
    }
    socket_close($sock);
  }


  if (isset($_POST['message'] )) { // && isset['data'])

    $message = $_POST['message'];
    $metadata = $_POST['metadata'];

    // Json parsing.
    $obj = json_decode($metadata, true);
    $description = $obj['description'];
    $component = $obj['component'];
    // echo $component;
    try{
  //    // Log the Error.
        send_remote_syslog($message, $component);

      $response = array(
        'code' => 200,
        'message' => "Success"
        // ),
        // 'debug' => new stdClass
      );

    } catch(Exception $e) {
      $response = array(
        'code' => 503,
        'data' => new stdClass,
        'debug' => array(
          'data' => array(
            'Caught exception: ' => $e->getMessage(),
          ),
          'message' => 'An exception has occured.'
        )
      );

    }

  } else {
        // Exception for no data/message
        // TODO: Replace with appropriate json schema.
        echo "fail";
   }


  } else {

      $response = array(
        'code' => 400,
        'data' => new stdClass,
        'debug' => array(
          'data' => new stdClass,
          'message' => 'This service only accepts a POST Request.'
        )
      );
}
// In every scenario a $response object is produced - return it.
 echo json_encode($response);

?>
