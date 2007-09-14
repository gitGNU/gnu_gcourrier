-- Track when a bill was _generated_. Defaults to the current
-- timestamp, no timestamp update.
ALTER TABLE `facture` ADD `dateSaisie` timestamp default CURRENT_TIMESTAMP;
