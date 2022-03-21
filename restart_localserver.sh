#!/bin/bash
#chgrp http ./*
# chmod 776 ./*
sudo systemctl restart mysqld
sudo systemctl restart httpd

