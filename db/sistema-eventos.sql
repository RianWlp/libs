CREATE USER eventos WITH ENCRYPTED PASSWORD 'eventos';
CREATE DATABASE eventos
    WITH OWNER = eventos
    ENCODING = 'UTF8'
    LC_COLLATE = 'pt_BR.UTF-8'
    LC_CTYPE = 'pt_BR.UTF-8'
    TEMPLATE = template0
;

CREATE TABLE usuarios (
    id              SERIAL PRIMARY KEY,
    nome            TEXT NOT NULL,
    username        TEXT NOT NULL,
    email           TEXT NOT NULL,
    senha           TEXT NOT NULL,
    cpf             VARCHAR(14),
    data_nascimento DATE,
    -- token           TEXT NOT NULL,
    token           TEXT,
    dt_criacao      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dt_atualizado   TIMESTAMP,
    dt_deletado     TIMESTAMP
);

INSERT INTO usuarios (
    id,
    nome,
    username,
    email,
    senha,
    cpf,
    data_nascimento,
    token,
    dt_criacao,
    dt_atualizado,
    dt_deletado
) VALUES (
    1,                                -- id
    'João Silva',                     -- nome
    'joaosilva',                      -- username
    'joaosilva@example.com',          -- email
    'hashed_senha_aqui',              -- senha (coloque o hash da senha)
    '123.456.789-00',                 -- cpf
    '1990-05-15',                     -- data_nascimento
    'token_aqui',                     -- token (pode ser um token JWT ou qualquer outro)
    CURRENT_TIMESTAMP,                -- dt_criacao
    CURRENT_TIMESTAMP,                -- dt_atualizado
    NULL                              -- dt_deletado (deixe como NULL se o usuário não foi deletado)
);

CREATE TABLE eventos (
    id                SERIAL PRIMARY KEY,
    nome              TEXT NOT NULL,
    descricao         TEXT,
    data_inicio       TIMESTAMP NOT NULL,
    data_fim          TIMESTAMP NOT NULL,
    capacidade_maxima INT,
    dt_criacao        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dt_atualizado    TIMESTAMP,
    dt_deletado       TIMESTAMP
);

CREATE TABLE inscricoes (
    id             SERIAL PRIMARY KEY,
    fk_usuario     INT NOT NULL,
    fk_evento      INT NOT NULL,
    dt_inscricao   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dt_atualizado TIMESTAMP,
    dt_deletado    TIMESTAMP,
    FOREIGN KEY (fk_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (fk_evento) REFERENCES eventos(id)
);

CREATE TABLE checkin (
    id                  SERIAL PRIMARY KEY,
    fk_usuario          INT NOT NULL,
    fk_evento           INT NOT NULL,
    presenca_confirmada BOOLEAN DEFAULT FALSE,
    dt_checkin          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dt_atualizado      TIMESTAMP,
    dt_deletado         TIMESTAMP,
    FOREIGN KEY (fk_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (fk_evento) REFERENCES eventos(id)
);

CREATE TABLE certificados (
    id                  SERIAL PRIMARY KEY,
    fk_usuario          INT NOT NULL,
    fk_evento           INT NOT NULL,
    url_validacao       TEXT,
    codigo_autenticacao VARCHAR(50) NOT NULL UNIQUE,
    dt_emissao          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dt_atualizado      TIMESTAMP,
    dt_deletado         TIMESTAMP,
    FOREIGN KEY (fk_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (fk_evento) REFERENCES eventos(id)
);

CREATE TABLE logs (
    id               BIGSERIAL PRIMARY KEY,
    fk_usuario       INT,
    endpoint         TEXT NOT NULL,
    metodo           TEXT NOT NULL,
    dados_requisicao TEXT,
    dados_resposta   TEXT,
    status_http      INT NOT NULL,
    dt_requisicao    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_origem        VARCHAR(45)
);
