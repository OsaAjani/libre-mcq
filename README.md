# Libre MCQ 📝

Libre MCQ est une plateforme open source de questionnaires à choix multiples (MCQ) conçue pour être facile à héberger et à utiliser. Construite avec PHP et SQLite, elle offre une solution légère et robuste pour créer et administrer des questionnaires en ligne.

## ✨ Fonctionnalités

### 📋 Types de questions supportés
- **Choix unique** - Une seule réponse correcte
- **Choix multiples** - Plusieurs réponses correctes possibles
- **Questions ouvertes** - Réponses en texte libre (courtes et longues)
- **Support d'images** - Intégration d'images dans les questions

### 🎯 Gestion des questionnaires
- Format JSON simple et lisible pour définir les questionnaires
- Correction automatique pour les questions fermées
- Randomisation des questions et réponses
- Système de points personnalisable
- Affichage conditionnel des résultats
- Interface d'administration complète

### 🛡️ Protection anti-triche avancée
Libre MCQ intègre plusieurs mécanismes pour empêcher la triche :

- **Détection de changement d'onglet/fenêtre** - Surveillance des tentatives de navigation
- **Blocage du copier-coller** - Empêche la copie de questions vers des IA
- **Détection des outils de développement** - Alerte en cas d'ouverture de la console
- **Protection contre l'IA** - Mécanismes spécifiques anti-ChatGPT et autres chatbots
- **Surveillance de la perte de focus** - Détection quand l'utilisateur quitte la page
- **Prévention du clic droit** - Altération du menu contextuel

### 👥 Administration
- Interface d'administration dédiée
- Gestion des sessions de questionnaires
- Visualisation des réponses
- Visualisation des alertes de triches enregistrées
- Contrôle de l'état des questionnaires (ouvert/fermé)

## 🚀 Installation

### Prérequis
- PHP 7.4 ou supérieur
- SQLite 3
- Serveur web (Apache, Nginx)

### Installation rapide

1. **Cloner le projet**
```bash
git clone https://github.com/votre-username/libre-mcq.git
cd libre-mcq
```

2. **Configuration du serveur web**
Pour Nginx, utilisez le fichier de configuration fourni et modifiez le nom de domaine :
```bash
cp confs/example.nginx.conf /etc/nginx/sites-available/libre-mcq.conf
ln -s /etc/nginx/sites-available/libre-mcq.conf /etc/nginx/sites-enabled/
```

Pour Apache assurez-vous que les fichiers .htaccess soient supportés et le module de ré-écriture d'URL activé.

3. **Initialiser la base de données**
```bash
sqlite3 data/database.sqlite < data/init_db.sql
```

4. **Configurer les permissions**
```bash
chown -R www-data:$user data/
chmod 755 data/
chmod 660 data/database.sqlite
```

5. **Modifier les identifiants admin**
```bash
htpasswd -c admin/.htpasswd admin
```

6. **Accéder à l'application**
Ouvrez votre navigateur et allez à `http://votre-domaine.local`


## 📝 Création d'un questionnaire

Les questionnaires sont définis au format JSON dans le dossier `data/`. Voici la structure :

### Structure de base
```json
{
    "title": "Titre du questionnaire",
    "description": "Description du questionnaire",
    "show_results": true,
    "randomize": true,
    "questions": [...]
}
```

### Exemples de questions

#### Choix unique
```json
{
    "id": 1,
    "type": "single_choice",
    "question": "Quelle est la capitale de la France ?",
    "options": {
        "a": "Londres",
        "b": "Berlin",
        "c": "Paris",
        "d": "Madrid"
    },
    "correct_answers": ["c"],
    "points": 2
}
```

#### Choix multiples
```json
{
    "id": 2,
    "type": "multiple_choice", 
    "question": "Quels sont des langages de programmation ?",
    "options": {
        "a": "Python",
        "b": "HTML",
        "c": "JavaScript", 
        "d": "CSS"
    },
    "correct_answers": ["a", "c"],
    "points": 3
}
```

#### Question ouverte
```json
{
    "id": 3,
    "type": "open",
    "question": "Expliquez le concept de programmation orientée objet.",
    "placeholder": "Tapez votre réponse ici...",
    "points": 5,
    "answer": "Réponse de référence (optionnelle)"
}
```

`answer` is optionnal and will be used to show an example of correct response to the student 

#### Avec image
```json
{
    "id": 4,
    "type": "single_choice",
    "question": "Que représente cette image ?",
    "images": [
        "https://example.com/image.jpg",
        "/assets/local-image.png"
    ],
    "options": {
        "a": "Option A",
        "b": "Option B"
    },
    "correct_answers": ["a"],
    "points": 2
}
```

## 📁 Structure du projet

```
libre-mcq/
├── admin/                 # Interface d'administration
├── assets/               # Ressources statiques (CSS, JS, images)
├── confs/                # Fichiers de configuration
├── data/                 # Base de données et questionnaires
│   ├── database.sqlite   # Base de données SQLite
│   ├── init_db.sql      # Script d'initialisation
│   └── example/         # Exemple de questionnaire
├── incs/                 # Fichiers PHP inclus
├── templates/            # Templates PHP
├── index.php            # Page d'accueil
├── mcq.php              # Affichage des questionnaires
├── answer.php           # Traitement des réponses
└── README.md            # Ce fichier
```

## 🔧 Configuration

### Gestion des questionnaires

1. **Créer un questionnaire** : Créez un dossier dans `data/` avec un fichier `mcq.json`
2. **Activer/désactiver** : Créez/supprimez le fichier `status.txt` contenant "open" ou "closed"
3. **Protection IA** : Ajoutez un fichier `ai_protect.txt` pour activer les protections avancées `on` pour activer `off` pour désactiver.

### Interface d'administration

Accédez à `/admin/` pour :
- Voir les sessions actives
- Consulter les résultats
- Gérer l'état des questionnaires
- Voir les alertes de triche

## 🛡️ Sécurité

Libre MCQ implémente plusieurs couches de sécurité :

- **Protection CSRF** pour toutes les soumissions de formulaires
- **Validation côté serveur** de toutes les données
- **Échappement HTML** pour prévenir les attaques XSS
- **Système de sessions sécurisé**
- **Monitoring anti-triche en temps réel**

## 🤝 Contribution

Libre MCQ est un logiciel libre sous licence GNU GPL v3. Les contributions sont les bienvenues !

### Comment contribuer

1. Fork le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Committez vos changements (`git commit -am 'Ajout d'une nouvelle fonctionnalité'`)
4. Poussez vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Ouvrez une Pull Request


## 📄 Licence

Ce projet est sous licence GNU General Public License v3.0. Voir le fichier `LICENSE` pour plus de détails.


---

**Libre MCQ** - Une plateforme MCQ libre, sécurisée et facile à utiliser. 🚀
