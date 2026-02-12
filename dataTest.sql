-- Fichier dataTest.sql - Comptes utilisateurs de test

-- User standard
INSERT INTO users (email, password_hash, display_name, roles, created_at) VALUES 
('test.user@example.com', '$2y$13$QHbGAZnMo5XYDUFL3jqACe4wcPD3RvW61ByMtA81D3o5i9ekZz7N.', 'Utilisateur Test', '["ROLE_USER"]', NOW());

-- Admin
INSERT INTO users (email, password_hash, display_name, roles, created_at) VALUES 
('admin1@sp-lifting.com', '$2y$13$FTtV41RqQXcvVfcXR5zjMurH7BU7ILDZTM481KYkNqwWMJCergLSm', 'Administrateur1', '["ROLE_ADMIN"]', NOW());