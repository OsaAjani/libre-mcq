# Libre MCQ 📝

Libre MCQ is an open-source multiple-choice questionnaire (MCQ) platform designed to be easy to host and use. Built with PHP and SQLite, it offers a lightweight and robust solution for creating and managing online quizzes.

## ✨ Features

### 📋 Supported Question Types
- **Single Choice** - One correct answer
- **Multiple Choice** - Multiple correct answers possible
- **Open Questions** - Free text responses (short and long)
- **Image Support** - Integration of images in questions

### 🎯 Questionnaire Management
- Simple and readable JSON format for defining quizzes
- Automatic grading for closed questions
- Randomization of questions and answers
- Customizable scoring system
- Conditional display of results
- Full administration interface

### 🛡️ Advanced Anti-Cheating Protection
Libre MCQ includes several mechanisms to prevent cheating:

- **Tab/Window Change Detection** - Monitors navigation attempts
- **Copy-Paste Blocking** - Prevents copying questions to AI tools
- **Developer Tools Detection** - Alerts when the console is opened
- **AI Protection** - Specific mechanisms against ChatGPT and other chatbots
- **Focus Loss Monitoring** - Detects when the user leaves the page
- **Right-Click Prevention** - Alters the context menu

### 👥 Administration
- Dedicated administration interface
- Management of quiz sessions
- Viewing responses
- Viewing recorded cheating alerts
- Control of quiz status (open/closed)
- One-click grading for open questions

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- SQLite 3
- Web server (Apache, Nginx)

### Quick Installation

1. **Clone the project**
```bash
git clone https://github.com/your-username/libre-mcq.git
cd libre-mcq
```

2. **Configure the web server**

For Nginx, use the provided configuration file and modify the domain name:
```bash
cp confs/example.nginx.conf /etc/nginx/sites-available/libre-mcq.conf
ln -s /etc/nginx/sites-available/libre-mcq.conf /etc/nginx/sites-enabled/
```

For Apache, ensure `.htaccess` files are supported and the URL rewrite module is enabled.

3. **Initialize the database**
```bash
sqlite3 data/database.sqlite < data/init_db.sql
```

4. **Set permissions**
```bash
chown -R www-data:$user data/
chmod 755 data/
chmod 660 data/database.sqlite
```

5. **Update admin credentials**
```bash
htpasswd -c admin/.htpasswd admin
```

## 📝 Creating a Quiz

Quizzes are defined in JSON format in the `data/` folder. Here is the structure:

### Basic Structure
```json
{
    "title": "Quiz Title",
    "description": "Quiz Description",
    "show_results": true,
    "randomize": true,
    "questions": [...]
}
```

### Question Examples

#### Single Choice
```json
{
    "id": 1,
    "type": "single_choice",
    "question": "What is the capital of France?",
    "options": {
        "a": "London",
        "b": "Berlin",
        "c": "Paris",
        "d": "Madrid"
    },
    "correct_answers": ["c"],
    "points": 2
}
```

#### Multiple Choice
```json
{
    "id": 2,
    "type": "multiple_choice", 
    "question": "Which of these are programming languages?",
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

#### Open Question
```json
{
    "id": 3,
    "type": "open",
    "question": "Explain the concept of object-oriented programming.",
    "placeholder": "Type your answer here...",
    "points": 5,
    "answer": "Reference answer (optional)"
}
```

`answer` is optional and will be used to show an example of a correct response to the student.

#### With Image
```json
{
    "id": 4,
    "type": "single_choice",
    "question": "What does this image represent?",
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

## 📁 Project Structure

```
libre-mcq/
├── admin/                 # Administration interface
├── assets/               # Static resources (CSS, JS, images)
├── confs/                # Configuration files
├── data/                 # Database and quizzes
│   ├── database.sqlite   # SQLite database
│   ├── init_db.sql      # Initialization script
│   └── example/         # Example quiz
├── incs/                 # Included PHP files
├── templates/            # PHP templates
├── index.php            # Homepage
├── mcq.php              # Quiz display
├── answer.php           # Response processing
└── README.md            # This file
```

## 🔧 Configuration

### Managing Quizzes

1. **Create a quiz**: Create a folder in `data/` with a `mcq.json` file.
2. **Enable/Disable**: Create/delete the `status.txt` file containing "open" or "closed".
3. **AI Protection**: Add an `ai_protect.txt` file to enable advanced protections. Use `on` to enable, `off` to disable.

### Administration Interface

Access `/admin/` to:
- View active sessions
- Review results
- Manage quiz status
- View cheating alerts

## 🛡️ Security

Libre MCQ implements multiple layers of security:

- **CSRF Protection** for all form submissions
- **Server-side Validation** of all data
- **HTML Escaping** to prevent XSS attacks
- **Secure Session Management**
- **Real-time Anti-Cheating Monitoring**

## 🤝 Contribution

Libre MCQ is free software under the GNU GPL v3 license. Contributions are welcome!

### How to Contribute

1. Fork the project.
2. Create a branch for your feature (`git checkout -b feature/new-feature`).
3. Commit your changes (`git commit -am 'Add a new feature'`).
4. Push to the branch (`git push origin feature/new-feature`).
5. Open a Pull Request.

## 📄 License

This project is licensed under the GNU General Public License v3.0. See the `LICENSE` file for details.

---

**Libre MCQ** - A free, secure, and easy-to-use MCQ platform. 🚀
