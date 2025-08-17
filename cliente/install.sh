#!/bin/bash
find . -name "package.json" -not -path "*/node_modules/*" -execdir npm install \;

