<?php

    class Grafo {

        // Cria variáveis que serão utilizadas posteriormente.
        private $lista_adjacencia = array(), $tabela = array(), $ajax = array(), $fnc = array(), $arestas = array();

        // Adiciona a aresta AB e BA na lista de adjacência, assim como preenche o vetor ajax com os dados do JSON a ser gerado.
        public function adicionar_aresta($a, $b) {

            // Verifica se A já está na lista de adjacência, caso não: é adicionado.
            if(!isset($this->lista_adjacencia[$a])) {
                $this->lista_adjacencia[$a] = array();
            }

            // Verifica se B já está na lista de adjacência, caso não: é adicionado.
            if(!isset($this->lista_adjacencia[$b])) {
                $this->lista_adjacencia[$b] = array();
            }

            // Verifica se já há uma aresta igual na lista de adjacência.
            if(!in_array($a, $this->lista_adjacencia[$b]) && !in_array($b, $this->lista_adjacencia[$a])) {

                // Cadastra A em B e B em A.
                array_push($this->lista_adjacencia[$a], $b);
                array_push($this->lista_adjacencia[$b], $a);

                // Adiciona AB no vetor de arestas.
                array_push($this->arestas, $a."".$b);

                // Verifica se A já está no AJAX gerado, caso não: é adicionado.
                if(!isset($this->ajax[$a])){
                    $this->ajax[$a] = array();
                }

                // Verifica se B já está no AJAX gerado, caso não: é adicionado.
                if(!isset($this->ajax[$b])){
                    $this->ajax[$b] = array();
                }

                // Retona todo um vetor com as chaves do array AJAX.
                $chaves_array = array_keys($this->ajax);

                // Adiciona B como filho de A, ou o inverso, dependendo do primeiro valor encontrado entre A e B no $this->ajax.
                if(array_search($a, $chaves_array) < array_search($b, $chaves_array)) {
                    array_push($this->ajax[$a], $b);
                }else{
                    array_push($this->ajax[$b], $a);
                }
                
            }

        }

        // Cria a tabela com todas as coordenadas.
        public function cria_tabela () {
            
            // Ordena os valores da lista de adjacência, preservando suas chaves (a,b,c...)
            ksort($this->lista_adjacencia);
            
            // Coordenada da tabela, começando em 1.
            $coordenada = 1;
            
            // Percorre a lista de adjacência, capturando a aresta (índice do vetor) e seu conteudo.
            foreach($this->lista_adjacencia as $aresta => &$adjacentes) {
                
                // Ordena seu conteúdo ($adjacentes), restaurando as chaves do array.
                sort($adjacentes);

                // Percorre todos os adjacentes de $aresta capturando o valor ($vertice_adjacente).
                foreach($adjacentes as $vertice_adjacente) {
                    
                    // Cria uma linha na tabela ($aresta."".$vertice_adjacente), exemplo: AB.
                    $this->tabela[$aresta."".$vertice_adjacente] = array();

                    // Adiciona o valor da coordenada na linha.
                    for ( $i = 0 ; $i < count($this->arestas) ; $i++ ) {
                        array_push($this->tabela[$aresta."".$vertice_adjacente], $coordenada++);
                    }

                }

            }

        }

        // Gera um arquivo AJAX, para a renderização do grafo.
        public function cria_ajax() {

            // Cria / Sobrescreve o arquivo data.json.
            $arquivo = fopen('data.json','w');

            // Se o arquivo não abre, é retornado a mensagem.
            if ($arquivo == false) {
                echo 'Não foi possível criar o JSON com os relacionamentos.';
            }else{

                // Ordena os valores do vetor $ajax, preservando suas chaves (a,b,c...)
                ksort($this->ajax);

                $dados_ajax = "[\n";

                // Percorre todo o vetor AJAX, capturando sua respectiva chave ($pai) e seu conteúdo ($filhos)
                foreach ($this->ajax as $pai => &$filhos) {

                    // Ordena seu conteúdo ($filhos), restaurando as chaves do array.
                    sort($filhos);
                    
                    $dados_ajax .= "\t{\n";

                    $dados_ajax .= "\t\t\"id\": \"".strtoupper($pai)."\",\n";

                    $dados_ajax .= "\t\t\"parentIds\": [";

                    // Caso o vetor de filhos esteja vazio, nao é retornado nada.
                    if(count($filhos) > 0) {

                        // Imprimo todos os filhos no formato ["a","b",...]
                        $dados_ajax .= "\"".strtoupper(implode($filhos, '","'))."\"";

                    }    
                    
                    $dados_ajax .= "]\n";

                    $dados_ajax .= "\t},\n";

                }

                // Retiro o último ,\n.
                $dados_ajax = substr($dados_ajax, 0, -2);

                $dados_ajax .= "\n]";
                
                // Escrevo no arquivo.
                fwrite($arquivo, $dados_ajax);

                // Fecho o arquivo.
                fclose($arquivo);

            }

        }

        // Imprime a tabela de coordenadas.
        public function imprime_tabela($solucao = null) {

            // Pega o ultimo valor da lista, e captura o total de casas decimais.
            $total_casas_decimais = strlen(count($this->tabela) * count($this->arestas));

            echo "<table class='table table-striped table-dark table-sm'><thead><tr>";

            echo "<th> XX </th>";

            // Imprime o cabeçalho da tabela, com o total de casas decimais correto.
            for ( $i = 1 ; $i <= count($this->arestas) ; $i++ ) {
                echo "<th>".sprintf("%0".$total_casas_decimais."d", $i)."</th>";
            }

            echo "</tr></thead><tbody>";

            // Percorre toda a tabela, capturando a sua chave ($aresta) e seu conteúdo ($coordenada).
            foreach ($this->tabela as $aresta => $coordenadas) {

                // Imprime a aresta em negrito.
                echo "<tr><th>".strtoupper($aresta)."</th>";

                // Percorre todo o vetor de coordenadas, capturando seu valor.
                foreach($coordenadas as $valor_coordenada) {

                    // Se a coordenada está na solução, é imprimido em vermelho e negrito, caso não: é imprimido normal.
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

            // Pega todas as chaves de $this->tabela e converte em um vetor com suas chaves (valor da aresta).
            $indices_arestas = array_keys($this->tabela);

            // Captura a linha da tabela de acordo com a coordenada.
            $linha = $coordenada % count($this->arestas) == 0 ? floor($coordenada / count($this->arestas)) - 1 : floor($coordenada / count($this->arestas));

            // Pega o valor da aresta que contém a coordenada.
            return $indices_arestas[$linha];

        }

        // Nega todos os valores de uma coluna, menos o passado por parâmetro.
        public function nega_coluna($coordenada) {
           
            // Pega a coordenada que está na mesma coluna que a passada por parâmetro, porém na primeira linha.
            $linha_atual = $coordenada % count($this->arestas) == 0 ? count($this->arestas) : ($coordenada % count($this->arestas));

            $retorno = array();

            // Percorre o tamanho da tabela.
            for ( $i = 0 ; $i < count($this->tabela) ; $i++ ) {

                // Se a coordenada passada por parâmetro é diferente coordenada da linha atual, o valor é adicionado o valor no retorno.
                if($coordenada != $linha_atual) {
                    array_push($retorno, [-$coordenada,-$linha_atual]);
                }

                // Pulo para o proximo valor (na próxima linha e na mesma coluna).
                $linha_atual += count($this->arestas);

            }

            return $retorno;

        }

        // Nega todos os valores de uma linha, menos o passado por parâmetro.
        public function nega_linha($coordenada) {

            $coluna_atual = $coordenada;

            // Pega o começo da linha da coordenada passada por parâmetro.
            while(($coluna_atual - 1) % count($this->arestas) != 0) {
                $coluna_atual--;
            }

            $retorno = array();

            // Percorre o tamanho da linha.
            for ( $i = 0 ; $i < count($this->arestas) ; $i++ ) {

                // Se a coordenada passada por parâmetro é diferente coordenada da coluna atual, o valor é adicionado o valor no retorno.
                if($coordenada != $coluna_atual) {
                    array_push($retorno, [-$coordenada,-$coluna_atual]);
                }
                
                // Pulo para o proximo valor (na mesma linha e na próxima coluna).
                $coluna_atual++;

            }

            return $retorno;

        }

        // Nega todos os valores da linha da aresta BA, tendo em vista que já temos uma aresta AB.
        public function nega_aresta_voltando($coordenada) {

            // Captura o conteúdo (coordenadas) da tabela no índice (aresta) reverso ao passado por parâmetro, EX: AB pego o conteúdo de BA.
            $linha_aresta_voltando = $this->tabela[strrev($this->captura_valor_aresta($coordenada))];

            $retorno = array();

            // Percorro toda a linha da aresta voltando, negando todos os valores nela.
            for ( $i = 0 ; $i < count($linha_aresta_voltando) ; $i++ ) {
                array_push($retorno, [-$coordenada,-$linha_aresta_voltando[$i]]);
            }

            return $retorno;

        }

        // Gera os possíveis caminhos posteriores a coordenada passada por parâmetro.
        public function busca_arestas_adjacentes($coordenada) {

            /**
             * Se entrar na primeira condição quer dizer que é a ultima coluna, senão: a $proxima_coluna recebe o valor da coluna da coordenada 
             * passada por parâmetro.
             */
            if($coordenada % count($this->arestas) == 0) {
                return array();
            }else{
                $proxima_coluna = $coordenada % count($this->arestas);
            }

            // Exemplo AB: $vertice_anterior recebe A e $vertice_atual recebe B.
            $vertice_anterior = substr($this->captura_valor_aresta($coordenada), 0, 1);
            $vertice_atual =  substr($this->captura_valor_aresta($coordenada), -1);

            // $retorno vai ser uma matriz por questão retorno lógica em funções posteriores.
            $retorno = array(array());

            // Adiciono a coordenada passada por parâmetro no retorno.
            array_push($retorno[0], -$coordenada);

            // Percorro toda a lista de adjacência do $vertice_atual, verificando qual poderia ser o próximo valor (aresta).
            for ( $i = 0 ; $i < count($this->lista_adjacencia[$vertice_atual]) ; $i++ ) {

                // Verifica se o adjacente é diferente do anterior (que no caso seria a aresta voltando: BA)
                if($this->lista_adjacencia[$vertice_atual][$i] != $vertice_anterior) {

                    // Monta o possível valor da próxima aresta, EX: BC.
                    $proxima_aresta = $vertice_atual."".$this->lista_adjacencia[$vertice_atual][$i];

                    // Adiciona o valor da coordenada da $proxima_aresta localizada na $proxima_coluna da tabela; ao retorno.
                    array_push($retorno[0], $this->tabela[$proxima_aresta][$proxima_coluna]);
   
                }

            }

            return $retorno;

        }

        // A cláusula positiva contém uma aresta indo e voltando, tendo em vista que so um valor da clausula acontece.
        public function clausula_positiva($aresta) {

            $retorno = array();

            // Captura todo os valores da linha da aresta indo.
            $linha_aresta_indo = $this->tabela[$aresta];

            // Captura todo os valores da linha da aresta voltando.
            $linha_aresta_voltando = $this->tabela[strrev($aresta)];

            // Junta os dois valores em uma única cláusula.
            array_push($retorno, array_merge($linha_aresta_indo,  $linha_aresta_voltando));

            return $retorno;
        
        }

        // Imprime qualquer um dos vetores retornado pelos 5 métodos anteriores.
        public function imprimeRetorno($vetor) {

            // Percorre todo o $vetor (nega_coluna, nega_linha...) passado por parâmetro, imprimindo todo seu conteúdo ($valor) no formato [X,Z].
            foreach($vetor as $valor){
                echo "[".implode(",", $valor)."] <br>";
            }
            
        }

        // Gera toda a FNC relativa as restrições do grafo.
        function gera_clausulas() {

            // Organiza o vetor arestas em ordem alfabética.
            sort($this->arestas); 
            
            // Crio esse vetor para imprimir posteriormente.
            $this->clausula_positiva = array();

            // Percorro toda a tabela adicionandno a FNC o retorno de nega_coluna($i).
            for ( $i = 1 ; $i <= count($this->tabela) * count($this->arestas) ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_coluna($i));
            }

            // Percorro toda a tabela adicionandno a FNC o retorno de nega_linha($i).
            for ( $i = 1 ; $i <= count($this->tabela) * count($this->arestas) ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_linha($i));
            }

            // Percorro toda a tabela adicionandno a FNC o retorno de nega_aresta_voltando($i).
            for ( $i = 1 ; $i <= count($this->tabela) * count($this->arestas) ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->nega_aresta_voltando($i));
            }

            // Percorro toda a tabela adicionandno a FNC o retorno de busca_arestas_adjacentes($i).
            for ( $i = 1 ; $i <= count($this->tabela) * count($this->arestas) ; $i++ ) {
                $this->fnc = array_merge($this->fnc, $this->busca_arestas_adjacentes($i));
            }

            // Percorro $this->arestas capturando seu valor ($aresta).
            foreach($this->arestas as $aresta) {

                // Adiciono a $this->clausula_positiva o retorno de clausula_positiva($i).
                $this->clausula_positiva = array_merge($this->clausula_positiva, $this->clausula_positiva($aresta));

                // Adiciono a FNC o retorno de clausula_positiva($i).
                $this->fnc = array_merge($this->fnc, $this->clausula_positiva($aresta));

            }

            // echo "<pre> FNC: "; var_dump($this->fnc); echo "</pre>";

            // Faço a requisição ao pythonSAT.
            $this->requisicao();

        }

        // Gera um arquivo com todas as cláusulas e faz a requisição ao pySAT.
        function requisicao() {

            // Cria / Sobrescreve o arquivo entrada.json.
            $arquivo = fopen('entrada.txt','w');

            // Se o arquivo não abre, é retornado a mensagem.
            if ($arquivo == false) {
                echo 'Não foi possível criar o arquivo com as cláusulas.';
            }else{

                $texto = "";
                
                 // Percorre todo o vetor FNC, capturando seu conteúdo ($clausulas)
                foreach($this->fnc as $clausula){
                    $texto .= "[".implode(",", $clausula)."]\n";
                }

                // Retiro o último \n.
                $texto = substr($texto, 0, -1);
                
                // Escrevo no arquivo.
                fwrite($arquivo, $texto);

                // Fecho o arquivo.
                fclose($arquivo);

                // Executo o código em python glucose.py
                $resultado = shell_exec('glucose.py');

                // Quebro o array do resultado retornado pelo código anterior.
                $resultado = explode("\n", $resultado);

                // echo "<pre>"; var_dump($resultado); echo "</pre>";

                // Tiro o último resultado do vetor ("", espaço não utilizado)
                array_pop($resultado);

                // Atribuo os respectivos valores do retorno a suas variáveis.
                $this->status = array_shift($resultado);
                $this->clausulas = array_shift($resultado);
                $this->variaveis = array_shift($resultado);

                // Vetor com o caminho euleriano (solução) do grafo.
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