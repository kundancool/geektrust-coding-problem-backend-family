#!/bin/bash

tests="input.txt
input1.txt
input2.txt
sample1.txt
sample2.txt
sample3.txt"

for i in $tests; do
	echo -e "\nRunning: php geektrust.php $i"
	php geektrust.php $i
done
