# From https://github.com/ApiGen/ApiGen/wiki/Generate-API-to-Github-pages-via-Travis

# echo "TRAVIS_REPO_SLUG: $TRAVIS_REPO_SLUG"
# echo "TRAVIS_PHP_VERSION: $TRAVIS_PHP_VERSION"
# echo "TRAVIS_PULL_REQUEST: $TRAVIS_PULL_REQUEST"
# echo "TRAVIS_BRANCH: $TRAVIS_BRANCH"
# echo "TRAVIS_BUILD_NUMBER: $TRAVIS_BUILD_NUMBER"

if [ "$TRAVIS_REPO_SLUG" == "locomotivemtl/charcoal-cms" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "7.1" ]; then

    echo -e "Publishing ApiGen to Github Pages...\n";

    pwd

    mkdir -p ./build/apidocs;

    # Get apigen binary
    wget http://www.apigen.org/apigen.phar

    # Generate Api
    php apigen.phar generate -s ./src -d ./build/apidocs

    # Copy generated doc in $HOME
    cp -R build/apidocs $HOME/apidocs-latest

    cd $HOME

    ## Clone gh-pages branch
    git config --global user.email "travis@travis-ci.org"
    git config --global user.name "travis-ci"
    git clone --quiet --branch=gh-pages https://${GH_TOKEN}@${GH_REPO} api-pages > /dev/null

    cd api-pages

    ## Suppression de l'ancienne version
    git rm -rf ./apigen/$TRAVIS_BRANCH

    ## Création des dossiers
    mkdir apigen
    cd apigen
    mkdir $TRAVIS_BRANCH

    ## Copie de la nouvelle version
    cp -Rf $HOME/apidocs-latest/* ./$TRAVIS_BRANCH/

    git add -f .
    git commit -m "ApiGen (Travis Build : $TRAVIS_BUILD_NUMBER  - Branch : $TRAVIS_BRANCH)"
    git push -fq origin gh-pages > /dev/null

    echo -e "Published ApiGen to gh-pages.\n"
    echo -e ">>> http://locomotivemtl.github.io/charcoal-cms/apigen/$TRAVIS_BRANCH/ \n"
fi
