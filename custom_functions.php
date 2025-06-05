<?php

function renderTwig(string $file_twig, $params = []){

    $services = $GLOBALS['services'];
    if(!isset($services["twig"]))
    {
        throw new LogicException("Not found installed symfony/twig-bundle.");
    }

    $twig = $services['twig'];
    return $twig->render($file_twig, $params);
}


function csrf_field(){
    $services = $GLOBALS['services'];
    if(!isset($services["security.csrf.token_manager"]))
    {
        throw new LogicException("Not found installed symfony/security-bundle.");
    }
    $security = $services['security.csrf.token_manager'];
    $token = $security->getToken('authenticate')->getValue();
    return '<input type="hidden" name="_csrf_token" value="'.$token.'">';
}

function route(string $name, $params = []) {
    $services = $GLOBALS['services'];
    $routes = $services["router"];
    $route = "";
    if(count($params) > 0)
    {
        $route = $routes->generate($name, $params);
    }else {
        $route = $routes->generate($name);
    }
    return $route;
};

function importMP($entrypointName, $type) {
    static $alreadyIncluded = [
        'css' => [],
        'js' => [],
    ];

    $services = $GLOBALS['services'];
    $encore = $services['orion_engine_symfony']->getConfigs()['encore'];

    if ($encore == null) {
        throw new Exception("Encore is not configured");
    }

    $file = $encore . DIRECTORY_SEPARATOR . "entrypoints.json";

    if (!file_exists($file)) {
        throw new Exception("Entry point file does not exist");
    }

    $file_content = file_get_contents($file);
    $file_json = json_decode($file_content, true);

    $entrypoints = $file_json["entrypoints"];

    if (!isset($entrypoints[$entrypointName])) {
        throw new Exception("Entrypoint '$entrypointName' not found in entrypoints.json");
    }

    $entrypoint = $entrypoints[$entrypointName];

    if (!isset($entrypoint[$type])) {
        throw new Exception("Entrypoint '$entrypointName' does not contain files of type '$type'");
    }

    $files = $entrypoint[$type];
    $html = "";

    foreach ($files as $file) {
        if (in_array($file, $alreadyIncluded[$type])) {
            continue; // já foi incluído
        }

        $alreadyIncluded[$type][] = $file;

        if ($type == "css") {
            $html .= "<link rel='stylesheet' href='$file' />\n";
        } elseif ($type == "js") {
            $html .= "<script src='$file'></script>\n";
        }
    }

    return $html;
}
