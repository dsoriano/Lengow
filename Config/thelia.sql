
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- lengow_exclude_category
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `lengow_exclude_category`;

CREATE TABLE `lengow_exclude_category`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `category_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `lengow_exclude_category_U_1` (`category_id`),
    CONSTRAINT `fk_category_id`
        FOREIGN KEY (`category_id`)
        REFERENCES `category` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- lengow_exclude_brand
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `lengow_exclude_brand`;

CREATE TABLE `lengow_exclude_brand`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `brand_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `lengow_exclude_brand_U_1` (`brand_id`),
    CONSTRAINT `fk_brand_id`
        FOREIGN KEY (`brand_id`)
        REFERENCES `brand` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- lengow_exclude_product
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `lengow_exclude_product`;

CREATE TABLE `lengow_exclude_product`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `product_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `lengow_exclude_product_U_1` (`product_id`),
    CONSTRAINT `fk_product_id`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- lengow_include_attribute
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `lengow_include_attribute`;

CREATE TABLE `lengow_include_attribute`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `attribute_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `lengow_include_attribute_U_1` (`attribute_id`),
    CONSTRAINT `fk_attribute_id`
        FOREIGN KEY (`attribute_id`)
        REFERENCES `attribute` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
