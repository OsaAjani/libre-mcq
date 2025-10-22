-- Schéma de base de données pour Open MCQ
-- Base SQLite pour stocker les réponses et résultats des MCQ

-- Table pour stocker les sessions de MCQ
CREATE TABLE IF NOT EXISTS sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    mcq_id VARCHAR(255) NOT NULL,
    student_name VARCHAR(255),
    start_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    end_time DATETIME,
    total_score INTEGER DEFAULT 0,
    max_score INTEGER DEFAULT 0,
    percentage DECIMAL(5,2) DEFAULT 0.00,
    status VARCHAR(50) DEFAULT 'in_progress' -- 'in_progress', 'completed'
);

-- Table pour stocker les réponses individuelles
CREATE TABLE IF NOT EXISTS answers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id INTEGER NOT NULL,
    question_id VARCHAR(255) NOT NULL,
    question_type VARCHAR(50) NOT NULL, -- 'single_choice', 'multiple_choice', 'open', 'text'
    student_answer TEXT, -- Stockage JSON pour les réponses multiples ou texte simple
    correct_answer TEXT, -- Stockage JSON des bonnes réponses
    is_correct BOOLEAN DEFAULT 0,
    points_earned INTEGER DEFAULT 0,
    max_points INTEGER DEFAULT 0,
    correction_needed BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE
);

-- Index pour améliorer les performances
CREATE INDEX IF NOT EXISTS idx_sessions_mcq_id ON sessions(mcq_id);
CREATE INDEX IF NOT EXISTS idx_sessions_student ON sessions(student_name);
CREATE INDEX IF NOT EXISTS idx_answers_session ON answers(session_id);
CREATE INDEX IF NOT EXISTS idx_answers_question ON answers(question_id);
