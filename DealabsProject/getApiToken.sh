token="Bearer "
tokenValue=$(bin/console doctrine:query:sql 'SELECT * FROM api_token' | grep  'string(120)' | cut -d '"' -f 2)

echo "${token}${tokenValue}";
