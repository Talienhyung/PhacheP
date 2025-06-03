# 🌐 Projet PHP sous XAMPP

## 📜 Description

Ce projet est une application développée en PHP, destinée à fonctionner en environnement local via **XAMPP**. Elle permet de [décrire brièvement ce que fait ton projet : gestion d’utilisateurs, blog, interface de données, etc.].

## ⚙️ Technologies utilisées

- PHP [version, ex. 8.2]
- MySQL / MariaDB
- HTML / CSS / JavaScript
- XAMPP (Apache + MySQL)

## 🧭 Installation

### 1. Prérequis
Assure-toi que XAMPP est installé sur ta machine :  
👉 [Télécharger XAMPP](https://www.apachefriends.org/index.html)

### 2. Démarrage des services
Lance les services **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.

### 3. Installation du projet

1. Clone ou télécharge ce dépôt :
   ```bash
   git clone https://github.com/Talienhyung/PhacheP.git
````

2. Place les fichiers dans le dossier `htdocs` de XAMPP :

   ```
   C:\xampp\htdocs\phachep
   ```

3. Importe le fichier `requeteSQL.sql` via **phpMyAdmin** :

   * Accède à : `http://localhost/phpmyadmin`
   * Crée une base de données (ex. `nom_de_ta_bdd`)
   * Importe le fichier `.sql`
   * Des données fictives sont dans les autres fichier sql

4. Configure le fichier de connexion à la base de données `db_config.php`:

   ```php
    $DB_HOST = 'localhost';
    $DB_NAME = 'nom_de_ta_bdd';
    $DB_USER = 'root';
    $DB_PASS = '';
   ```

### 4. Lancer l'application

Accède à ton projet via un navigateur à l’adresse suivante :

```
http://localhost/phachep
```


## ✍️ Auteur

* Tibor Lassalle
* William Habberjam
* Soléane Rivier

