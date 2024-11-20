CREATE USER eventos WITH ENCRYPTED PASSWORD 'eventos';
CREATE DATABASE eventos
    WITH OWNER = eventos
    ENCODING = 'UTF8'
    LC_COLLATE = 'pt_BR.UTF-8'
    LC_CTYPE = 'pt_BR.UTF-8'
    TEMPLATE = template0
;

DROP TABLE inscricoes; DROP TABLE checkin; DROP TABLE certificados; DROP TABLE eventos; DROP TABLE logs; DROP TABLE usuarios;

CREATE TABLE usuarios (
    id              SERIAL PRIMARY KEY,
    nome            TEXT NOT NULL,
    username        TEXT NOT NULL,
    email           TEXT NOT NULL,
    senha           TEXT NOT NULL,
    cpf             VARCHAR(14),
    dt_nascimento    DATE,
    -- token           TEXT NOT NULL,
    token           TEXT,
    dt_criacao      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dt_atualizado   TIMESTAMP,
    dt_deletado     TIMESTAMP
);

CREATE TABLE eventos (
    id                SERIAL PRIMARY KEY,
    nome              TEXT NOT NULL,
    descricao         TEXT,
    dt_inicio         TIMESTAMP NOT NULL,
    dt_fim            TIMESTAMP NOT NULL,
    capacidade_maxima INT,
    dt_criacao        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dt_atualizado     TIMESTAMP,
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

INSERT INTO usuarios (nome, username, email, senha, cpf, dt_nascimento)

VALUES
('João Silva', 'joaosilva', 'joaosilva@example.com', 'senha123', '123.456.789-00', '1990-05-12'),
('Maria Oliveira', 'mariaol', 'mariaol@example.com', 'senha456', '987.654.321-00', '1985-03-22'),
('Carlos Pereira', 'carlospereira', 'carlospereira@example.com', 'senha789', '321.654.987-00', '1992-08-14'),
('Ana Costa', 'anacosta', 'anacosta@example.com', 'senha321', '456.789.123-00', '1988-11-30'),
('Ricardo Souza', 'ricardosouza', 'ricardosouza@example.com', 'senha654', '789.123.456-00', '1982-01-05'),
('Fernanda Lima', 'fernandalima', 'fernandalima@example.com', 'senha987', '654.321.789-00', '1995-07-28'),
('Juliana Martins', 'julianamartins', 'julianamartins@example.com', 'senha112', '321.987.654-00', '1993-12-18'),
('Marcos Pereira', 'marcospereira', 'marcospereira@example.com', 'senha223', '111.222.333-44', '1987-10-09'),
('Patrícia Rocha', 'patriciarocha', 'patriciarocha@example.com', 'senha334', '444.555.666-77', '1984-09-15'),
('Lucas Almeida', 'lucasalmeida', 'lucasalmeida@example.com', 'senha445', '222.333.444-55', '1996-02-21');


INSERT INTO eventos (nome, descricao, dt_inicio, dt_fim, capacidade_maxima)
VALUES
('Workshop de Inteligência Artificial', 'Um workshop sobre os fundamentos de IA e Machine Learning.', '2024-12-01 09:00:00', '2024-12-01 17:00:00', 100),
('Seminário de Tecnologia', 'Discussões sobre tendências tecnológicas no mercado.', '2024-12-05 10:00:00', '2024-12-05 15:00:00', 200),
('Hackathon 2024', 'Maratona de programação com premiação para as melhores soluções.', '2024-12-10 08:00:00', '2024-12-11 20:00:00', 50),
('Congresso de Ciências', 'Evento acadêmico para apresentação de pesquisas científicas.', '2025-01-15 09:00:00', '2025-01-17 18:00:00', 300),
('Palestra de Empreendedorismo', 'Inspirando novos empreendedores com histórias de sucesso.', '2024-11-20 14:00:00', '2024-11-20 16:00:00', 150),
('Curso de Desenvolvimento Web', 'Curso introdutório de HTML, CSS e JavaScript.', '2024-12-03 09:00:00', '2024-12-03 17:00:00', 75),
('Oficina de Design Gráfico', 'Aprenda os fundamentos do design e ferramentas gráficas.', '2024-12-07 10:00:00', '2024-12-07 16:00:00', 40),
('Encontro de Robótica', 'Demonstrações e palestras sobre robótica e automação.', '2024-11-25 08:00:00', '2024-11-25 14:00:00', 60);


INSERT INTO inscricoes (fk_usuario, fk_evento)
VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 3),
(1, 4),
(2, 4),
(3, 5),
(4, 6),
(5, 7),
(1, 8);

INSERT INTO checkin (fk_usuario, fk_evento, presenca_confirmada)
VALUES
(1, 1, TRUE),
(2, 1, FALSE),
(3, 2, TRUE),
(4, 3, FALSE),
(5, 3, TRUE),
(1, 4, FALSE),
(2, 4, TRUE),
(3, 5, FALSE),
(4, 6, TRUE),
(5, 6, FALSE);

INSERT INTO certificados (fk_usuario, fk_evento, url_validacao, codigo_autenticacao)
VALUES
(1, 1, 'http://validacao.example.com/certificado/1', 'C47F3B22-45DC-4A57-A6D3-34FB5A7E5F4A'),
(2, 1, 'http://validacao.example.com/certificado/2', '8E2F4D88-55C0-41D6-9A2E-5BE71F4C47A2'),
(3, 2, 'http://validacao.example.com/certificado/3', '56A7CC80-4C58-49A5-B1A3-8E6F3D88B8D4'),
(4, 3, 'http://validacao.example.com/certificado/4', '49F7C0AA-05ED-4B99-93FB-6B75F8B3BB49'),
(5, 3, 'http://validacao.example.com/certificado/5', 'B94E6D3C-5729-40F1-A315-2E915B5D6B07'),
(1, 4, 'http://validacao.example.com/certificado/6', 'E56F8091-01D6-4A6B-95AA-7459B5C63E9D'),
(2, 4, 'http://validacao.example.com/certificado/7', 'A6A32B7C-CF82-4A09-BC82-DA6FF5A5AE83'),
(3, 5, 'http://validacao.example.com/certificado/8', 'D7E87142-6E6E-4E39-A40E-B9B508D05125'),
(4, 6, 'http://validacao.example.com/certificado/9', '55AB8F59-2FF9-42A3-8A76-6C23DDFBCF70'),
(5, 6, 'http://validacao.example.com/certificado/10', 'A8909A90-40A9-4BCF-A6A2-65982B1BFAE0');
