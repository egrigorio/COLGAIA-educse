-- Esquema reconstruído a partir das consultas e metadados PHP.
-- Instalar apenas num banco vazio. Não contém dados históricos.
-- Hipóteses e limitações: docs/banco-de-dados.md.
SET NAMES utf8mb4;

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    password VARCHAR(255) NOT NULL,
    cargo VARCHAR(40) NOT NULL DEFAULT 'aluno',
    pfp VARCHAR(255) NOT NULL DEFAULT 'e.png',
    ultimo_login DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    editado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT NOT NULL DEFAULT 1,
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE instituicao (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    id_dono INT NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    editado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT NOT NULL DEFAULT 1,
    KEY idx_instituicao_dono (id_dono)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE curso (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome_curso VARCHAR(255) NOT NULL,
    abreviatura VARCHAR(40) NOT NULL,
    duracao INT NOT NULL DEFAULT 3,
    id_diretor_curso INT NOT NULL DEFAULT -1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    editado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT NOT NULL DEFAULT 1,
    KEY idx_curso_diretor (id_diretor_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE esforco (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    limite DECIMAL(8,2) NOT NULL DEFAULT 4,
    barreira DECIMAL(8,2) NOT NULL DEFAULT 3,
    dia_0 TINYINT NOT NULL DEFAULT 1,
    dia_1 TINYINT NOT NULL DEFAULT 1,
    dia_2 TINYINT NOT NULL DEFAULT 1,
    dia_3 TINYINT NOT NULL DEFAULT 1,
    dia_4 TINYINT NOT NULL DEFAULT 1,
    dia_5 TINYINT NOT NULL DEFAULT 0,
    dia_6 TINYINT NOT NULL DEFAULT 0,
    ativo TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE turma (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome_turma VARCHAR(100) NOT NULL,
    id_curso INT NOT NULL,
    ano_letivo VARCHAR(7) NOT NULL,
    ano INT NOT NULL DEFAULT 1,
    id_esforco INT NOT NULL,
    id_diretor_turma INT NOT NULL DEFAULT -1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    editado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT NOT NULL DEFAULT 1,
    KEY idx_turma_curso_ano (id_curso, ano_letivo),
    KEY idx_turma_diretor (id_diretor_turma)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE disciplinas (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    abreviatura VARCHAR(40) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    editado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE turno (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    id_turma INT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    editado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT NOT NULL DEFAULT 1,
    UNIQUE KEY uq_turno_numero (numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE eventos (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    comeco DATETIME NOT NULL,
    fim DATETIME NOT NULL,
    barreira DECIMAL(8,2) NOT NULL DEFAULT 0,
    ativo TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE atividades (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_evento INT NOT NULL,
    descricao TEXT NOT NULL,
    tipo VARCHAR(100) NOT NULL DEFAULT '',
    id_professor INT NOT NULL,
    tempo_sugerido DECIMAL(8,2) NOT NULL,
    id_disciplina INT NOT NULL,
    id_turno INT NULL DEFAULT -1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    editado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT NOT NULL DEFAULT 1,
    KEY idx_atividade_evento (id_evento),
    KEY idx_atividade_professor (id_professor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE eventos_alunos_calendario (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    `start` DATETIME NOT NULL,
    `end` DATETIME NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    backgroundColor VARCHAR(40) NOT NULL DEFAULT '#3788d8',
    KEY idx_calendario_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE conf_convite (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL,
    id_curso INT NOT NULL,
    cargo VARCHAR(40) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_convite_curso (id_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE logs (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dataHora DATETIME NOT NULL,
    url TEXT NOT NULL,
    ip VARCHAR(45) NOT NULL DEFAULT '',
    idUser INT NOT NULL DEFAULT 0,
    session VARCHAR(128) NOT NULL DEFAULT '',
    tipoLog TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_user_curso (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_curso INT NOT NULL,
    estado VARCHAR(40) NOT NULL DEFAULT '1',
    KEY idx_id_user (id_user),
    KEY idx_id_curso (id_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_turma_user (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_turma INT NOT NULL,
    id_user INT NOT NULL,
    ativo TINYINT NOT NULL DEFAULT 1,
    KEY idx_id_turma (id_turma),
    KEY idx_id_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_turno_user (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_turno INT NOT NULL,
    id_rel_turma_user INT NOT NULL,
    KEY idx_id_turno (id_turno),
    KEY idx_id_rel_turma_user (id_rel_turma_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_instituicao_curso (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_instituicao INT NOT NULL,
    id_curso INT NOT NULL,
    KEY idx_id_instituicao (id_instituicao),
    KEY idx_id_curso (id_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_instituicao_disciplinas (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_instituicao INT NOT NULL,
    id_disc INT NOT NULL,
    KEY idx_id_instituicao (id_instituicao),
    KEY idx_id_disc (id_disc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_disciplina_curso (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_disciplina INT NOT NULL,
    id_curso INT NOT NULL,
    KEY idx_id_disciplina (id_disciplina),
    KEY idx_id_curso (id_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_disciplina_curso_ano (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_disciplina INT NOT NULL,
    id_curso INT NOT NULL,
    ano INT NOT NULL,
    KEY idx_id_disciplina (id_disciplina),
    KEY idx_id_curso (id_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_disciplina_turma (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_disciplina INT NOT NULL,
    id_turma INT NOT NULL,
    KEY idx_id_disciplina (id_disciplina),
    KEY idx_id_turma (id_turma)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_disciplina_user (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_disciplina INT NOT NULL,
    id_curso INT NOT NULL,
    cargo VARCHAR(40) NOT NULL DEFAULT 'professor',
    KEY idx_id_user (id_user),
    KEY idx_id_disciplina (id_disciplina),
    KEY idx_id_curso (id_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rel_atividades_turma (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_atividade INT NOT NULL,
    id_turma INT NOT NULL,
    KEY idx_id_atividade (id_atividade),
    KEY idx_id_turma (id_turma)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE VIEW view_user_curso AS
SELECT u.id AS id_user, u.username, u.email, u.cargo, u.pfp, u.ultimo_login,
       r.id_curso, r.estado, c.nome_curso, c.id_diretor_curso
FROM users u
JOIN rel_user_curso r ON r.id_user = u.id
JOIN curso c ON c.id = r.id_curso;

CREATE VIEW view_user_turma_turno AS
SELECT u.id AS id_user, u.username, u.email, u.cargo, u.pfp,
       r.id_turma, r.id AS id_rel_turma_user, r.ativo,
       COALESCE(rt.id_turno, -1) AS id_turno, COALESCE(t.numero, 0) AS num_turno
FROM users u
JOIN rel_turma_user r ON r.id_user = u.id
LEFT JOIN rel_turno_user rt ON rt.id_rel_turma_user = r.id
LEFT JOIN turno t ON t.id = rt.id_turno;

CREATE VIEW view_turno_turma AS
SELECT DISTINCT r.id_turma, t.id AS id_turno, t.numero, t.ativo
FROM rel_turma_user r
JOIN rel_turno_user rt ON rt.id_rel_turma_user = r.id
JOIN turno t ON t.id = rt.id_turno
WHERE r.ativo = 1 AND t.ativo = 1;

CREATE VIEW view_professor_turno_turma AS
SELECT id_user, username, email, cargo, pfp, id_turma, id_turno, num_turno
FROM view_user_turma_turno
WHERE cargo = 'professor' AND ativo = 1;
