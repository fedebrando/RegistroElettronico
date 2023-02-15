USE Scuola;

INSERT INTO Materia(Nome) VALUES
('Italiano'),
('Matematica'),
('Storia'),
('Geografia'),
('Inglese'),
('Disegno tecnico'),
('Informatica'),
('Diritto ed Economia'),
('Sistemi e Reti'),
('Tecnologia');

INSERT INTO Docente(Username, PswHash, Amministratore, Nome, Cognome, DataNascita, Email) VALUES
('zollari', 'd760688da522b4dc3350e6fb68961b0934f911c7d0ff337438cabf4608789ba94ce70b6601d7e08a279ef088716c4b1913b984513fea4c557d404d0598d4f2f1', 1, 'Paolo', 'Ollari', '1967-01-25', 'paolone@gmail.com'),
('dferrari', 'd760688da522b4dc3350e6fb68961b0934f911c7d0ff337438cabf4608789ba94ce70b6601d7e08a279ef088716c4b1913b984513fea4c557d404d0598d4f2f1', 0, 'Lea', 'Ferrari', '1967-01-25', 'ferrarilea@gmail.com'),
('dmassera', 'd760688da522b4dc3350e6fb68961b0934f911c7d0ff337438cabf4608789ba94ce70b6601d7e08a279ef088716c4b1913b984513fea4c557d404d0598d4f2f1', 0, 'Renata', 'Massera', '1967-01-25', 'renatina@gmail.com'),
('ddoberti', 'd760688da522b4dc3350e6fb68961b0934f911c7d0ff337438cabf4608789ba94ce70b6601d7e08a279ef088716c4b1913b984513fea4c557d404d0598d4f2f1', 0, 'Alessandra', 'Doberti', '1967-01-25', 'alledobby@gmail.com'),
('dpaganuzzi', 'd760688da522b4dc3350e6fb68961b0934f911c7d0ff337438cabf4608789ba94ce70b6601d7e08a279ef088716c4b1913b984513fea4c557d404d0598d4f2f1', 0, 'Alberto', 'Paganuzzi', '1967-01-25', 'apaganuzzimail@gmail.com');

INSERT INTO Aula(Codice, Piano, LIM) VALUES
('U1', -1, 1),
('U2', -1, 1),
('T1', 0, 0),
('T2', 0, 0),
('T3', 0, 0),
('T4', 0, 1),
('O1', 1, 0),
('O2', 1, 0),
('O3', 1, 1),
('O4', 1, 1);

INSERT INTO Classe(Anno, Sezione) VALUES
(5, 'A'),
(5, 'B');

INSERT INTO Abilitazione(UserDocente, Materia) VALUES
('zollari', 'Sistemi e Reti'),
('dferrari', 'Matematica'),
('dmassera', 'Inglese'),
('ddoberti', 'Italiano'),
('ddoberti', 'Storia'),
('dpaganuzzi', 'Informatica');

INSERT INTO Lezione(UserDocente, Materia, Aula, AnnoClasse, SezClasse, Giorno, Ora) VALUES
('zollari', 'Sistemi e Reti', 'T4', 5, 'A', 'lunedì', 1),
('zollari', 'Sistemi e Reti', 'T4', 5, 'A', 'lunedì', 2),
('ddoberti', 'Italiano', 'U1', 5, 'A', 'lunedì', 3),
('dmassera', 'Inglese', 'U1', 5, 'A', 'lunedì', 4),
('dferrari', 'Matematica', 'U1', 5, 'A', 'lunedì', 5),
('ddoberti', 'Storia', 'U2', 5, 'A', 'martedì', 1),
('ddoberti', 'Storia', 'U2', 5, 'A', 'martedì', 2),
('dpaganuzzi', 'Informatica', 'O1', 5, 'A', 'martedì', 3),
('dpaganuzzi', 'Informatica', 'O1', 5, 'A', 'martedì', 4),
('dmassera', 'Inglese', 'O1', 5, 'A', 'martedì', 5),
('zollari', 'Sistemi e Reti', 'T4', 5, 'A', 'mercoledì', 1),
('zollari', 'Sistemi e Reti', 'T4', 5, 'A', 'mercoledì', 2),
('dferrari', 'Matematica', 'T1', 5, 'A', 'mercoledì', 3),
('ddoberti', 'Italiano', 'T1', 5, 'A', 'mercoledì', 4),
('ddoberti', 'Italiano', 'T1', 5, 'A', 'mercoledì', 5),
('dmassera', 'Inglese', 'O2', 5, 'A', 'giovedì', 1),
('dferrari', 'Matematica', 'O2', 5, 'A', 'giovedì', 2),
('dpaganuzzi', 'Informatica', 'T4', 5, 'A', 'giovedì', 3),
('dpaganuzzi', 'Informatica', 'T4', 5, 'A', 'giovedì', 4),
('dpaganuzzi', 'Informatica', 'T4', 5, 'A', 'giovedì', 5),
('zollari', 'Sistemi e Reti', 'T4', 5, 'A', 'venerdì', 1),
('zollari', 'Sistemi e Reti', 'T4', 5, 'A', 'venerdì', 2),
('ddoberti', 'Italiano', 'T3', 5, 'A', 'venerdì', 3),
('dferrari', 'Matematica', 'T3', 5, 'A', 'venerdì', 4),
('ddoberti', 'Storia', 'T3', 5, 'A', 'venerdì', 5),
('dferrari', 'Matematica', 'T3', 5, 'A', 'sabato', 1),
('dpaganuzzi', 'Informatica', 'T3', 5, 'A', 'sabato', 2),
('dmassera', 'Inglese', 'U1', 5, 'A', 'sabato', 3),
('zollari', 'Sistemi e Reti', 'T4', 5, 'A', 'sabato', 4),
('zollari', 'Sistemi e Reti', 'T4', 5, 'A', 'sabato', 5),
('zollari', 'Sistemi e Reti', 'O2', 5, 'B', 'martedì', 2),
('zollari', 'Sistemi e Reti', 'O2', 5, 'B', 'giovedì', 5);

INSERT INTO Assegnamento(UserDocente, AnnoClasse, SezClasse) VALUES
('zollari', 5, 'A'),
('zollari', 5, 'B'),
('dferrari', 5, 'A'),
('dmassera', 5, 'A'),
('ddoberti', 5, 'A'),
('dpaganuzzi', 5, 'A');
