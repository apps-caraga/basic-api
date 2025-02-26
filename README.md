# Super basic Php-CRUD-API sample

Middleware used:
- dbAuth
- authorization
- multiTenancy

**Database schema**   
    
    
    PRAGMA foreign_keys = off;
    
    BEGIN  TRANSACTION;
    
      
    
    -- Table: reports
    
    CREATE  TABLE IF NOT  EXISTS reports (
    id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
    title TEXT  NOT NULL,
    content TEXT,
    created_at INTEGER  DEFAULT (unixepoch() ) NOT NULL,
    created_by INTEGER  REFERENCES users (id)
    
    );
    
      
      
    
    -- Table: roles
    
    CREATE  TABLE IF NOT  EXISTS roles (
    id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
    role  TEXT  NOT NULL UNIQUE,
    created_at INTEGER  DEFAULT (unixepoch() )NOT NULL);
    
      
      
    
    -- Table: users
    
    CREATE  TABLE IF NOT  EXISTS users (
    id INTEGER  PRIMARY KEY AUTOINCREMENTNOT NULL UNIQUE,
    username TEXT  UNIQUE NOT NULL,
    password  TEXT  NOT NULL,
    role_id INTEGER  REFERENCES roles (id),
    is_admin   INTEGER CONSTRAINT ZERO_OR_ONE_ONLY CHECK (is_admin IN (1, 0) )  DEFAULT (0),
    created_at INTEGER  DEFAULT (unixepoch() )
    NOT NULL);
    
      
      
    
    -- View: active_users
    
    CREATE  VIEW IF NOT  EXISTS active_users AS
    SELECT u.id,
    u.username,
    u.password,
    role,is_admin,
    datetime('now', 'localtime') AS login_time
    FROM users u
    LEFT JOIN
    roles r ON r.id = u.role_id
    WHERE u.role_id IS NOT NULL;
    

    COMMIT  TRANSACTION;
    PRAGMA foreign_keys = on;



**Activating Users**:
New users cannot login by default. You need to set the `role_id` (via a database browser) as this project has no front-end. 
 




