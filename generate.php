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
    $s = implode(' & ', $premises) . ' &! ' . $conclusion;
    return ! sat($s);
}

function relevant($premises, $conclusion) {
	$premise_atoms = array();
	$conclusion_atoms = array();
	$numRelevPrems=0;
	$pattern = '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/'; //Regular expression to check a valid "variable name"
	
	preg_match_all($pattern, $conclusion, $conclusion_atoms); 

	
	foreach ($premises as $key => $premise ) {
		$isRelevant=false;
		
		preg_match_all($pattern, $premise, $premise_atoms); 
		
		foreach ($premise_atoms[0] as $k => $p_atoms) { 
			
			if (in_array($p_atoms, $conclusion_atoms[0])) {
				$isRelevant=true;
			}	
		}
		
		if ( $isRelevant == true){
			$numRelevPrems++;
		}
	}
	$output = shell_exec("echo 'Num de Premissas ". sizeof($premises) ."\n' >> log");
	if ($numRelevPrems < sizeof($premises)) {
		return false;
	} else {
		return true;
	}
}


function contingency($formula) {
    return (boolean) sat($formula) && (! valid($formula));
 }


function valid($formula) {
    $formula = escapeshellarg($formula);
    $output = `echo $formula | ./limboole`;
    return preg_match('/^% VALID/', $output) >= 1; 
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

	while (!relevant($exercise['premises'], $exercise['conclusion'])) {
		$exercise = new_exercise($num_premises, $new_formula);
	}

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

      
    $num_premises = $params['num_premises'];


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
	

	$atoms = array();
		
	foreach ($params['atoms'] as $atm) {
		array_push($atoms, new Atom($atm));
	}
	
    $fgenerator = new FormulaGenerator($conectives, $atoms);
	
	$compl_min = intval($params['compl_min']);
	$compl_min = intval($params['compl_max']);

    

    $exercises = generate_exercises($num_valid, $num_invalid, $num_premises, function () use ($fgenerator, $compl_min, $compl_max) {
	   $complex = rand($compl_min, $compl_max);
	   $formula = $fgenerator->generateFormula($complex)->toInfixNotation(); 
	   
	   
	   while (contingency($formula) !=  1):
			$formula = $fgenerator->generateFormula($complex)->toInfixNotation(); 
	   endwhile;
	      
	   return $formula; 
	});
	
	
    $exercises['num_valid'] = count($exercises['valid']);
    $exercises['num_invalid'] = count($exercises['invalid']);
    $exercises['num_total'] = $exercises['num_valid'] + $exercises['num_invalid'];

    $exercises['req_valid'] = $num_valid;
    $exercises['req_invalid'] = $num_invalid;
    $exercises['req_total'] = $num_valid + $num_invalid;

    return $exercises;
}

/*
follows(array('a', 'b', 'c'), 'a');

$x = superfluous('c', array('a', 'b', 'c'), '(c|b)');


echo '<br/><br/>';

print 'Contingencia: '. ((bool) (contingency('(c -> c)')));


echo '<br/><br/>';

echo  'Validade: '. ((bool)  ( valid('(c -> c)')));

 
echo '<br/><br/>';



echo 'Contingencia: '. ((bool) (contingency('(c -> c)'))));



echo '<br/><br/>';

$premises = array();

$f1 =  "A /\ A";
$f2 =  "D -> C";
$f3 =  "E -> B";
$f4 =  "E -> A";
$c0 =  "D -> (B /\ A)";

array_push($premises, $f1);
array_push($premises, $f2);
array_push($premises, $f3);
array_push($premises, $f4);

echo '<br/><br/>';

echo relevant($premises, $c0);


echo '<br/><br/>';
*/


$handle = fopen('php://input','r');
$jsonInput = fgets($handle);
$decoded = json_decode($jsonInput,true);

 
$json = array();
$json['request'] = $decoded;

$json['exercises'] = gen($decoded);

die(json_encode($json));
 
?>
