<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

require_once __DIR__ . '/providers.php';
require_once __DIR__ . '/routes.php';

$app->before(function(Request $request) {
    // https://gist.github.com/mnot/210535
    $TOKEN = '(?:[^\(\)<>@,;:\\\\"/\[\]\?={} \t]+?)';
    $QUOTED_STRING = '(?:"(?:\\\\"|[^"])*")';
    $PARAMETER = sprintf('(?:%s(?:=(?:%s|%s))?)', $TOKEN, $TOKEN, $QUOTED_STRING);
    $LINK = sprintf('<[^>]*>\s*(?:;\s*%s?\s*)*', $PARAMETER);
    $COMMA = '(?:\s*(?:,\s*)+)';
    $LINK_SPLIT = sprintf('%s(?=%s|\s*$)', $LINK, $COMMA);
    $PARAMETER_SPLIT = sprintf('%s(?=%s|\s*$)', $PARAMETER, '\s*;\s*');

    $links = $request->headers->get('link');
    preg_match_all(sprintf('#%s#', $LINK_SPLIT), $links, $link_matches, PREG_SET_ORDER);

    $links = array();
    foreach ($link_matches as $link) {
        $link = $link[0];
        list($url, $params) = explode('>', $link, 2);
        $url = substr($url, 1);

        $params_arr = array();
        preg_match_all(sprintf('#%s#', $PARAMETER_SPLIT), $params, $param_matches, PREG_SET_ORDER);
        foreach ($param_matches as $param) {
            $param = $param[0];
            list($key, $val) = explode('=', $param, 2);

            if ($val{0} == '"') {
                $val = substr($val, 1, -1);
                $val = preg_replace('#\\\\(.)#', '\1', $val);
            }

            $params_arr[strtolower($key)] = $val;
        }

        $links[] = array(
            'url' => $url,
            'parameters' => $params_arr
        );
    }

    $request->attributes->set('_links', $links);
});
