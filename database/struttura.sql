DROP DATABASE IF EXISTS Scuola;
CREATE DATABASE Scuola;
USE Scuola;

CREATE TABLE Docente
(
	Username VARCHAR(15) NOT NULL,
	PswHash CHAR(128) NOT NULL,
	Amministratore BIT NOT NULL,
	CF CHAR(16) DEFAULT NULL,
	Nome VARCHAR(30) NOT NULL,
	Cognome VARCHAR(30) NOT NULL,
	DataNascita DATE NOT NULL,
	Email VARCHAR(40) NOT NULL,
	Localita VARCHAR(30) DEFAULT NULL,
	Civico INT DEFAULT NULL CHECK(Civico > 0),
	CAP CHAR(5) DEFAULT NULL,
	PRIMARY KEY(Username)
);

CREATE TABLE Materia
(
	Nome VARCHAR(30) PRIMARY KEY
);

CREATE TABLE Aula
(
	Codice VARCHAR(10) NOT NULL,
	Piano INT NOT NULL,
	LIM BIT NOT NULL,
	PRIMARY KEY(Codice)
);

CREATE TABLE Classe
(
	Anno INT(1) NOT NULL CHECK(Anno > 0),
	Sezione VARCHAR(2) NOT NULL,
	Articolazione VARCHAR(30) DEFAULT NULL,
	PRIMARY KEY(Anno, Sezione)
);

CREATE TABLE Studente
(
	Username VARCHAR(15) NOT NULL,
	PswHash CHAR(128) NOT NULL,
	CF CHAR(16) DEFAULT NULL,
	Nome VARCHAR(30) NOT NULL,
	Cognome VARCHAR(30) NOT NULL,
	DataNascita DATE NOT NULL,
	Email VARCHAR(40) NOT NULL,
	Localita VARCHAR(30) DEFAULT NULL,
	Civico INT DEFAULT NULL CHECK(Civico > 0),
	CAP CHAR(5) DEFAULT NULL,
	AnnoClasse INT(1) NOT NULL,
	SezClasse VARCHAR(2) NOT NULL,
	PRIMARY KEY(Username),
	FOREIGN KEY(AnnoClasse, SezClasse) REFERENCES Classe(Anno, Sezione)
);

CREATE TABLE Abilitazione
(
	UserDocente VARCHAR(15) NOT NULL,
	Materia VARCHAR(30) NOT NULL,
	PRIMARY KEY(UserDocente, Materia),
	FOREIGN KEY(UserDocente) REFERENCES Docente(Username),
	FOREIGN KEY(Materia) REFERENCES Materia(Nome)
);

CREATE TABLE Lezione
(
	UserDocente VARCHAR(15) NOT NULL,
	Materia VARCHAR(30) NOT NULL,
	Aula VARCHAR(10) NOT NULL,
	AnnoClasse INT(1) NOT NULL,
	SezClasse VARCHAR(2) NOT NULL,
	Giorno VARCHAR(9) NOT NULL CHECK(Giorno IN ('lunedì', 'martedì', 'mercoledì', 'giovedì', 'venerdì', 'sabato')),
	Ora INT(1) NOT NULL CHECK(Ora > 0),
    UNIQUE(Aula, Giorno, Ora),
    UNIQUE(AnnoClasse, SezClasse, Giorno, Ora),
	PRIMARY KEY(UserDocente, Giorno, Ora),
	FOREIGN KEY(UserDocente) REFERENCES Docente(Username),
	FOREIGN KEY(Materia) REFERENCES Materia(Nome),
	FOREIGN KEY(AnnoClasse, SezClasse) REFERENCES Classe(Anno, Sezione)
);

CREATE TABLE Valutazione
(
	ID INT auto_increment NOT NULL,
	UserDocente VARCHAR(15) NOT NULL,
	Materia VARCHAR(30) NOT NULL,
	UserStudente VARCHAR(15) NOT NULL,
	Data DATE NOT NULL,
	Voto INT(2) NOT NULL CHECK(0 <= Voto AND Voto <= 10),
	Nota VARCHAR(100) DEFAULT NULL,
	PRIMARY KEY (ID),
	FOREIGN KEY(UserDocente) REFERENCES Docente(Username),
	FOREIGN KEY(Materia) REFERENCES Materia(Nome),
	FOREIGN KEY(UserStudente) REFERENCES Studente(Username)
);

CREATE TABLE Assegnamento
(
	UserDocente VARCHAR(15) NOT NULL,
	AnnoClasse INT(1) NOT NULL,
	SezClasse VARCHAR(2) NOT NULL,
	PRIMARY KEY(UserDocente, AnnoClasse, SezClasse),
	FOREIGN KEY(UserDocente) REFERENCES Docente(Username),
	FOREIGN KEY(AnnoClasse, SezClasse) REFERENCES Classe(Anno, Sezione)
);
