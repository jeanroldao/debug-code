<?php
class CampoMinado {

	private $mapOrig = array();
	private $mapRadar = array();
	
	private $tamX = 4;
	private $tamY = 4;
	
	private $rawMap = "";
	
	private $gameOver = false;
	
	public function __construct($args = array()) {
	
		$this->rawMap = $this->GerarCampoMinado($args);
		$this->scanMinner();
	}
	
	private function scanMinner() {
		$this->mapOrig = $this->getMap();
        $this->mapRadar = newArray(' ', array($this->tamX, $this->tamY));
        $this->openArea = newArray(false, array($this->tamX, $this->tamY));
		
        for ($i = 0; $i < $this->tamX; $i++) {
            for ($k = 0; $k < $this->tamY; $k++) {
                $this->mapRadar[$i][$k] = $this->countMinesInPoint($k, $i);
				if ($this->mapRadar[$i][$k] == '*') {
					$this->qt_minas++;
				}
            }
        }

        //printMap(mapOrig);
        //System.out.println("---------------------------");		
		$this->update();
	}
	
	public function readGameInput() {
		$coord = $this->readLine();
		
		if ($coord) {
			$lin = ord($coord[0]) - 65;
			$col = ord($coord[1]) - 65;
			
			$this->checkArea($col, $lin);
			$this->update();
		}
	}

	private function checkArea($x, $y) {
		$this->openArea[$y][$x] = true;
		//mapRadar[y][x]
		if ($this->mapRadar[$y][$x] == '*') {
			$go = "GAME OVER!";
			echo $go . PHP_EOL;
			//go = "" + (1/0);
			$this->gameOver = true;
			//throw new Exception(go);
		} 
		
		if ($this->mapRadar[$y][$x] == ' ') {
			for ($i = $y - 1; $i <= $y+1; $i++) {
				for ($k = $x - 1; $k <= $x + 1; $k++) {
					if (isset($this->openArea[$i][$k]) && !$this->openArea[$i][$k]) {
						$this->checkArea($k, $i);
					}
				}
			}
			
		}
	}
	
	private function countHidden() {
		//$this->openArea
		$iCount = 0;
		foreach ($this->openArea as $lin) {
			foreach ($lin as $area) {
				if ($area === false) {
					$iCount++;
				}
			}
		}
		return $iCount;
	}
	
	public function update() {
		echo "minas=" . $this->qt_minas . PHP_EOL . 'escondidos=' . $this->countHidden() . PHP_EOL;
        $this->printMap($this->mapRadar);
		
		if (!$this->gameOver) {
			$this->readGameInput();
		}
	}
	
	private function countMinesInPoint($x, $y) {
        $res = 0;
        
        if ($this->mapOrig[$y][$x] == '*') {
            return '*';
        }
        
        for ($i = $y - 1; $i <= $y+1; $i++) {
            for ($k = $x - 1; $k <= $x + 1; $k++) {
				if (isset($this->mapOrig[$i][$k]) && $this->mapOrig[$i][$k] == '*') {
					$res++;
				}
            }
        }
        if ($res == 0) {
		  return ' ';
		}
        
        return "$res";
	}
	
    private function printMap($map) {
		$nomecoluna = 'A';
		echo ' ';
		foreach ($map[0] as $c) {
            echo $nomecoluna++;
        }
		echo PHP_EOL;
		$nomelinha = 'A';
		for ($i = 0; $i < count($map); $i++) {
			echo $nomelinha++;
            //for (char c : linha) {
			for ($k = 0; $k < count($map[$i]); $k++) {
				if ($this->openArea[$i][$k]) {
					echo $map[$i][$k];
				} else {
					echo '#';
				}
            }
            echo PHP_EOL;
        }
    }
	
	private function getMap() {
		$arrayMap = explode("\n", $this->rawMap);
		
		$this->tamX = count($arrayMap) - 1;
		$this->tamY = strlen($arrayMap[0]);
		
		$map = newArray(' ', array($this->tamX, $this->tamY));
		
		//for (String linha : arrayMap) 
		for ($i = 0; $i < $this->tamX; $i++) {
			$map[$i] = str_split($arrayMap[$i]);
		}
        
        return $map;
	}
	
	private function GerarCampoMinado($args) {
		$ret = "";
		
		$tamX = 10;
		if (isset($args[1])) {
			$tamX = $args[1];
		}

		$tamY = 10;
		if (isset($args[2])) {
			$tamY = $args[2];
		}
		
		$num_minas = ($tamX * $tamY) / 10;
		if (isset($args[3])) {
			$num_minas = $args[3];
		}
		
		$minas = array();
		
		while (count($minas) < $num_minas) {
			$x = $this->getIntRand(0, $tamX);
			$y = $this->getIntRand(0, $tamY);
			
			$coord = "$x*$y";
			if (!in_array($coord, $minas)) {
				$minas[] = $coord;
			}
		}
		
		for ($i = 0; $i < $tamY; $i++) {
			for ($k = 0; $k < $tamX; $k++) {
				if (in_array("$k*$i", $minas)) {
					$ret .= "*";
				} else {
					$ret .= "0";
				}
			}
			$ret .= "\n";
		}
		
		return $ret;
	}
	
	private function getIntRand($ini, $limit) {
		return (int)rand($ini, $limit-1);
	}
	
	public function readLine() {
		return readLine();
	}
}

function newArray($valueIni, array $dimentions) {
	if (count($dimentions) == 0) {
		return $valueIni;
	}
	
	$size = array_shift($dimentions);
	
	return array_fill(0, $size, newArray($valueIni, $dimentions));
}

function readLine() {
	return trim(fgets(STDIN));
}

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	new CampoMinado($_SERVER["argv"]);
}

?>