#!/bin/bash
#CMD_ROOT='/home/nayanajith/Projects/ucscsis/mod/payment/banks/sampath'
CMD_ROOT='/home/nayanajith/Projects/workspace/ucscsis/mod/payment/banks/sampath'
cd $CMD_ROOT

#RUN
#Ex: ./IPGInvoice.sh -am 25000 -kp ipgkeys/ -mi m -ru 'http://ucsc.lk/pg' -ti t

java -cp lib/commons-cli-1.2.jar:lib/iclient.jar:lib/ibmjceprovider.jar:lib/ibmpkcs.jar:. IPGInvoice $@ | awk -F#### '{print $2}'
