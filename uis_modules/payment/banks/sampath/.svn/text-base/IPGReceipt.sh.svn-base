#!/bin/bash
CMD_ROOT='/home/nayanajith/Projects/ucscsis/mod/payment/banks/sampath'
cd $CMD_ROOT

#RUN
#Ex: ./IPGReceipt.sh -k ipgkeys/ -r receipt

java -cp lib/commons-cli-1.2.jar:lib/iclient.jar:lib/ibmjceprovider.jar:lib/ibmpkcs.jar:. IPGReceipt $@ | awk -F#### '{print $2}'
