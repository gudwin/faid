#!/bin/bash
clear
echo $'\n'
echo "Welcome to faid tests";

echo $'\n'[Test Configure]
./testConfigure.sh

echo $'\n'[Test Views]
./testView.sh

echo $'\n'[Test Dispatcher]
./testDispatcher.sh

echo $'\n'[Test Staticobservable]
./testStaticobservable.sh

echo $'\n'[Test Controller]
./testController.sh

echo $'\n'[Test Models]
./testModels.sh
