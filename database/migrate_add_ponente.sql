-- Migration: Add PONENTE to participant type enum
-- Purpose: Allow "Ponente" as a valid participant type
-- Date: 2026-06-22

ALTER TABLE participants 
MODIFY COLUMN type ENUM('INTERNAL','EXTERNAL','PONENTE') NOT NULL DEFAULT 'INTERNAL';
