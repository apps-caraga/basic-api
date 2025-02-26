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
    
      
      -- Table: role_permissions
      
    CREATE TABLE IF NOT EXISTS role_permissions (
        id            INTEGER PRIMARY KEY AUTOINCREMENT  NOT NULL,
        role_id       INTEGER REFERENCES roles (id) ON DELETE CASCADE  NOT NULL,
        permission_id INTEGER REFERENCES permissions (id) ON DELETE CASCADE  NOT NULL,
        created_at    INTEGER NOT NULL  DEFAULT (unixepoch() ) 
    );


 

    -- Table: permissions

    CREATE TABLE IF NOT EXISTS permissions (
        id         INTEGER PRIMARY KEY AUTOINCREMENT  NOT NULL,
        permission TEXT    UNIQUE  NOT NULL,
        created_at INTEGER NOT NULL  DEFAULT (unixepoch() ) 
    );
    
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
        SELECT 
        u.id AS id,
        password,
        u.username,
        u.role_id,
        is_admin,
        r.role,
        COALESCE(GROUP_CONCAT(p.permission, ', '), 'No Permissions') AS permissions,
        datetime('now','localtime') as login_time
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    LEFT JOIN role_permissions rp ON r.id = rp.role_id
    LEFT JOIN permissions p ON rp.permission_id = p.id
    WHERE u.role_id IS NOT NULL
    GROUP BY u.id, u.username, u.role_id, r.role
        

    COMMIT  TRANSACTION;
    PRAGMA foreign_keys = on;



**Activating Users**:
New users cannot login by default. You need to set the `role_id` (via a database browser) as this project has no front-end. 
 




