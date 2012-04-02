<?php


require_once 'vH/Atom.php';
require_once 'vH/Node.php';
require_once 'vH/Connective.php';
require_once 'vH/Formula.php';
require_once 'vH/FormulaGenerator.php';
require_once 'vH/FormulaChecker.php';


function sat($formula) {
    $formula = escapeshellarg($formula);
    $output = `echo $formula | ./limboole -s`;
    return preg_match('/^% SATISFIABLE/', $output) >= 1;
}

function follows($premises, $conclusion) {
    $s = implode('&', $premises) . '&!' . $conclusion;
    return ! sat($s);
}

function superfluous($p, $premises, $conclusion) {
    $premises_without_p = array_filter($premises, function ($q) use ($p) {
	    return $q !== $p;
	});

    return follows($premises_without_p, $conclusion);
}

function has_superfluous_premises($premises, $conclusion) {
    foreach($premises as $p)
	if (superfluous($p, $premises, $conclusion))
	    return true;

    return false;
}

function new_exercise($num_premises, $new_formula) {
    $exercise['premises'] = array();
    for ($i = 0; $i < $num_premises; $i++)
	array_push($exercise['premises'], $new_formula());

    $exercise['conclusion'] = $new_formula();

    return $exercise;
}

function generate_exercises($num_valid, $num_invalid, $num_premises, $new_formula) {
    $valid = array();
    $invalid = array();
    $discarded_valid = array();
    $discarded_invalid = array();

    while (count($valid) < $num_valid || count($invalid) < $num_invalid) {
	$exercise = new_exercise($num_premises, $new_formula);

	if (follows($exercise['premises'], $exercise['conclusion']))
	    if(count($valid) < $num_valid) array_push($valid, $exercise);
	    else array_push($discarded_valid, $exercise);
	else
	    if(count($invalid) < $num_invalid) array_push($invalid, $exercise);
	    else array_push($discarded_invalid, $exercise);
    }

    return array('valid' => $valid, 'invalid' => $invalid, 'discarded_valid' =>
		 $discarded_valid, 'discarded_invalid' => $discarded_invalid);
}

function gen($params) {
    $total = intval($params['num_exercises']);

    $cutoff = rand(0, $total);
    $no_superfluous = false;

    foreach ($params['restrictions'] as $restr) {
	if ($restr === 'same_proportion')
	    $cutoff = floor($total / 2);
	elseif ($restr === 'no_superfluous_premises')
	    $no_superfluous = true;
    }

    $num_valid = $cutoff;
    $num_invalid = $total - $cutoff;

    $compl_min = intval($params['compl_min']);
    $compl_max = intval($params['compl_max']);
    $num_premises = intval($params['num_premises']);

    $conectives = array();

    foreach ($params['conectives'] as $con) {
	if ($con === 'and')
	    array_push($conectives, new Connective("&","and","K",2,0));
	elseif ($con === 'or')
	    array_push($conectives, new Connective("|","or","A",2,0));
	elseif ($con === 'imp')
	    array_push($conectives, new Connective("->","imp","C",2,0));
	elseif ($con === 'biimp')
	    array_push($conectives, new Connective("<->","biimp","B",2,0));
	elseif ($con === 'not')
	    array_push($conectives, new Connective("!","not","N",1,0));
    }


    $fgenerator = new FormulaGenerator($conectives);

    $exercises = generate_exercises($num_valid, $num_invalid, $num_premises, function () use ($fgenerator) {
	   return $fgenerator->generateFormula(rand(2, 6), 4)->toInfixNotation(); 
	});

    $exercises['num_valid'] = count($exercises['valid']);
    $exercises['num_invalid'] = count($exercises['invalid']);
    $exercises['num_total'] = $exercises['num_valid'] + $exercises['num_invalid'];

    $exercises['req_valid'] = $num_valid;
    $exercises['req_invalid'] = $num_invalid;
    $exercises['req_total'] = $num_valid + $num_invalid;

    return $exercises;
}

set_time_limit(0);


$handle = fopen('php://input','r');
$jsonInput = fgets($handle);
$decoded = json_decode($jsonInput,true);

 
$json = array();
$json['request'] = $decoded;

$json['exercises'] = gen($decoded);

die(json_encode($json));
 
?>