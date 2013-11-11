sudo -u abhi sqoop import --connect jdbc:mysql://localhost/login --username root --table login
echo $?
