-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- Procédure -----------------
-- ------------------------------

-- procédure qui met a jours les séquences des tables afin d'éviter des pb suite a des ajouts fait en sql
create or replace procedure update_sequences()
	language plpgsql
as
$$
DECLARE
	req varchar(300);
BEGIN
	for req in (SELECT 'SELECT SETVAL(' ||
		                   quote_literal(quote_ident(sequence_namespace.nspname) || '.' ||
			                   quote_ident(class_sequence.relname)) ||
		                   ', COALESCE(MAX(' || quote_ident(pg_attribute.attname) || '), 1) ) FROM ' ||
		                   quote_ident(table_namespace.nspname) || '.' || quote_ident(class_table.relname) || ';'
	            FROM pg_depend
		                 INNER JOIN pg_class AS class_sequence
		                 ON class_sequence.oid = pg_depend.objid
				                 AND class_sequence.relkind = 'S'
		                 INNER JOIN pg_class AS class_table
		                 ON class_table.oid = pg_depend.refobjid
		                 INNER JOIN pg_attribute
		                 ON pg_attribute.attrelid = class_table.oid
				                 AND pg_depend.refobjsubid = pg_attribute.attnum
		                 INNER JOIN pg_namespace as table_namespace
		                 ON table_namespace.oid = class_table.relnamespace
		                 INNER JOIN pg_namespace AS sequence_namespace
		                 ON sequence_namespace.oid = class_sequence.relnamespace
	            ORDER BY sequence_namespace.nspname, class_sequence.relname)
		loop
			EXECUTE req;
		end loop;
end;
$$;

-- call update_sequences();