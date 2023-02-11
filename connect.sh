#!/usr/bin/env bash

if [ -z "$1" ]; then
    printf "\033[1;31mError: Please specify an environment (e.g. ./connect.sh staging)\033[0m \n";
    exit 0;
fi

##### Environment Config #####

if [ $1 = "staging" ]; then
    ssh -t anglingtrust@ds2.barqueshosting.co.uk -p 22007 "cd ~/api.angling-trust.goodformtest.co.uk ; bash -l";
    exit 1;
fi

if [ $1 = "production" ]; then
    ssh -t forge@134.122.96.86 "cd ~/api.anglingtrust.net ; bash -l";
    exit 1;
fi

##### Environment Config #####

printf "\033[1;31mError: Environment\033[0m \033[1;33m$1\033[0m \033[1;31mnot set\033[0m \n";
