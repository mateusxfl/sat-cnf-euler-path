#!/usr/bin/python

# pip install python-sat

from pysat.solvers import Glucose4
from pysat.formula import CNF

g = Glucose4()

formula = CNF()

with open('entrada.txt') as f:
    mylist = [line for line in f]

clausulas = ""

for x in mylist:
    clausulas += x

clausulas = clausulas.split('\n')

for x in clausulas:
    formula.append(eval(x))

g.append_formula(formula)

print("<strong> Válida: </strong>" ,g.solve(), "<br>")
#print("Solução: " ,g.get_model(), "<br>")
print("<strong> Cláusulas: </strong>" ,g.nof_clauses(), "<br>")
print("<strong> Variáveis: </strong>" ,g.nof_vars(), "<br>")

print("<br><strong> Solução: </strong><br>")
for p in g.get_model():
  if p > 0:
    print(p-1, clausulas[p-1], end=' <br> ')