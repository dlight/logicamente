<html >
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
<title>Testando php (:</title>
<style type="text/css">
body{font-family:"Consolas",Times,serif;font-size:13}
</style>
</head>
<body>
<?php

require_once 'Atom.php';
require_once 'Node.php';
require_once 'Connective.php';
require_once 'Formula.php';
require_once 'FormulaGenerator.php';
require_once 'FormulaChecker.php';


echo "<h2> Fórmulas em Unicode</h2>";

$p1 = new Atom("p1");
$p2 = new Atom("p2");
$p3 = new Atom("p3");
$and = new Connective("∧","and","K",2,0);
$or = new Connective("∨","or","A",2,0);
$imp = new Connective("→","imp","C",2,0);
$not = new Connective("¬","not","N",1,0);

$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not));
//$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not), array($p1,$p2,$p3));
$numero_formulas = 20;

$complexity_min = 4;
$complexity_max = 4;

$min_symbols = 3;
$max_symbols = 6;
	
	
for ($i = 0; $i < $numero_formulas; $i++) {
	echo "<br/>";
	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max),rand($min_symbols,$max_symbols));
//	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max),rand($min_symbols,$max_symbols),array($p1,$p2,$p3));
	echo $formula->toInfixNotation();
}

echo "<br/><br/><br/>";

echo "<h2> Fórmulas na sintaxe do ProofWeb</h2>";

	

$p1 = new Atom("A");
$p2 = new Atom("B");
$p3 = new Atom("C");
$and = new Connective(" /\ ","and","K",2,0);
$or = new Connective(" \/ ","or","A",2,0);
$imp = new Connective("->","imp","C",2,0);
$bii = new Connective("<->","bii","B",2,0);
$not = new Connective("~","not","N",1,0);

$fgenerator = new FormulaGenerator(array($and,$or,$imp,$bii,$not));
//$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not), array($p1,$p2,$p3));
$numero_formulas = 20;

$complexity_min = 4;
$complexity_max = 4;

$min_symbols = 3;
$max_symbols = 6;

	
for ($i = 0; $i < $numero_formulas; $i++) {
	echo "<br/>";
	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max),rand($min_symbols,$max_symbols));
//	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max),rand($min_symbols,$max_symbols),array($p1,$p2,$p3));
	echo $formula->toInfixNotation();
}


echo "<br/><br/><br/>";

echo "<h2> Fórmulas na sintaxe do Limboole</h2>";



$p3 = new Atom("p");
$p4 = new Atom("q");
$p5 = new Atom("r");
$and = new Connective("&","and","K",2,0);
$or = new Connective("|","or","A",2,0);
$imp = new Connective("->","imp","C",2,0);
$imp = new Connective("<->","bii","B",2,0);
$not = new Connective("!","not","N",1,0);


$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not));
//$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not), array($p1,$p2,$p3));
$numero_formulas = 20;

$complexity_min = 4;
$complexity_max = 4;

$min_symbols = 3;
$max_symbols = 6;
	
for ($i = 0; $i < $numero_formulas; $i++) {
	echo "<br/>";
	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max),rand($min_symbols,$max_symbols));
//	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max),rand($min_symbols,$max_symbols),array($p1,$p2,$p3));
	echo $formula->toInfixNotation();
}



echo "<br/><br/><br/>";

echo "<h2> Fórmulas em Latex</h2>";



$p1 = new Atom("\alpha");
$p2 = new Atom("\beta");
$p3 = new Atom("\gamma");

$and = new Connective("\land","and","K",2,0);
$or = new Connective("\\vee ","or","A",2,0);
$imp = new Connective("\to","imp","C",2,0);
$imp = new Connective("\leftrightarrow","bii","B",2,0);
$not = new Connective("\\neg","not","N",1,0);


//$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not), array($p1,$p2,$p3));
$fgenerator = new FormulaGenerator(array($and,$or,$imp,$not));
$numero_formulas = 20;

$complexity_min = 4;
$complexity_max = 4;

$min_symbols = 3;
$max_symbols = 6;
	
for ($i = 0; $i < $numero_formulas; $i++) {
	echo "<br/>";
	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max),rand($min_symbols,$max_symbols));
//	$formula = $fgenerator->generateFormula(rand($complexity_min,$complexity_max),rand($min_symbols,$max_symbols),array($p1,$p2,$p3));
	echo $formula->toInfixNotation();
}

?>

</body>
</html>
