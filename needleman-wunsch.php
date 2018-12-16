<?php 
	$penalidade_gap = -1;   // Gap Penalty
	$pontuacao_match = 1;   // Match Score
	$incompatibilidade = 0; // Mismatch Score
	$sequencia1 = "GTGACCA ";
	$sequencia2 = "AACAACC ";
	$alinhamentoA = "";
	$alinhamentoB = "";
	$matriz = array();
	$alinhamento_otimo = array();
	
	// Inicializando a matriz
	for($i = 0; $i < strlen($sequencia1)+1; $i++) {
		for($j = 0; $j < strlen($sequencia2)+1; $j++) {
			$matriz[$i][$j] = 0;
		}
	}
	
	for($i = 0; $i < strlen($sequencia1); $i++) {
		$matriz[$i+1][0] = ($i+1) * $penalidade_gap;
	}
	for($j = 0; $j < strlen($sequencia2); $j++) {
		$matriz[0][$j+1] = ($j+1) * $penalidade_gap;
	}
	
	for($i = 1; $i < strlen($sequencia1); $i++) {
		for($j = 1; $j < strlen($sequencia2); $j++) {
			$pon_inc = ($sequencia1[$i-1] === $sequencia2[$j-1]) ? $pontuacao_match : $incompatibilidade;
			$escolha1 = $matriz[$i-1][$j-1] + $pon_inc;
			$escolha2 = $matriz[$i-1][$j] + $penalidade_gap;
			$escolha3 = $matriz[$i][$j-1] + $penalidade_gap;
			$matriz[$i][$j] = max($escolha1, $escolha2, $escolha3);
		}
	}
	/***********************************************************************/
	$i = strlen($sequencia1)-1;
	$j = strlen($sequencia2)-1;
	
	$alinhamento_otimo['sequencia1'] = array();
	$alinhamento_otimo['sequencia2'] = array();
	$alinhamento_otimo['aln'] = array();
	$alinhamento_otimo['score'] = $matriz[$i][$j];

	while($i > 0 && $j > 0){
		$score = $matriz[$i][$j];
		$scoreDiag = $matriz[$i-1][$j-1];
		$scoreUp = $matriz[$i][$j-1];
		$scoreLeft = $matriz[$i-1][$j];
		$pon_inc = ($sequencia1[$i-1] === $sequencia2[$j-1]) ? $pontuacao_match : $incompatibilidade;
		
		if($score == $scoreDiag + $pon_inc){
			$alinhamento_otimo['sequencia1'][] = $sequencia1[$i-1];
			$alinhamento_otimo['sequencia2'][] = $sequencia2[$j-1];
			$alinhamento_otimo['aln'][] = ($sequencia1[$i-1] === $sequencia2[$j-1]) ? '|' : ' ';
			$i--;
			$j--;
		}
		else if($score == ($scoreLeft + $penalidade_gap)){
			$alinhamento_otimo['sequencia1'][] = $sequencia1[$i-1];
			$alinhamento_otimo['sequencia2'][] = '-';
			$alinhamento_otimo['aln'][] = ' ';
			$i--;
		}
		else if($score == ($scoreUp + $penalidade_gap)){
			$alinhamento_otimo['sequencia1'][] = '-';
			$alinhamento_otimo['sequencia2'][] = $sequencia2[$j-1];
			$alinhamento_otimo['aln'][] = ' ';
			$j--;
		}
		else {
            die("Ponteiro Inválido: $i,$j");
        }
	}
	
	foreach(array('sequencia1', 'sequencia2', 'aln') as $k) {
		$alinhamento_otimo[$k] = array_reverse($alinhamento_otimo[$k]);
	}
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>';
	echo '<meta http-equiv="content-type" content="text/html;charset=utf-8" />';
	echo '<meta name="description" content="" />';
	echo '<meta name="keywords" content="" />';
	echo '<title>Needleman-Wunsch Alignment Score Table</title>';
	echo '</head>';
	echo '<body>';
	echo '<h3>Optimal Global Alignment (score = '.$alinhamento_otimo['score'].')</h3>';
	echo '<table class="align">';
	foreach(array('sequencia2', 'aln', 'sequencia1') as $k) {
		echo '<tr>';
		foreach($alinhamento_otimo[$k] as $v) {
			echo "<td>$v</td>";
		}
		echo '</tr>';
	}
	echo "\n</table>";

	echo '</body></html>';
?>