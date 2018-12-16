<?php 
	echo "hello world";

	$penalidade_gap = -1;   // Gap Penalty
	$pontuacao = 1;         // Match Score
	$incompatibilidade = 0; // Mismatch Score
	$sequencia1 = "aaaaaaaaaaa";
	$sequencia2 = "aaaaaaaaaaa";
	$matriz = array();
	
	// Inicializando a matriz
	for($i = 0; $i < strlen($sequencia1)+1; $i++) {
		for($j = 0; $j < strlen($sequencia2)+1; $j++) {
			$matriz[$i][$j] = 0;
		}
	}
	
	for($i = 0; $i < strlen($sequencia1)-1; $i++) {
		$matriz[$i+1][0] = ($i+1) * $penalidade_gap;
	}
	for($j = 0; $j < strlen($sequencia2)-1; $j++) {
		$matriz[0][$j+1] = ($j+1) * $penalidade_gap;
	}
	
	for($i = 1; $i < strlen($sequencia1); $i++) {
		for($j = 1; $j < strlen($sequencia2); $j++) {
			$pon_inc = ($sequencia1[$i-1] === $sequencia2[$j-1]) ? $pontuacao : $incompatibilidade;
			$escolha1 = $matriz[$i-1][$j-1] + $pon_inc;
			$escolha2 = $matriz[$i-1][$j] + $penalidade_gap;
			$escolha3 = $matriz[$i][$j-1] + $penalidade_gap;
			$matriz[$i][$j] = max($escolha1, $escolha2, $escolha3);
			echo $matriz[$i][$j];
		}
		echo "<html><br /></html>";
	}
	
	
	
?>