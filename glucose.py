#!/usr/bin/python

# pip install python-sat

import sys, json

try:
    data = json.loads(sys.argv[1])
except:
    print ("ERROR")
    sys.exit(1)

# Send it to stdout (to PHP)
# print (json.dumps(data))

from pysat.solvers import Glucose4
from pysat.formula import CNF

g = Glucose4()

formula = CNF()

for x in data:

    predicado = []

    for y in x:
        predicado.append(y)

    # print(predicado)

    formula.append(predicado)

g.append_formula(formula)

print(g.solve())
print(g.get_model())