#!/bin/sh

cd docs

gitbook build

cd ..

git checkout gh-pages

git pull origin gh-pages --rebase

cp -R ./docs/_book/* .

git add .

git commit -a -m "Update docs."

git push origin gh-pages

git checkout master
