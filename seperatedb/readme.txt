Steps To Test "Seperate DB Functionality"

***************************************************
There is a folder "seperatedb" in the project zip file shared which contains files required in the steps mentioned below.
***************************************************

1. Create a new database "societydb".
2. Import "societydb.sql" file into "societydb".
3. Create 3 new databases named "society1", "society2" and "society3"
(Note : You can create these db with any name. However you will need to update the "dbname" table in "societydb" with the name of the database).
4. Import "societies_empty.sql" in each of the newly created database above.
5. Open the website in localhost.
6. Login with Username : sadmin and Password : sadmin
(Note : Facebook login will not work on localhost. Use the normal login)
7. If you see any type of exception such as "'Facebook needs the CURL PHP extension", go to path "C:\wamp\bin\php\php5.3.13\ext", rename the existing php_curl.dll and copy the php_curl.dll from folder "seperatedb" to the above location.



