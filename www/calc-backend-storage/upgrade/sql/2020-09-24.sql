CREATE TABLE calculation_results (
     id INT UNSIGNED AUTO_INCREMENT,
     token VARCHAR(56) NOT NULL,
     value DOUBLE SIGNED NOT NULL,
     created_at INT UNSIGNED NOT NULL,
     PRIMARY KEY(id),
     INDEX(token)
) ENGINE = InnoDB;
