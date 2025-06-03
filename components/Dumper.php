<?php
function dmp($var, $print = false)
{
    $str =  '<pre>'.print_r($var, true).'</pre>';
    if ($print){
        echo $str;
    } else {
        die($str);
    }
}

function setTimemark()
{
	$session = new \app\my_toolbox\SingleSession('debug');
	$time = $session->tGet('time');
	$time[] = microtime(true);
	$session->tSet('time', $time);
}

function showTimeDebug($die = true)
{
	$session = new \app\my_toolbox\SingleSession('debug');
	$time = $session->tGet('time');
	$base = array_shift($time);
	if(is_array($time))
		foreach($time as &$t)
			$t = $t - $base;
	$session->clear();
	dump($time, !$die);
}