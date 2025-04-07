DROP DATABASE IF EXISTS GACTI;

CREATE DATABASE IF NOT EXISTS GACTI;
USE GACTI;
# -----------------------------------------------------------------------------
#       TABLE : TYPEANIM
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS TYPEANIM
 (
   CODETYPEANIM CHAR(5) NOT NULL  ,
   NOMTYPEANIM CHAR(50) NULL  
   , PRIMARY KEY (CODETYPEANIM) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : INSCRIPTION
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS INSCRIPTION
 (
   NOINSCRIP BIGINT(6) NOT NULL AUTO_INCREMENT ,
   USER CHAR(8) NOT NULL  ,
   CODEANIM CHAR(8) NOT NULL  ,
   DATEACT DATE NOT NULL  ,
   DATEINSCRIP DATETIME NOT NULL  ,
   DATEANNULE DATE NULL  
   , PRIMARY KEY (NOINSCRIP) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE INSCRIPTION
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_INSCRIPTION_COMPTE
     ON INSCRIPTION (USER ASC);

CREATE  INDEX I_FK_INSCRIPTION_ACTIVITE
     ON INSCRIPTION (CODEANIM ASC,DATEACT ASC);

# -----------------------------------------------------------------------------
#       TABLE : ETATACTIVITE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS ETATACTIVITE
 (
   CODEETATACT CHAR(2) NOT NULL  ,
   NOMETATACT CHAR(25) NULL  
   , PRIMARY KEY (CODEETATACT) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : ACTIVITE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS ACTIVITE
 (
   CODEANIM CHAR(8) NOT NULL  ,
   DATEACT DATE NOT NULL  ,
   CODEETATACT CHAR(2) NOT NULL  ,
   HRRDVACT TIME NOT NULL  ,
   PRIXACT DECIMAL(7,2) NOT NULL  ,
   HRDEBUTACT TIME NOT NULL  ,
   HRFINACT TIME NOT NULL  ,
   DATEANNULEACT DATE NULL  ,
   NOMRESP CHAR(40) NOT NULL  ,
   PRENOMRESP CHAR(30) NOT NULL  
   , PRIMARY KEY (CODEANIM, DATEACT) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE ACTIVITE
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_ACTIVITE_ANIMATION
     ON ACTIVITE (CODEANIM ASC);

CREATE  INDEX I_FK_ACTIVITE_ETATACTIVITE
     ON ACTIVITE (CODEETATACT ASC);

# -----------------------------------------------------------------------------
#       TABLE : ANIMATION
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS ANIMATION
 (
   CODEANIM CHAR(8) NOT NULL  ,
   CODETYPEANIM CHAR(5) NOT NULL  ,
   NOMANIM CHAR(40) NOT NULL  ,
   DATECREATIONANIM DATETIME NOT NULL  ,
   DATEVALIDITEANIM DATETIME NULL  ,
   DUREEANIM REAL(5,0) NULL       ,
   LIMITEAGE INTEGER(2) NULL  ,
   TARIFANIM DECIMAL(7,2) NOT NULL  ,
   NBREPLACEANIM INTEGER(2) NOT NULL  ,
   DESCRIPTANIM CHAR(250) NOT NULL  ,
   COMMENTANIM CHAR(250) NULL  ,
   DIFFICULTEANIM CHAR(40) NULL  
   , PRIMARY KEY (CODEANIM) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE ANIMATION
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_ANIMATION_TYPEANIM
     ON ANIMATION (CODETYPEANIM ASC);

# -----------------------------------------------------------------------------
#       TABLE : COMPTE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS COMPTE (
   USER CHAR(8) NOT NULL,
   MDP VARCHAR(255) NULL,
   NOMCOMPTE VARCHAR(40) NOT NULL,
   PRENOMCOMPTE CHAR(40) NOT NULL,
   DATEINSCRIP DATETIME NOT NULL, -- ✅ Correction ici
   DATEFERME DATETIME NULL,
   TYPEPROFIL CHAR(2) NOT NULL,
   DATEDEBSEJOUR DATE NOT NULL,
   DATEFINSEJOUR DATE NULL,
   DATENAISCOMPTE DATE NOT NULL,
   ADRMAILCOMPTE CHAR(40) NOT NULL,
   NOTELCOMPTE CHAR(15) NOT NULL,
   PRIMARY KEY (USER)
);

CREATE TABLE IF NOT EXISTS TYPEPROFIL (
   TYPEPROFIL CHAR(2) NOT NULL,
   DESCRIPTION VARCHAR(255) NOT NULL,
   PRIMARY KEY (TYPEPROFIL));

# -----------------------------------------------------------------------------
#       CREATION DES REFERENCES DE TABLE
# -----------------------------------------------------------------------------
ALTER TABLE COMPTE 
  ADD FOREIGN KEY FK_COMPTE_TYPEPROFIL (TYPEPROFIL)
      REFERENCES TYPEPROFIL (TYPEPROFIL) ;

ALTER TABLE INSCRIPTION 
  ADD FOREIGN KEY FK_INSCRIPTION_COMPTE (USER)
      REFERENCES COMPTE (USER) ;


ALTER TABLE INSCRIPTION 
  ADD FOREIGN KEY FK_INSCRIPTION_ACTIVITE (CODEANIM,DATEACT)
      REFERENCES ACTIVITE (CODEANIM,DATEACT) ;


ALTER TABLE ACTIVITE 
  ADD FOREIGN KEY FK_ACTIVITE_ANIMATION (CODEANIM)
      REFERENCES ANIMATION (CODEANIM) ;


ALTER TABLE ACTIVITE 
  ADD FOREIGN KEY FK_ACTIVITE_ETATACTIVITE (CODEETATACT)
      REFERENCES ETATACTIVITE (CODEETATACT) ;


ALTER TABLE ANIMATION 
  ADD FOREIGN KEY FK_ANIMATION_TYPE_ANIM (CODETYPEANIM)
      REFERENCES TYPEANIM (CODETYPEANIM) ;

ALTER TABLE COMPTE 
ADD UNIQUE INDEX idx_nom_prenom (NOMCOMPTE, PRENOMCOMPTE);

ALTER TABLE ACTIVITE 
  ADD FOREIGN KEY FK_ACTIVITE_RESP (NOMRESP, PRENOMRESP)
      REFERENCES COMPTE (NOMCOMPTE, PRENOMCOMPTE) ;


ALTER TABLE ANIMATION
  ADD FOREIGN KEY FK_ANIMATION_CODETYPEANIM (CODETYPEANIM)
      REFERENCES TYPEANIM (CODETYPEANIM) ;


-- admin password admin

INSERT INTO TYPEPROFIL (TYPEPROFIL, DESCRIPTION) 
VALUES 
    ('AD', 'ADMIN'), 
    ('EN', 'ENCADRANT'), 
    ('VA', 'VACANCIER');

INSERT INTO ETATACTIVITE (CODEETATACT, NOMETATACT)
VALUES
('OU', 'Activité ouverte'),
('AN', 'Activité annulée'),
('IN', 'Activité incertaines');

INSERT INTO COMPTE (USER, MDP, NOMCOMPTE, PRENOMCOMPTE, DATEINSCRIP, DATEFERME, TYPEPROFIL, DATEDEBSEJOUR, DATEFINSEJOUR, DATENAISCOMPTE, ADRMAILCOMPTE, NOTELCOMPTE)
VALUES
('admin', '$2y$10$F.6Q//T0/.qCHsughR03mOLGjaZgk7eukWSfVi0V88rVFc6XYl0hW', 'Administrateur', 'Super', CURDATE(), null, 'AD', "2025-01-01", null, '1991-10-12', 'admin@example.com', '0123456789')
;



/*

   INSERT INTO TYPEANIM (CODETYPEANIM, NOMTYPEANIM)
VALUES
('FEUCA', 'Feu de camp'),
('SOIRE', 'Soirée Karaoké'),
('CONCD', 'Concours de danse');

INSERT INTO ANIMATION (CODEANIM, CODETYPEANIM, NOMANIM, DATECREATIONANIM, DATEVALIDITEANIM, DUREEANIM, LIMITEAGE, TARIFANIM, NBREPLACEANIM, DESCRIPTANIM, COMMENTANIM, DIFFICULTEANIM)
VALUES
('A001', 'FEUCA', 'Feu de camp', '2025-07-20', NULL, 3.0, NULL, 10, 5, 'Rassemblement autour du feu de camp, où les vacanciers peuvent chanter des chansons, écouter des histoires ou participer à des jeux de groupe.', NULL, 'Facile'),
('A002', 'SOIRE', 'Soirée Karaoké', '2025-07-21', NULL, 2.0, NULL, 20, 6, 'Une soirée où les vacanciers peuvent chanter leurs chansons préférées en solo ou en groupe.', NULL, 'Moyenne'),
('A003', 'CONCD', 'Concours de danse', '2025-07-22', NULL, 2.0, NULL, 30, 7, 'Les vacanciers participent à un concours de danse sur différents styles musicaux, jugé par un jury.', NULL, 'Difficile');


INSERT INTO ACTIVITE (CODEANIM, DATEACT, CODEETATACT, HRRDVACT, PRIXACT, HRDEBUTACT, HRFINACT, DATEANNULEACT, NOMRESP, PRENOMRESP)
VALUES 
('A001', '2025-07-20', 'OU', '08:00:00', 10, '08:00:00', '11:00:00', NULL, 'Administrateur', 'Super'),
('A001',  '2025-08-21', 'OU','06:45:00', 10, '07:00:00', '10:00:00', NULL, 'Administrateur', 'Super'),
('A001',  '2025-08-22', 'OU','08:45:00', 10, '09:00:00', '10:00:00', NULL, 'Administrateur', 'Super'),
('A002',  '2025-06-21', 'OU','09:45:00', 20, '10:00:00', '12:00:00', NULL, 'Administrateur', 'Super'),
('A002', '2025-07-21', 'IN',  '14:00:00', 20, '14:00:00', '15:00:00', NULL, 'Administrateur', 'Super'),
('A003',  '2025-07-22', 'IN','15:00:00', 20, '15:00:00', '16:00:00', NULL, 'Administrateur', 'Super');
*/

select * from ETATACTIVITE;

select * from COMPTE;
select * from ANIMATION;
select * from ACTIVITE a ;
select * from ACTIVITE a where CODEETATACT='OU' ;
select * from INSCRIPTION i ;
select count(*) as nn from INSCRIPTION i ;

SELECT a.HRDEBUTACT, a.HRFINACT
   FROM INSCRIPTION i
   JOIN ACTIVITE a ON i.CODEANIM = a.CODEANIM AND i.DATEACT = a.DATEACT
   WHERE i.USER = 'admin' AND i.DATEACT = '2025-07-20';
  
 SELECT a.LIMITEAGE, c.DATENAISCOMPTE 
           FROM ANIMATION a, COMPTE c 
           WHERE a.CODEANIM = 'A001' AND c.USER = 'va1'
           ;

select * from TYPEPROFIL
;
select * from TYPEANIM