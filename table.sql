CREATE TABLE zabc_modules (name VARCHAR(254) NOT NULL PRIMARY KEY, score int, version VARCHAR(254), description VARCHAR(254),updated int,FULLTEXT (name,description));
