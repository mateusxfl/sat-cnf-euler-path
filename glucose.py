#!/usr/bin/python

# pip install python-sat

# Importa o Glucose4 e a CNF.
from pysat.solvers import Glucose4
from pysat.formula import CNF

# Instancia o glucose.
g = Glucose4()

# Instancia a formula.
formula = CNF()

# Abre o arquivo entrada.txt e todo seu conteúdo é atribuido ao vetor conteudo.
with open('entrada.txt') as f:
    conteudo = [line for line in f]

clausulas = ""

# Percorro todo o vetor conteudo, adicionando seu conteudo em uma string (clausulas).
for x in conteudo:
    clausulas += x

# Divido a string clausulas a cada linha (\n).
clausulas = clausulas.split('\n')

# Adiciono todas as linhas da clausula na formula SAT.
for x in clausulas:
    formula.append(eval(x))

# Adiciona a formula no glucose.
g.append_formula(formula)

# Retorna resultados.
print(g.solve())
print(g.nof_clauses())
print(g.nof_vars())

# Imprime todos os valores positivos (caminho euleriano válido) no vetor com todas as variáveis. 
for s in g.get_model():
  if s > 0:
    print(s)