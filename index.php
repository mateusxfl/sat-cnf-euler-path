<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<?php

    class Grafo {

        private $lista_adjacencia = array(), $tabela = array(), $fnc = array(), $arestas = 0;

        // Adiciona a aresta AB e BA.
        public function adicionar_aresta($a, $b) {

            if(!isset($this->lista_adjacencia[$a])) {
                $this->lista_adjacencia[$a] = array();
            }

            if(!isset($this->lista_adjacencia[$b])) {
                $this->lista_adjacencia[$b] = array();
            }

            // && !in_array($a, $this->lista_adjacencia[$b])
            if(!in_array($b, $this->lista_adjacencia[$a])) {
                array_push($this->lista_adjacencia[$a], $b);
                array_push($this->lista_adjacencia[$b], $a);
                $this->arestas++;
            }

        }

        // Cria a tabela com todas as coordenadas.
        public function cria_tabela () {
            
            ksort($this->lista_adjacencia); $coordenada = 1;
            
            foreach($this->lista_adjacencia as $aresta => &$adjacentes) {
                
                sort($adjacentes);

                foreach($adjacentes as $vertice_adjacente) {
                    
                    $this->tabela[$aresta."".$vertice_adjacente] = array();

                    for ( $i = 0 ; $i < $this->arestas ; $i++ ) {
                        array_push($this->tabela[$aresta."".$vertice_adjacente], $coordenada++);
                    }

                }

            }

        }

        // Imprime a tabela de coordenadas.
        public function imprime_tabela() {

            $total_casas_decimais = strlen($this->arestas * $this->arestas * 2);

            echo "<table class='table table-striped table-dark table-sm'><thead><tr>";

            echo "<th> XX </th>";

            for ( $i = 1 ; $i <= $this->arestas ; $i++ ) {
                echo "<th>".sprintf("%0".$total_casas_decimais."d", $i)."</th>";
            }

            echo "</tr></thead><tbody>";

            foreach ($this->tabela as $aresta => $coordenadas) {

                echo "<tr><th>".strtoupper($aresta)."</th>";

                foreach($coordenadas as $valor_coordenada) {
                    echo "<td>".sprintf("%0".$total_casas_decimais."d", $valor_coordenada)."</td>";
                }

                echo "</tr>";

            }

            echo "</tbody></table>";

        }

        // Captura o valor de uma aresta por sua coordenada.
        public function captura_valor_aresta($coordenada) {

            $indices_arestas = array_keys($this->tabela);

            $linha = $coordenada % $this->arestas == 0 ? floor($coordenada / $this->arestas) - 1 : floor($coordenada / $this->arestas);

            return $indices_arestas[$linha];

        }

        // Nega todos os valores de uma coluna, menos o passado por parâmetro.
        public function nega_coluna($coordenada) {
           
            $coluna_atual = $coordenada % $this->arestas == 0 ? $this->arestas : ($coordenada % $this->arestas);

            $retorno = array();

            for ( $i = 0 ; $i < count($this->tabela) ; $i++ ) {

                if($coordenada != $coluna_atual) {

                    array_push($retorno, [-$coordenada,-$coluna_atual]);

                    // array_push($retorno, [-$coordenada,-$coluna_atual,0]);

                }

                $coluna_atual += $this->arestas;

            }

            return $retorno;

        }

        // Nega todos os valores de uma linha, menos o passado por parâmetro.
        public function nega_linha($coordenada) {

            $linha_atual = $coordenada;

            while(($linha_atual - 1) % $this->arestas != 0) {
                $linha_atual--;
            }

            $retorno = array();

            for ( $i = 0 ; $i < $this->arestas ; $i++ ) {

                if($coordenada != $linha_atual) {

                    array_push($retorno, [-$coordenada,-$linha_atual]);

                    // array_push($retorno, [-$coordenada,-$linha_atual,0]);

                }
                
                $linha_atual++;

            }

            return $retorno;

        }

        // Nega todos os valores da linha da aresta BA, tendo em vista que já temos uma aresta AB no caminho euleriano.
        public function nega_aresta_voltando($coordenada) {

            $linha_aresta_negada = $this->tabela[strrev($this->captura_valor_aresta($coordenada))];

            $retorno = array();

            for ( $i = 0 ; $i < count($linha_aresta_negada) ; $i++ ) {

                array_push($retorno, [-$coordenada,-$linha_aresta_negada[$i]]);

                // array_push($retorno, [-$coordenada,-$linha_aresta_negada[$i],0]);

            }

            return $retorno;

        }

        // Gera os possíveis caminhos posteriores a coordenada passada por parâmetro.
        public function busca_arestas_adjacentes($coordenada) {

            if($coordenada % $this->arestas == 0) {
                return array();
            }else{
                $proxima_coluna = $coordenada % $this->arestas;
            }

            $vertice_anterior = substr($this->captura_valor_aresta($coordenada), 0, 1);
            $vertice_atual =  substr($this->captura_valor_aresta($coordenada), -1);

            $retorno = array(); $valores = array();

            array_push($valores, -$coordenada);

            for ( $i = 0 ; $i < count($this->lista_adjacencia[$vertice_atual]) ; $i++ ) {

                if($this->lista_adjacencia[$vertice_atual][$i] != $vertice_anterior) {

                    $proxima_aresta = $vertice_atual."".$this->lista_adjacencia[$vertice_atual][$i];

                    array_push($valores, $this->tabela[$proxima_aresta][$proxima_coluna]);
   
                }

            }

            array_push($retorno, $valores);

            // array_push($valores, 0);
            // array_push($retorno, $valores);

            return $retorno;

        }

        // Imprime o vetor retornado pelas 4 funções anteriores.
        public function imprimeRetorno($vetor) {

            foreach($vetor as $valor){
                echo "[".implode(",", $valor)."] <br>";
            }
            
        }

        // Gera toda a FNC relativa as restrições do grafo.
        function gera_clausulas() {

            // $line1 = [1,2,3,4,5,6,7,8,9,10];
            // $line2 = [11,12,13,14,15,16,17,18,19,20];
            // $line3 = [21,22,23,24,25,26,27,28,29,30];
            // $line4 = [31,32,33,34,35,36,37,38,39,40];
            // $line5 = [41,42,43,44,45,46,47,48,49,50];

            // array_push($this->fnc, $line1);
            // array_push($this->fnc, $line2);
            // array_push($this->fnc, $line3);
            // array_push($this->fnc, $line4);
            // array_push($this->fnc, $line5);

            for ( $i = 1 ; $i <= count($this->tabela) * $this->arestas ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_coluna($i));
            }

            for ( $i = 1 ; $i <= count($this->tabela) * $this->arestas ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_linha($i));
            }

            for ( $i = 1 ; $i <= count($this->tabela) * $this->arestas ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_aresta_voltando($i));
            }

            for ( $i = 1 ; $i <= count($this->tabela) * $this->arestas ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->busca_arestas_adjacentes($i));
            }

            // echo "<pre> FNC: "; var_dump($this->fnc); echo "</pre>";

            // Falta restrições.

            $this->requisicao();

        }

        function requisicao() {

            $arquivo = fopen('entrada.txt','w');

            if ($arquivo == false) {
                echo 'Não foi possível criar o arquivo.';
            }else{

                $texto = "";
                
                // Imprime retorno;
                foreach($this->fnc as $valor){
                    $texto .= "[".implode(",", $valor)."]\n";
                }

                $texto = substr($texto, 0, -1);
                
                fwrite($arquivo, $texto);
                fclose($arquivo);

                $resultado = shell_exec('glucose.py');

                echo "<br> <div class='col-md-12'>".utf8_encode($resultado)."</div> <br>";

            }

        }

    }

    $grafo = new Grafo();

    $grafo->adicionar_aresta("a", "c");
    $grafo->adicionar_aresta("a", "b");
    $grafo->adicionar_aresta("b", "d");
    $grafo->adicionar_aresta("b", "c");
    $grafo->adicionar_aresta("c", "d");

    $grafo->cria_tabela();

    $coordenada = 1;

?>

<div class="col-md-12 mt-3">
    <div class="row">
        <div class="col-md-3">
            <?php $grafo->imprime_tabela(); ?>
        </div>
        <div class="col-md-9">
            <?php $grafo->gera_clausulas(); ?>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="row">

        <div class="col-md-3">
            <?php $grafo->imprime_tabela(); ?>
        </div>

        <div class="col-md-2">
            <strong>Nega coluna <?php echo $coordenada; ?>.</strong> <br>
            <?php $grafo->imprimeRetorno($grafo->nega_coluna($coordenada)); ?>   
        </div>

        <div class="col-md-2">
            <strong>Nega linha <?php echo $coordenada; ?>.</strong> <br>
            <?php $grafo->imprimeRetorno($grafo->nega_linha($coordenada)); ?>   
        </div>

        <div class="col-md-2">
            <strong>Nega aresta voltando <?php echo $coordenada; ?>.</strong> <br>
            <?php $grafo->imprimeRetorno($grafo->nega_aresta_voltando($coordenada)); ?>   
        </div>

        <div class="col-md-2">
            <strong>Gera posteriores <?php echo $coordenada; ?>.</strong> <br>
            <?php  $grafo->imprimeRetorno($grafo->busca_arestas_adjacentes($coordenada)); ?>   
        </div>

    </div>
</div>

<script href="jquery-3.5.1.min.js"></script>
<script href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>