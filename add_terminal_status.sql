-- Add status column to route_terminals table
ALTER TABLE route_terminals 
ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' NOT NULL;

-- Add status column to transport_route table
ALTER TABLE transport_route 
ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' NOT NULL;

-- Set all existing records to active
UPDATE route_terminals SET status = 'active' WHERE status IS NULL;
UPDATE transport_route SET status = 'active' WHERE status IS NULL;
