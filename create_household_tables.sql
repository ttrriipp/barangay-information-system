-- Add household_id column to the residents table if it doesn't exist
ALTER TABLE residents ADD COLUMN IF NOT EXISTS household_id INT NULL;

-- Create the households table
CREATE TABLE IF NOT EXISTS households (
    id INT PRIMARY KEY AUTO_INCREMENT,
    head_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_head FOREIGN KEY (head_id) REFERENCES residents(id) ON DELETE RESTRICT
);

-- Create the household_members table
CREATE TABLE IF NOT EXISTS household_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    household_id INT NOT NULL,
    resident_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_household FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE,
    CONSTRAINT fk_resident FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
);

-- Add foreign key constraint to residents table
ALTER TABLE residents ADD CONSTRAINT fk_household_id FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE SET NULL;

-- Create an index for faster lookups
CREATE INDEX idx_household_id ON residents(household_id);
CREATE INDEX idx_household_member ON household_members(household_id, resident_id); 