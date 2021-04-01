#!/usr/bin/python

# pip install python-sat

from pysat.solvers import Glucose4
from pysat.formula import CNF

g = Glucose4()

formula = CNF()

with open('entrada.txt') as f:
    mylist = [line for line in f]

newstr = ""

for x in mylist:
    newstr += x

newstr = newstr.split('\n')

for x in newstr:

    # print(x);

    formula.append(eval(x))

g.append_formula(formula)

print("Válida: " ,g.solve(), "<br>")
print("Solução: " ,g.get_model(), "<br>")
print("Prova: " ,g.get_proof(), "<br>")
print("Status: " ,g.get_status(), "<br>")
print("Cláusulas: " ,g.nof_clauses(), "<br>")
print("Variáveis: " ,g.nof_vars(), "<br>")