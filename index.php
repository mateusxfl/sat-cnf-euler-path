<?php

    class Grafo {

        private $lista_adjacencia = array(), $tabela = array(), $ajax = array(), $fnc = array(), $arestas = array();

        // Adiciona a aresta AB e BA na lista de adjacência, assim como preenche o vetor ajax com os dados do JSON a ser gerado.
        public function adicionar_aresta($a, $b) {

            if(!isset($this->lista_adjacencia[$a])) {
                $this->lista_adjacencia[$a] = array();
            }

            if(!isset($this->lista_adjacencia[$b])) {
                $this->lista_adjacencia[$b] = array();
            }

            // && !in_array($b, $this->lista_adjacencia[$a])
            if(!in_array($a, $this->lista_adjacencia[$b])) {
                array_push($this->lista_adjacencia[$a], $b);
                array_push($this->lista_adjacencia[$b], $a);
                array_push($this->arestas, $a."".$b);

                if(!isset($this->ajax[$a])){
                    $this->ajax[$a] = array();
                }

                if(!isset($this->ajax[$b])){
                    $this->ajax[$b] = array();
                }

                array_push($this->ajax[$a], $b);
                
            }

        }

        // Cria a tabela com todas as coordenadas.
        public function cria_tabela () {
            
            ksort($this->lista_adjacencia); $coordenada = 1;
            
            foreach($this->lista_adjacencia as $aresta => &$adjacentes) {
                
                sort($adjacentes);

                foreach($adjacentes as $vertice_adjacente) {
                    
                    $this->tabela[$aresta."".$vertice_adjacente] = array();

                    for ( $i = 0 ; $i < count($this->arestas) ; $i++ ) {
                        array_push($this->tabela[$aresta."".$vertice_adjacente], $coordenada++);
                    }

                }

            }

        }

        // Gera um arquivo AJAX, para a renderização do grafo.
        public function cria_ajax() {

            $arquivo = fopen('data.json','w');

            if ($arquivo == false) {
                echo 'Não foi possível criar o arquivo com as cláusulas.';
            }else{

                ksort($this->ajax);

                $dados_ajax = "[\n";

                foreach ($this->ajax as $key => &$pai) {

                    sort($pai);
                    
                    $dados_ajax .= "\t{\n";

                    $dados_ajax .= "\t\t\"id\": \"".strtoupper($key)."\",\n";

                    $dados_ajax .= "\t\t\"parentIds\": [";

                    if(count($pai) > 0) {
                        $dados_ajax .= "\"".strtoupper(implode($pai, '","'))."\"";
                    }    
                    
                    $dados_ajax .= "]\n";

                    $dados_ajax .= "\t},\n";

                }

                $dados_ajax = substr($dados_ajax, 0, -2);

                $dados_ajax .= "\n]";

                // echo $ajax;
                
                fwrite($arquivo, $dados_ajax);
                fclose($arquivo);

            }

        }

        // Imprime a tabela de coordenadas.
        public function imprime_tabela($solucao = null) {

            $total_casas_decimais = strlen(count($this->arestas) * count($this->arestas) * 2);

            echo "<table class='table table-striped table-dark table-sm'><thead><tr>";

            echo "<th> XX </th>";

            for ( $i = 1 ; $i <= count($this->arestas) ; $i++ ) {
                echo "<th>".sprintf("%0".$total_casas_decimais."d", $i)."</th>";
            }

            echo "</tr></thead><tbody>";

            foreach ($this->tabela as $aresta => $coordenadas) {

                echo "<tr><th>".strtoupper($aresta)."</th>";

                foreach($coordenadas as $valor_coordenada) {
                    if(isset($solucao) && in_array($valor_coordenada, $solucao)) {
                        echo "<th style='color:red'>".sprintf("%0".$total_casas_decimais."d", $valor_coordenada)."</th>";
                    }else{
                        echo "<td>".sprintf("%0".$total_casas_decimais."d", $valor_coordenada)."</td>";
                    }
                }

                echo "</tr>";

            }

            echo "</tbody></table>";

        }

        // Captura o valor de uma aresta por sua coordenada.
        public function captura_valor_aresta($coordenada) {

            $indices_arestas = array_keys($this->tabela);

            $linha = $coordenada % count($this->arestas) == 0 ? floor($coordenada / count($this->arestas)) - 1 : floor($coordenada / count($this->arestas));

            return $indices_arestas[$linha];

        }

        // Nega todos os valores de uma coluna, menos o passado por parâmetro.
        public function nega_coluna($coordenada) {
           
            $linha_atual = $coordenada % count($this->arestas) == 0 ? count($this->arestas) : ($coordenada % count($this->arestas));

            $retorno = array();

            for ( $i = 0 ; $i < count($this->tabela) ; $i++ ) {

                if($coordenada != $linha_atual) {
                    array_push($retorno, [-$coordenada,-$linha_atual]);
                }

                $linha_atual += count($this->arestas);

            }

            return $retorno;

        }

        // Nega todos os valores de uma linha, menos o passado por parâmetro.
        public function nega_linha($coordenada) {

            $coluna_atual = $coordenada;

            while(($coluna_atual - 1) % count($this->arestas) != 0) {
                $coluna_atual--;
            }

            $retorno = array();

            for ( $i = 0 ; $i < count($this->arestas) ; $i++ ) {

                if($coordenada != $coluna_atual) {
                    array_push($retorno, [-$coordenada,-$coluna_atual]);
                }
                
                $coluna_atual++;

            }

            return $retorno;

        }

        // Nega todos os valores da linha da aresta BA, tendo em vista que já temos uma aresta AB.
        public function nega_aresta_voltando($coordenada) {

            $linha_aresta_voltando = $this->tabela[strrev($this->captura_valor_aresta($coordenada))];

            $retorno = array();

            for ( $i = 0 ; $i < count($linha_aresta_voltando) ; $i++ ) {
                array_push($retorno, [-$coordenada,-$linha_aresta_voltando[$i]]);
            }

            return $retorno;

        }

        // Gera os possíveis caminhos posteriores a coordenada passada por parâmetro.
        public function busca_arestas_adjacentes($coordenada) {

            if($coordenada % count($this->arestas) == 0) {
                return array();
            }else{
                $proxima_coluna = $coordenada % count($this->arestas);
            }

            $vertice_anterior = substr($this->captura_valor_aresta($coordenada), 0, 1);
            $vertice_atual =  substr($this->captura_valor_aresta($coordenada), -1);

            $retorno = array(array());

            array_push($retorno[0], -$coordenada);

            for ( $i = 0 ; $i < count($this->lista_adjacencia[$vertice_atual]) ; $i++ ) {

                if($this->lista_adjacencia[$vertice_atual][$i] != $vertice_anterior) {

                    $proxima_aresta = $vertice_atual."".$this->lista_adjacencia[$vertice_atual][$i];

                    array_push($retorno[0], $this->tabela[$proxima_aresta][$proxima_coluna]);
   
                }

            }

            return $retorno;

        }

        // A cláusula positiva contém uma aresta indo e voltando, tendo em vista que so um valor da clausula acontece.
        public function clausula_positiva($aresta) {

            $retorno = array();

            $linha_aresta_indo = $this->tabela[$aresta];
            $linha_aresta_voltando = $this->tabela[strrev($aresta)];

            array_push($retorno, array_merge($linha_aresta_indo,  $linha_aresta_voltando));

            return $retorno;
        
        }

        // Imprime qualquer um dos vetores retornado pelos 5 métodos anteriores.
        public function imprimeRetorno($vetor) {

            foreach($vetor as $valor){
                echo "[".implode(",", $valor)."] <br>";
            }
            
        }

        // Gera toda a FNC relativa as restrições do grafo.
        function gera_clausulas() {

            sort($this->arestas); $this->clausula_positiva = array();

            // 450 OK
            for ( $i = 1 ; $i <= count($this->tabela) * count($this->arestas) ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_coluna($i));
            }

            // 200 OK
            for ( $i = 1 ; $i <= count($this->tabela) * count($this->arestas) ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_linha($i));
            }

            // 250 OK
            for ( $i = 1 ; $i <= count($this->tabela) * count($this->arestas) ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_aresta_voltando($i));
            }

            // 40 OK
            for ( $i = 1 ; $i <= count($this->tabela) * count($this->arestas) ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->busca_arestas_adjacentes($i));
            }

            // 5 OK
            foreach($this->arestas as $aresta) {
                $this->clausula_positiva = array_merge($this->clausula_positiva, $this->clausula_positiva($aresta));
                $this->fnc = array_merge($this->fnc, $this->clausula_positiva($aresta));
            }

            // echo "<pre> FNC: "; var_dump($this->fnc); echo "</pre>";

            $this->requisicao();

        }

        // Gera um arquivo com todas as cláusulas e faz a requisição ao pySAT.
        function requisicao() {

            $arquivo = fopen('entrada.txt','w');

            if ($arquivo == false) {
                echo 'Não foi possível criar o arquivo com as cláusulas.';
            }else{

                $texto = "";
                
                foreach($this->fnc as $valor){
                    $texto .= "[".implode(",", $valor)."]\n";
                }

                $texto = substr($texto, 0, -1);
                
                fwrite($arquivo, $texto);
                fclose($arquivo);

                $resultado = shell_exec('glucose.py');

                $resultado = explode("\n", $resultado);

                // echo "<pre>"; var_dump($resultado); echo "</pre>";

                array_pop($resultado);

                $this->status = array_shift($resultado);
                $this->clausulas = array_shift($resultado);
                $this->variaveis = array_shift($resultado);

                $this->solucao = $resultado;

            }

        }

    }

    $grafo = new Grafo();

    $grafo->adicionar_aresta("a", "c");
    $grafo->adicionar_aresta("a", "b");
    $grafo->adicionar_aresta("b", "d");
    $grafo->adicionar_aresta("b", "c");
    $grafo->adicionar_aresta("c", "d");

    // $grafo->adicionar_aresta("a", "b");
    // $grafo->adicionar_aresta("b", "c");
    // $grafo->adicionar_aresta("c", "d");
    // $grafo->adicionar_aresta("d", "e");
    // $grafo->adicionar_aresta("e", "f");
    // $grafo->adicionar_aresta("f", "g");
    // $grafo->adicionar_aresta("g", "a");

    $grafo->cria_tabela();

    $grafo->cria_ajax();

    $grafo->gera_clausulas();

    $coordenada = 1;

?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>

    body {
        zoom: 0.67;
    }

</style>

<div class="conteudo">

    <div class="col-md-12 mt-3">
        <div class="row">

            <div class="col-md-4">
                <?php $grafo->imprime_tabela(); ?>
            </div>
            
            <div class="col-md-4">
                <?php $grafo->imprime_tabela($grafo->solucao); ?>
            </div>

            <div class="col-md-4">
                <?php 

                    $status = $grafo->status == "True" ? "Válido" : "Inválido";
                
                    echo "
                        <table class='table table-striped table-dark table-sm'>
                            <tr>
                                <th>STATUS</th>
                                <td>$status</td>
                            </tr>
                            <tr>
                                <th>CLÁUSULAS</th>
                                <td>$grafo->clausulas</td>
                            </tr>
                            <tr>
                                <th>VARIÁVEIS</th>
                                <td>$grafo->variaveis</td>
                            </tr>
                        </table>
                    ";

                    // echo "<h3>";
                    //     foreach($grafo->solucao as $coordenada_atual){
                    //         echo strtoupper($grafo->captura_valor_aresta($coordenada_atual))." ";
                    //     }
                    // echo "</h3>";
                
                ?>
            </div>

            

        </div>
    </div>

    <hr>

    <div class="col-md-12">
        <div class="row">

            <div class="col-md-2">
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

            <div class="col-md-2">
                <strong>Cláusula positiva X.</strong> <br>
                <?php  $grafo->imprimeRetorno($grafo->clausula_positiva); ?>   
            </div>

        </div>
    </div>

</div>

<script href="jquery-3.5.1.min.js"></script>
<script href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>