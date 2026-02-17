DROP DATABASE IF EXISTS lemotocoin;
CREATE DATABASE lemotocoin;
USE lemotocoin;

CREATE TABLE Vendeurs(
        idvendeur Int  Auto_increment  NOT NULL PRIMARY KEY ,
        nom       Varchar (50) NOT NULL ,
        prenom    Varchar (50) NOT NULL ,
        phone     Varchar (50) NOT NULL ,
        mail      Varchar (100) NOT NULL ,
        mdp       Varchar (100) NOT NULL ,
        adresse   Varchar (100) NOT NULL 
	
);



DROP TABLE IF EXISTS Annonces;
CREATE TABLE Annonces(
    idannonce INT AUTO_INCREMENT PRIMARY KEY,
    modele VARCHAR(100) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    annee INT NOT NULL,
    couleur VARCHAR(50) NOT NULL,
    kilometrage INT NOT NULL,
    description TEXT NOT NULL,
    photo VARCHAR(255),
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    idvendeur INT NOT NULL,
    FOREIGN KEY (idvendeur) REFERENCES Vendeurs(idvendeur)
);
	




CREATE TABLE Annonces(
        idannonce Int  Auto_increment  NOT NULL PRIMARY KEY,
        model     Varchar (100) NOT NULL ,
        km        Varchar (100) NOT NULL ,
        annee     Varchar (100) NOT NULL ,
        couleur   Varchar (100) NOT NULL ,
        photo     Blob NOT NULL ,
        idvendeur Int,
        prix      Int NOT NULL ,
        description Varchar (200) NOT NULL ,
	

	FOREIGN KEY (idvendeur) REFERENCES Vendeurs(idvendeur)
);



CREATE TABLE contacter(
        idclient  Int NOT NULL ,
        idvendeur Int NOT NULL ,
        message   Varchar (200) NOT NULL ,
        date      Date NOT NULL,
	

	 FOREIGN KEY (idclient) REFERENCES Client(idclient),
	FOREIGN KEY (idvendeur) REFERENCES Vendeurs(idvendeur)
);

