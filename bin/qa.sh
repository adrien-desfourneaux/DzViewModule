#!/bin/bash

# /*!
#     Script d'assurance qualité (Quality Assurance)
#     Vérifie la qualité du code
#     @author Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
#  */

# /*!
#     Change de répertoire vers le répertoire du module
#  */
cdmodulepath () {
    if [ -z $SCRIPTPATH ]; then
        SCRIPTPATH=$( cd "$(dirname "$0")" ; pwd -P )
        cd $SCRIPTPATH/..
    fi
}

# /*!
#     Définit les fixers à utiliser pour le php-cs-fixer
#  */
setphpcsfixers () {
    #indentation [PSR-2] Code must use 4 spaces for indenting, not tabs.
    fixers="indentation"
    # linefeed [PSR-2] All PHP files must use the Unix LF (linefeed) line ending.
    fixers="$fixers,linefeed"
    # trailing_spaces [PSR-2] Remove trailing whitespace at the end of lines.
    fixers="$fixers,trailing_spaces"
    # unused_use [all] Unused use statements must be removed.
    fixers="$fixers,unused_use"
    # phpdoc_params [all] All items of the @param phpdoc tags must be aligned vertically.
    fixers="$fixers,phpdoc_params"
    # short_tag [PSR-1] PHP code must use the long <?php ?> tags or the short-echo <?= ?> tags; it must not use the other tag variations.
    fixers="$fixers,short_tag"
    # return [all] An empty line feed should precede a return statement.
    fixers="$fixers,return"
    # visibility [PSR-2] Visibility must be declared on all properties and methods; abstract and final must be declared before the visibility; static must be declared after the visibility.
    fixers="$fixers,visibility"
    # php_closing_tag [PSR-2] The closing ?> tag MUST be omitted from files containing only PHP.
    fixers="$fixers,php_closing_tag"
    # braces [PSR-2] Opening braces for classes, interfaces, traits and methods must go on the next line, and closing braces must go on the next line after the body. Opening braces for control structures must go on the same line, and closing braces must go on the next line after the body.
    fixers="$fixers,braces"
    # extra_empty_lines [all] Removes extra empty lines.
    fixers="$fixers,extra_empty_lines"
    # function_declaration [PSR-2] Spaces should be properly placed in a function declaration
    fixers="$fixers,function_declaration"
    # include [all] Include and file path should be divided with a single space. File path should not be placed under brackets.
    fixers="$fixers,include"
    # controls_spaces [all] A single space should be between: the closing brace and the control, the control and the opening parentheses, the closing parentheses and the opening brace.
    fixers="$fixers,controls_spaces"
    # psr0 [PSR-0] Classes must be in a path that matches their namespace, be at least one namespace deep, and the class name should match the file name.
    # Do not use this fixer
    fixers="$fixers,-psr0"
    # elseif [PSR-2] The keyword elseif should be used instead of else if so that all control keywords looks like single words.
    fixers="$fixers,elseif"
    # eof_ending [PSR-2] A file must always end with an empty line feed.
    fixers="$fixers,eof_ending"
}

# /*!
#     Lance le code sniffer php-cs-fixer (PHP Coding Standards Fixer)
#  */
runcodesniffer () {
    cdmodulepath
    setphpcsfixers

    [[ -n $(vendor/bin/php-cs-fixer fix src --dry-run --verbose --fixers=$fixers) ]] || printf "Aucune violation de standard n'a été trouvée\n"
}

# /*!
#     Lance le code fixer
#  */
runcodefixer () {
    cdmodulepath
    setphpcsfixers

    vendor/bin/php-cs-fixer fix src --verbose --fixers=$fixers
}

# /*!
#     Génére la documentation du module
#  */
gendoc () {
    cdmodulepath
    mkdir -p doc
    vendor/bin/phpdoc.php run -d src -t doc
}

# /*!
#     Vérifie la documentation (docblocks) du module
#  */
checkdoc () {
    cdmodulepath

    Category="(\<Config\>|\<Autoload\>|\<Source\>|\<Spec\>|\<Test\>)$"
    Package="\<DzViewModule(/.*|\>)"
    Author="Adrien Desfourneaux \(aka Dieze\) <dieze51@gmail\.com>$"
    License="http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0$"
    Link="https://github.com/dieze/DzViewModule$"

    find src \( -name "*.php" \) | xargs grep -E -sL "@category\s+${Category}" | awk '{print "Mauvaise catégorie : "$1}'
    find src \( -name "*.php" \) | xargs grep -E -sL "@package\s+${Package}" | awk '{print "Mauvais nom de package : "$1}'
    find src \( -name "*.php" \) | xargs grep -E -sL "@author\s+${Author}" | awk '{print "Mauvais auteur : "$1}'
    find src \( -name "*.php" \) | xargs grep -E -sL "@license\s+${License}" | awk '{print "Mauvaise licence : "$1}'
    find src \( -name "*.php" \) | xargs grep -E -sL "@link\s+${Link}" | awk '{print "Mauvais lien : "$1}'
}

# /*!
#     Génère le classmap pour l'autoloader
#  */
genclassmap () {
    cdmodulepath

    ../../vendor/bin/classmap_generator.php --library src/DzViewModule --output ./autoload_classmap.php --overwrite --sort .
}

# /*!
#     Affiche l'aide
#  */
help () {
    printf "Usage: qa.sh [command]\n"
    printf "help\t\tAffiche cette aide\n"
    printf "help [command]\tAffiche l'aide de la commande\n"
    printf "code\t\tGestion du code source\n"
    printf "doc\t\tGestion de la documentation du module\n"
    printf "loader\t\tGestion du loader du module\n"
    printf "\nAffiche cette aide si aucune action n'est spécifiée\n"
}

# /*!
#      Affiche l'aide de la gestion du code
#  */
helpcode () {
    printf "Usage: qa.sh code [command]\n"
    printf "check\tVéfifie la conformité du code aux standards\n"
    printf "fix\tRésout les problèmes de conformité du code aux standards\n"
}

# /*!
#     Affiche l'aide de la gestion de la documentation
#  */
helpdoc () {
    printf "Usage: qa.sh doc [action]\n"
    printf "check\tvérifie les blocs de documentation de code\n"
    printf "gen\tGénère la documentation du module\n"
}

# /*!
#     Affiche l'aide de la gestion du loader du module
#  */
helploader () {
    printf "Usage: qa.sh loader [arg]\n"
    printf "classmap\tGénère le classmap pour l'autoload\n"
} 

# no argument
if [ $# -eq 0 ]; then help

# help
elif [ $1 = 'help' ]; then
    if [ $# -eq 1 ]; then help
    elif [ $2 = 'code' ]; then helpcode
    elif [ $2 = 'doc' ]; then helpdoc
    elif [ $2 = 'loader' ]; then helploader
    else help
    fi

# code
elif [ $1 = 'code' ]; then
    if [ $# -eq 1 ]; then helpcode
    elif [ $2 = 'check' ]; then runcodesniffer
    elif [ $2 = 'fix' ]; then runcodefixer
    else helpcode
    fi

# doc
elif [ $1 = 'doc' ]; then
    if [ $# -eq 1 ]; then helpdoc
    elif [ $2 = 'check' ]; then checkdoc
    elif [ $2 = 'gen' ]; then gendoc
    else helpdoc
    fi

# loader
elif [ $1 = 'loader' ]; then
    if [ $# -eq 1 ]; then helploader
    elif [ $2 = 'classmap' ]; then genclassmap
    else helpdoc
    fi

# help
else help;

fi
