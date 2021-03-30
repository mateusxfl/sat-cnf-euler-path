<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<?php

    class Grafo {

        private $lista_adjacencia = array(), $tabela = array(), $pilha_verificacao = array(), $fnc = array(), $arestas = 0;

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

            for ( $i = 0 ; $i < count($this->tabela) ; $i++ ) {

                if($coordenada != $coluna_atual) {
                    echo "-".$coordenada." or -".$coluna_atual."<br>";
                }

                $coluna_atual += $this->arestas;

            }

        }

        // Nega todos os valores de uma linha, menos o passado por parâmetro.
        public function nega_linha($coordenada) {

            $linha_atual = $coordenada;

            while(($linha_atual - 1) % $this->arestas != 0) {
                $linha_atual--;
            }

            for ( $i = 0 ; $i < $this->arestas ; $i++ ) {

                if($coordenada != $linha_atual) {
                    echo "-".$coordenada." or -".$linha_atual."<br>";
                }
                
                $linha_atual++;

            }

        }

        // Nega todos os valores da linha da aresta BA, tendo em vista que já temos uma aresta AB no caminho euleriano.
        public function nega_aresta_voltando($coordenada) {

            $linha_aresta_negada = $this->tabela[strrev($this->captura_valor_aresta($coordenada))];

            for ( $i = 0 ; $i < count($linha_aresta_negada) ; $i++ ) {
                echo "-".$coordenada." or -".$linha_aresta_negada[$i]."<br>";
            }

        }

        // Gera os possíveis caminhos posteriores a coordenada passada por parâmetro.
        public function busca_arestas_adjacentes($coordenada) {

            if($coordenada % $this->arestas == 0) {
                return null;
            }else{
                $proxima_coluna = $coordenada % $this->arestas;
            }

            $vertice_anterior = substr($this->captura_valor_aresta($coordenada), 0, 1);
            $vertice_atual =  substr($this->captura_valor_aresta($coordenada), -1);

            $retorno = "-".$coordenada;

            for ( $i = 0 ; $i < count($this->lista_adjacencia[$vertice_atual]) ; $i++ ) {

                if($this->lista_adjacencia[$vertice_atual][$i] != $vertice_anterior) {

                    $proxima_aresta = $vertice_atual."".$this->lista_adjacencia[$vertice_atual][$i];

                    $retorno .= " or ".$this->tabela[$proxima_aresta][$proxima_coluna];

                    array_push($this->pilha_verificacao, $this->tabela[$proxima_aresta][$proxima_coluna]);
   
                }

            }

            echo $retorno."<br><br>";

        }

        // Gera uma FNC relativa a cada começo de caminho do grafo.
        public function geraClausulas() {

            $inicio_caminho = 1;

            for ( $i = 1 ; $i <= count($this->tabela) ; $i++ ) {

                array_push($this->pilha_verificacao, $inicio_caminho); 

                while(count($this->pilha_verificacao) > 0) {

                    $coordenada = array_shift($this->pilha_verificacao);

                    /**
                     * echo "NEGA COLUNA DE $coordenada: <br>";
                     *  $this->nega_coluna($coordenada);
                     *  echo "NEGA LINHA DE $coordenada: <br>";
                     *  $this->nega_linha($coordenada);
                     *  echo "<br> NEGA ARESTA VOLTANDO DE $coordenada: <br>";
                     *  $this->nega_aresta_voltando($coordenada);
                     *  echo "<br> GERA POSTERIOR DE $coordenada: <br>";
                     *  $this->busca_arestas_adjacentes($coordenada); 
                     *  echo "<hr style='background-color: red;'>";
                    */
                    
                    $this->nega_coluna($coordenada);
                    $this->nega_linha($coordenada);
                    $this->nega_aresta_voltando($coordenada);
                    $this->busca_arestas_adjacentes($coordenada); 

                }

                $inicio_caminho += $this->arestas;

                echo "<hr style='background-color: green;'>";

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

    // $grafo->geraClausulas();

?>

<div class="col-md-12">
    <div class="row">

        <?php $coordenada = 10; ?>

        <div class="col-md-3">
            <?php $grafo->imprime_tabela(); ?>
        </div>

        <div class="col-md-2">
            <strong>Nega coluna <?php echo $coordenada; ?>.</strong> <br>
            <?php $grafo->nega_coluna($coordenada); ?>   
        </div>

        <div class="col-md-2">
            <strong>Nega linha <?php echo $coordenada; ?>.</strong> <br>
            <?php $grafo->nega_linha($coordenada); ?>   
        </div>

        <div class="col-md-2">
            <strong>Nega aresta voltando <?php echo $coordenada; ?>.</strong> <br>
            <?php $grafo->nega_aresta_voltando($coordenada); ?>   
        </div>

        <div class="col-md-2">
            <strong>Gera posteriores <?php echo $coordenada; ?>.</strong> <br>
            <?php $grafo->busca_arestas_adjacentes($coordenada); ?>   
        </div>

    </div>
</div>

<script href="jquery-3.5.1.min.js"></script>
<script href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>