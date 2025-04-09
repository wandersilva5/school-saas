<?php

return [
    'up' => "ALTER TABLE institutions 
            ADD COLUMN bank_name VARCHAR(100) NULL,
            ADD COLUMN bank_agency VARCHAR(20) NULL,
            ADD COLUMN bank_account VARCHAR(20) NULL,
            ADD COLUMN bank_account_type VARCHAR(20) NULL,
            ADD COLUMN bank_cnpj VARCHAR(20) NULL;",
            
    'down' => "ALTER TABLE institutions 
              DROP COLUMN bank_name,
              DROP COLUMN bank_agency,
              DROP COLUMN bank_account,
              DROP COLUMN bank_account_type,
              DROP COLUMN bank_cnpj;"
];
