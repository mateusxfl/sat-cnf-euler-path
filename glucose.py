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

print(g.solve())
print(g.nof_clauses())
print(g.nof_vars())

# print("Solução: " ,g.get_model(), "<br>")

for s in g.get_model():
  if s > 0:
    # print(s-1)
    print(s)