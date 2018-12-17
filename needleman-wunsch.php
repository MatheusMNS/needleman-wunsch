<?php 
	// Definindo as variáveis que irão ler os arquivos
	$arquivo1 = "";
	$arquivo2 = "";
?>

<html>
<head>
	<title>Algoritmo Needleman-Wunsch</title>
	<style type="text/css">
		.trace { background-color: #c99;font-weight: bold }
		.seq { background-color: #ccc;}
		.data { border-collapse: collapse }
		.data td { border: 1px solid #666; text-align: center; }
		.align td { text-align: center; }
		.config { border-collapse: collapse }
		.config td { font-size:small;border: 1px solid #ccc; text-align: left; padding: 5px;}
		.conteudo {
			width: 400px;
			border: 5px solid white;
			padding: 5px;
			margin: margin:0 auto;
		}
		.alinhamento {
			border: 5px solid white;
			padding: 5px;
			margin: margin:0 auto;
		}
		
	</style>
</head>
<body>

<div class="conteudo">
<center>
	<h3>Implementação do Algoritmo</h3>
	<h2>Needleman-Wunsch</h2>
	<h3>em PHP</h3>
	<hr>
	<br />
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
		Sequência 1: <input type="file" name="file1" id="file1" />  <br /><br />
		Sequência 2: <input type="file" name="file2" id="file2" />  <br /><br />
		Penalidade Gap: <br /><input type="text" name="penalidadegap" id="penalidadegap" value="-1"/> <br /><br />
		Pontuação Match: <br /><input type="text" name="pontuacaomatch" id="pontuacaomatch" value="1"/> <br /><br />
		Pontuação Mismatch: <br /><input type="text" name="pontuacaomismatch" id="pontuacaomismatch" value="0"/> <br /><br />
		<input type="submit" name="submit" value="CALCULAR">
	</form>
	<hr>

<?php 
	if(isset($_FILES['file1']) && isset($_FILES['file2'])){
		$arquivo1 = $_FILES['file1']['name'];  
		$arquivo2 = $_FILES['file2']['name'];  
	}
	
	if(isset($_POST['penalidadegap']) && isset($_POST['pontuacaomatch']) && isset($_POST['pontuacaomismatch'])){
		$penalidade_gap = (int) $_POST['penalidadegap'];
		$pontuacao_match = (int) $_POST['pontuacaomatch'];
		$incompatibilidade = (int) $_POST['pontuacaomismatch'];
	}

	/***********************************************************************/

	//$penalidade_gap = -1;   // Gap Penalty
	//$pontuacao_match = 1;   // Match Score
	//$incompatibilidade = 0; // Mismatch Score
	$sequencia1 = "";
	$sequencia2 = "";
	$alinhamentoA = "";
	$alinhamentoB = "";
	$qtdgap = 0;
	$matriz = array();
	$alinhamento_otimo = array();
	
	/***********************************************************************/
	
	// Lendo primeiro arquivo txt
	if($arquivo1 != ''){
		$myfile1 = fopen($arquivo1, "r") or die("Impossível abrir arquivo!");
		fgets($myfile1);
		while(!feof($myfile1)){
			$sequencia1 = $sequencia1 . fgets($myfile1);
			$sequencia1 = str_replace("\r\n","",$sequencia1);
		}
		fclose($myfile1);
	}
	
	// Lendo segundo arquivo txt
	if($arquivo2 != ''){
		$myfile2 = fopen($arquivo2, "r") or die("Impossível abrir arquivo!");
		fgets($myfile2);
		while(!feof($myfile2)){
			$sequencia2 = $sequencia2 . fgets($myfile2);
			$sequencia2 = str_replace("\r\n","",$sequencia2);
		}
		fclose($myfile2);
	}
	
	/***********************************************************************/
	
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
	if($i > -1 && $j > -1){
		$alinhamento_otimo['score'] = $matriz[$i][$j];
	}
	else{
		$alinhamento_otimo['score'] = 0;
	}

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
			$qtdgap++;
		}
		else if($score == ($scoreUp + $penalidade_gap)){
			$alinhamento_otimo['sequencia1'][] = '-';
			$alinhamento_otimo['sequencia2'][] = $sequencia2[$j-1];
			$alinhamento_otimo['aln'][] = ' ';
			$j--;
			$qtdgap++;
		}
		else {
            die("Ponteiro Inválido: $i,$j");
        }
	}
	
	foreach(array('sequencia1', 'sequencia2', 'aln') as $k) {
		$alinhamento_otimo[$k] = array_reverse($alinhamento_otimo[$k]);
	}
	
	echo '<h3>Pontuação = '.$alinhamento_otimo['score'].'</h3>';
	echo '<h3>Gaps = '.$qtdgap.'</h3>';
	echo '<hr>';
	echo '</center></div>';
	
	echo "<div class='alinhamento'>";
	echo '<h3>Alinhamento:</h3>';
	echo '<table class="align">';
	foreach(array('sequencia2', 'aln', 'sequencia1') as $k) {
		echo '<tr>';
		foreach($alinhamento_otimo[$k] as $v) {
			echo "<td>$v</td>";
		}
		echo '</tr>';
	}
	echo "\n</table>";
	echo "</div>";
?>
<br />
</body>
</html>

