

USE rentora_db;

  /*table countries*/
CREATE TABLE countries (
    id_country INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


/*table cities*/
CREATE TABLE cities (
    id_city INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    id_country INT NOT NULL,

    CONSTRAINT fk_city_country
    FOREIGN KEY (id_country)
    REFERENCES countries(id_country)
    ON DELETE CASCADE
);



/*table users*/
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    watssap VARCHAR(30),
    
    role ENUM('tenant', 'owner', 'admin') DEFAULT 'tenant',
    
    status ENUM('active', 'blocked') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



/*table properties*/
CREATE TABLE properties (
    id_property INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,
    
    discription TEXT NOT NULL,
    
    price DECIMAL(12,2) NOT NULL,
    
    rooms INT NOT NULL,
    
    bathrooms INT NOT NULL,
    
    type VARCHAR(100) NOT NULL,

    address TEXT NOT NULL,

    latitude DECIMAL(10,8) NULL,
    
    longitude DECIMAL(11,8) NULL,

    status ENUM('pending', 'approved', 'rejected')
    DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    id_user INT NOT NULL,
    id_city INT NOT NULL,
    id_currency INT NOT NULL,

    CONSTRAINT fk_property_user
    FOREIGN KEY (id_user)
    REFERENCES users(id_user)
    ON DELETE CASCADE,

    CONSTRAINT fk_property_city
    FOREIGN KEY (id_city)
    REFERENCES cities(id_city)
    ON DELETE CASCADE,

    CONSTRAINT fk_property_currency
    FOREIGN KEY (id_currency)
    REFERENCES currencies(id_currency)
    ON DELETE CASCADE
);


/*table images*/
CREATE TABLE images (
    id_image INT AUTO_INCREMENT PRIMARY KEY,

    image_path VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    id_property INT NOT NULL,

    CONSTRAINT fk_image_property
    FOREIGN KEY (id_property)
    REFERENCES properties(id_property)
    ON DELETE CASCADE
);


/*table messages*/
CREATE TABLE messages (
    id_message INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(150) NOT NULL,

    email VARCHAR(150) NOT NULL,

    subject VARCHAR(255) NOT NULL,

    message TEXT NOT NULL,

    status ENUM('unread', 'read')
    DEFAULT 'unread',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



/*table notifications*/
CREATE TABLE notifications (
    id_notification INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,

    message TEXT NOT NULL,

    is_read BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    id_user INT NOT NULL,

    CONSTRAINT fk_notification_user
    FOREIGN KEY (id_user)
    REFERENCES users(id_user)
    ON DELETE CASCADE
);


