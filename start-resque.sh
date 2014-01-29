#!/bin/bash

VVERBOSE=1 QUEUE=ipp-requests,qbxml INTERVAL=15 APP_INCLUDE=app/connect.php php vendor/bin/resque
