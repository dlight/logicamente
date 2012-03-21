<?php
class FormulaGenerator {
	public $connectives = array();
//	public $atoms = array();
	private $connectives_left;
	private $root;
//	public function __construct($connectives, $atoms) {
	public function __construct($connectives) {		
		$this->connectives = $connectives;
//		$this->atoms = $atoms;
	}
//	public function generateFormula($complexity, $symbols, $atoms) {
	public function generateFormula($complexity, $symbols) {		
		if ($complexity < 0) {
			return;
			//erro aqui ;P
		}
		
		//Gerar!
		$this->connectives_left = $complexity;
		if ($this->connectives_left == 0) $this->root = new Atom("p". rand(0,$symbols-1)); //Any atom
//		if ($this->connectives_left == 0) $this->root = array_rand($this->atoms,1); //Any atom
		else {
			$this->root = $this->rConnective();
			$this->connectives_left--;
			$this->recursiveGenerator($this->root, $symbols);
		}
		return new Formula($this->root);
	}
	private function recursiveGenerator(&$root, $symbols) {
		if ($root instanceof Atom) return;
		if ($this->connectives_left == 0) { //Nao h� mais conectivos para serem adicionados, ent�o, completar com �tomos
			if ($root instanceof Connective) {
				for($i = $root->arity - count($root->children); $i > 0; $i--) {
					$root->children[] = new Atom("p". rand(0,$symbols-1));
//					$root->children[] = array_rand($this->atoms,1);				
				}
			}
		}
		else { //Nao � atomo, e ainda temos conectivos para adicionar
			if ($this->connectives_left > $root->arity) $min = $root->arity;
			else $min = $this->connectives_left;
			$connectives_to_add = rand(1,$min); //Calculando o numero de conectivos que serao adicionados ao conectivo atual nessa chamada
			
			for($i = 0; $i < $connectives_to_add; $i++) { //Adicionando os conectivos
				$x = $this->rConnective();
				$root->children[] = $x;
				$this->connectives_left--;
			}

			for($i = count($root->children); $i < $root->arity; $i++) { //Completando o conectivo atual com atomos
				$root->children[] = new Atom("p". rand(0,$symbols-1));
//				$root->children[] = array_rand($this->atoms,1);		
			}
			
			shuffle($root->children); //Elarmhabando
			
			foreach($root->children as $child) {
				$this->recursiveGenerator($child, $symbols);
			}
		}
		
	}
	private function rConnective() {
		$i = $this->connectives[rand(0,count($this->connectives)-1)];
		return new Connective($i->infix, $i->functional, $i->polish, $i->arity, $i->order);
	}
}
?>
