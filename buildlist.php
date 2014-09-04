<?php
function returnBuildList() {
	$buildList = array(
		'StaticObservable.php',
		'Configure/Configure.php',
		'Configure/exception.php',
		'Debug/Debug.php',
		'Debug/baseRenderer.php',
		'Debug/ErrorRenderer.php',
		'Debug/ExceptionRenderer.php',
		'DB.php',
		'DBSimple.php',
		'UParser.php',
		'Model.php',
		'Exception.php',
        'View/View.php',
        'View/Exception.php',
        'Controller/Controller.php',
		'Response/Response.php',
		'Response/HttpResponse.php',
		'Response/JsonResponse.php',
		'Dispatcher/Dispatcher.php',
		'Dispatcher/Route.php',
		'Dispatcher/HttpRoute.php',
		'Dispatcher/RouteException.php',
		'Request/Request.php',
		'Request/HttpRequest.php',
		'Request/ValidationException.php',
		'Request/Validator/Validator.php',
		'Request/Validator/Email.php',
		'Request/Validator/Integer.php',
		'Request/Validator/Url.php',
	);
	return $buildList;
}
function loadBuildList( $baseDir ) {
	$buildList = returnBuildList();
	foreach ( $buildList as $key=>$row ){
		require_once $baseDir.$row;
	}
} 