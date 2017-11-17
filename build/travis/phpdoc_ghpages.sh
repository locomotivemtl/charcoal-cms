#!/bin/bash

## From http://rootslabs.net/blog/511-publier-automatiquement-phpdoc-github-travis-ci

# echo "TRAVIS_REPO_SLUG: $TRAVIS_REPO_SLUG"
# echo "TRAVIS_PHP_VERSION: $TRAVIS_PHP_VERSION"
# echo "TRAVIS_PULL_REQUEST: $TRAVIS_PULL_REQUEST"
# echo "TRAVIS_BRANCH: $TRAVIS_BRANCH"
# echo "TRAVIS_BUILD_NUMBER: $TRAVIS_BUILD_NUMBER"

if [ "$TRAVIS_REPO_SLUG" == "locomotivemtl/charcoal-cms" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "7.1" ]; then

    echo -e "Publishing PHPDoc to Github Pages...\n"

    pwd

    mkdir -p ./build/docs;

    ## Install the PHPdoc binary
    pear channel-discover pear.phpdoc.org
    pear install phpdoc/phpDocumentor
    phpenv rehash

    # Generate the phpdoc files
    phpdoc -p -d ./src -t ./build/docs

    # Copy generated doc in $HOME
    cp -R build/docs $HOME/docs-latest

    cd $HOME

    # Clone gh-pages branch
    git config --global user.email "travis@travis-ci.org"
    git config --global user.name "travis-ci"
    git clone --quiet --branch=gh-pages https://${GH_TOKEN}@${GH_REPO} gh-pages > /dev/null

    cd gh-pages

    ## Suppression de l'ancienne version
    git rm -rf ./docs/$TRAVIS_BRANCH

    ## Création des dossiers
    mkdir docs
    cd docs
    mkdir $TRAVIS_BRANCH

    ## Copie de la nouvelle version
    cp -Rf $HOME/docs-latest/* ./$TRAVIS_BRANCH/
    rm -rf ./$TRAVIS_BRANCH/phpdoc-cache-*

    git add -f .
    git commit -m "PHPDocumentor (Travis Build : $TRAVIS_BUILD_NUMBER  - Branch : $TRAVIS_BRANCH)"
    git push -fq origin gh-pages > /dev/null

    echo -e "Published PHPDoc to gh-pages.\n"
    echo -e ">>> http://locomotivemtl.github.io/charcoal-cms/docs/$TRAVIS_BRANCH/ \n"

fi

