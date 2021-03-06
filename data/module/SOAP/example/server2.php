<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Shane Caraveo <Shane@Caraveo.com>                           |
// +----------------------------------------------------------------------+
//
// $Id: server2.php,v 1.3 2005/03/10 23:16:40 yunosh Exp $
//

// first, include the SOAP/Server class
require_once 'SOAP/Server.php';

$server = new SOAP_Server;
/* tell server to translate to classes we provide if possible */
$server->_auto_translation = true;

/* This is a simple example of implementing a custom
   call handler.  If you do this, the soap server will ignore
   objects or functions added to it, and will call your handler
   for **ALL** soap calls the server receives, wether the call
   is defined in your WSDL or not.  The handler receives two
   arguments, the method name being called, and the arguments
   sent for that call.
*/
function myCallHandler($methodname, $args)
{
    global $soapclass;
    return @call_user_func_array(array($soapclass, $methodname),$args);
}
$server->setCallHandler('myCallHandler',false);

require_once 'example_server.php';

$soapclass = new SOAP_Example_Server();
$server->addObjectMap($soapclass,'urn:SOAP_Example_Server');

if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD']=='POST') {
    $server->service($HTTP_RAW_POST_DATA);
} else {
    require_once 'SOAP/Disco.php';
    $disco = new SOAP_DISCO_Server($server,'ServerExample');
    header("Content-type: text/xml");
    if (isset($_SERVER['QUERY_STRING']) &&
       strcasecmp($_SERVER['QUERY_STRING'],'wsdl')==0) {
        echo $disco->getWSDL();
    } else {
        echo $disco->getDISCO();
    }
    exit;
}
?>
